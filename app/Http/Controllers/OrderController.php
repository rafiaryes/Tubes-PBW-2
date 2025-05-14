<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{

    public function addItem(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menu,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = $request->user_id;
        $menuId = $request->menu_id;
        $quantity = $request->quantity;
        $orderMethod = $request->input('order_method');

        try {
            DB::beginTransaction();

            $order = Order::firstOrCreate(
                ['user_id' => $userId, 'status' => 'cart'],
                [
                    'total_price' => 0,
                    "order_method" => $orderMethod
                ]
            );

            // Ambil menu dan cek stok
            $menu = Menu::findOrFail($menuId);
            if ($menu->stok < $quantity) {
                throw new \Exception('Stok tidak mencukupi untuk item ini.');
            }

            // Tambahkan atau perbarui item di keranjang
            $orderItem = OrderItem::firstOrNew([
                'order_id' => $order->id,
                'menu_id' => $menuId,
            ]);

            if ($orderItem->exists) {
                $orderItem->quantity += $quantity;
            } else {
                $orderItem->quantity = $quantity;
            }

            $orderItem->price = $orderItem->quantity * $menu->price;
            $orderItem->save();

            // Kurangi stok
            $menu->stok -= $quantity;
            $menu->save();

            // Perbarui total harga order
            $order->total_price = OrderItem::where('order_id', $order->id)->sum('price');
            $order->save();

            DB::commit();
            return response()->json(['message' => 'Item berhasil ditambahkan ke keranjang'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
        }
    }

    public function makeOrder(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'payment_method' => 'required|in:pay_in_casheer,pay_online',
            'order_method' => 'required|in:dine_in,takeaway',
            'name' => 'required|string|max:255',
            // 'email' => 'required|email',
            'nophone' => 'required|string|max:20',
            'no_meja' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'code' => 422,
                'message' => 'Mohon lengkapi semua data!',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $userId = $request->input('user_id');
            $paymentMethod = $request->input('payment_method');
            $orderMethod = $request->input('order_method');
            $name = $request->name;
            // $email = $request->email;
            $noPhone = $request->nophone;
            $noMeja = $request->no_meja;

            $order = Order::where('user_id', $userId)
                ->where('status', 'cart')
                ->first();

            if (!$order) {
                return response()->json([
                    'error' => true,
                    'code' => 404,
                    'message' => 'Order Active tidak ditemukan.',
                ], 404);
            }

            $order->name = $name;
            // $order->email = $email;
            $order->payment_method = $paymentMethod;
            $order->order_method = $orderMethod;
            $order->nophone = $noPhone;
            $order->no_meja = $noMeja;
            $order->status = 'waiting_for_payment';
            // total price
            $order->total_price = $order->items->sum('price');
            $order->save();

            $data = [
                'order_id' => $order->id,
            ];

            if ($paymentMethod == 'pay_online') {
                $midtransService = new \App\Services\MidtransService();
                $qrisResponse = $midtransService->createQrisPayment($order);
                $payment = new Payment();
                $payment->order_id = $order->id;
                $payment->snap_token = $qrisResponse->transaction_id ?? null;
                $payment->qr_url = $qrisResponse->actions[0]->url;
                $payment->qr_string = $qrisResponse->qr_string;
                $payment->status = 'pending';
                $payment->expired_at = now()->addMinutes(30);
                $payment->save();

                $data['redirect_url'] = route('user.payment.qris', ['order_id' => $order->id]);
            }

            DB::commit();

            $message = $paymentMethod == 'pay_online' ? 'Silahkan lakukan pembayaran dengan QRIS' : 'Silahkan lakukan pembayaran di kasir kami';

            return response()->json([
                'error' => false,
                'code' => 200,
                'message' => $message,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => true,
                'code' => 500,
                'message' => 'Something went wrong!',
            ], 500);
        }
    }

    public function getCart(Request $request)
    {
        // Get user_id from the request (you can pass it as a parameter or retrieve it from localStorage)
        $userId = $request->user_id;

        // Fetch the order with status 'cart' for the specific user
        $cart = Order::with('items.menu')
            ->where('user_id', $userId)
            ->where('status', 'cart')
            ->first();

        // Return the order items or an empty array if no cart found
        return response()->json(['cart' => $cart ? $cart->items : []]);
    }


    public function updateQuantity(Request $request, $orderItemId)
    {
        $userId = $request->user_id; // Ambil user_id dari request
        $quantity = $request->quantity;

        // Temukan item dalam order
        $orderItem = OrderItem::where('id', $orderItemId)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', 'cart');
            })
            ->first();

        if ($orderItem) {
            // Cek stok menu
            $menu = $orderItem->menu;
            if ($quantity > $menu->stok) {
                return response()->json(['message' => 'Stok tidak mencukupi'], 400);
            }

            $orderItem->quantity = $quantity;
            $orderItem->price = $menu->price * $quantity;
            $orderItem->save();

            $order = Order::find($orderItem->order_id);
            $order->total_price = OrderItem::where('order_id', $order->id)->sum('price');
            $order->save();

            return response()->json(['item' => $orderItem, 'total_price' => $order->total_price]);
        }

        return response()->json(['message' => 'Item tidak ditemukan'], 404);
    }

    public function deleteItem($orderItemId)
    {
        $orderItem = OrderItem::find($orderItemId);

        if ($orderItem) {
            $orderItem->delete();
            return response()->json(['message' => 'Item dihapus']);
        }

        return response()->json(['message' => 'Item tidak ditemukan'], 404);
    }

    public function orderList(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('kasir_id')
                        ->where('status', 'waiting_for_payment');
                })->orWhere(function ($q) {
                    $q->where('payment_method', 'pay_online')
                        ->whereNotIn('status', ['canceled', 'waiting_for_payment'])
                        ->whereNull('kasir_id');
                });
            })
                ->latest()
                ->get();


            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('', function ($order) {})
                ->addColumn('action', function ($order) {
                    $detailUrl = route('detail-order', $order->id) . "?type=order";
                    $updateStatusUrl = route('update-status-order', $order->id);

                    // Status options for the dropdown
                    $statusOptions = [
                        'waiting_for_payment' => 'Waiting for Payment',
                        'finished' => 'Finished',
                        'canceled' => 'Canceled'
                    ];

                    // Create a select dropdown with the available statuses
                    $statusSelect = '<form action="' . $updateStatusUrl . '" method="POST" style="display:flex; width: auto;">';
                    $statusSelect .= csrf_field(); // Add CSRF token

                    if ($order->payment_method != 'pay_online') {
                        $statusSelect .= '<select name="status" class="status-select form-control" data-order-id="' . $order->id . '" style="max-width: 120px; margin-right: 10px;">';
                        foreach ($statusOptions as $status => $label) {
                            $selected = ($order->status == $status) ? 'selected' : '';
                            $statusSelect .= '<option value="' . $status . '" ' . $selected . '>' . $label . '</option>';
                        }
                        $statusSelect .= '</select>';
                        $statusSelect .= '<button type="submit" class="btn btn-success btn-sm" style="margin-right: 10px;">Update Status</button>';
                    } else if($order->payment_method == 'pay_online' && $order->status == 'finished') {
                        $statusSelect .= '<button type="submit" class="btn btn-success btn-sm" style="margin-right: 10px;">Konfirmasi pesanan</button>';
                    }

                    $statusSelect .= '<input name="payment_method" value="' . $order->payment_method . '" type="hidden">';
                    $statusSelect .= '<input name="type" value="order" type="hidden">';
                    $statusSelect .= '</form>';

                    // Return HTML with select, update status button, and detail button all in one row using flexbox
                    return '
                        <div class="d-flex align-items-center">
                            <a href="' . $detailUrl . '" class="btn btn-info btn-sm" style="margin-right: 10px;">Detail</a>
                            ' . $statusSelect . '
                        </div>
                    ';
                })                
                ->editColumn('payment_method', function ($order) {
                    return $order->payment_method == 'pay_online' ? 'Pay Online' : 'Pay in Cashier';
                })
                ->editColumn('status', function ($order) {
                    // Define colors based on the status
                    $statusClasses = [
                        'waiting_for_payment' => 'bg-warning text-dark', // Yellow background
                        'finished' => 'bg-success text-white', // Green background
                        'canceled' => 'bg-danger text-white' // Red background
                    ];

                    // Get the class for the current order's status
                    $statusClass = isset($statusClasses[$order->status]) ? $statusClasses[$order->status] : 'bg-secondary text-white';

                    // Return the status wrapped in a div with the appropriate class
                    return '<span class="badge ' . $statusClass . '">' . ucfirst(str_replace('_', ' ', $order->status)) . '</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('order.index', [
            'title' => 'Daftar Pesanan',
            'routePrefix' => 'order'
        ]);
    }


    public function historyOrderList(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::when(Auth::user()->hasRole('kasir'), function ($query) {
                $query->where('kasir_id', auth()->user()->id); // Apply filter only if the user is not an admin
            })
                ->latest()
                ->get();

            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('', function ($order) {})
                ->addColumn('action', function ($order) {
                    $detailUrl = route('detail-order', $order->id) . "?type=historyOrder";
                    $updateStatusUrl = route('update-status-order', $order->id);

                    // Status options for the dropdown
                    $statusOptions = [
                        'waiting_for_payment' => 'Waiting for Payment',
                        'finished' => 'Finished',
                        'canceled' => 'Canceled'
                    ];

                    // Create a select dropdown with the available statuses
                    $statusSelect = '<form action="' . $updateStatusUrl . '" method="POST" style="display:flex; width: auto;">';
                    $statusSelect .= csrf_field(); // Add CSRF token
                    if ($order->payment_method != 'pay_online') {
                        $statusSelect .= '<select name="status" class="status-select form-control" data-order-id="' . $order->id . '" style="max-width: 120px; margin-right: 10px;">';
                        foreach ($statusOptions as $status => $label) {
                            $selected = ($order->status == $status) ? 'selected' : '';
                            $statusSelect .= '<option value="' . $status . '" ' . $selected . '>' . $label . '</option>';
                        }
                        $statusSelect .= '</select>';
                        $statusSelect .= '<button type="submit" class="btn btn-success btn-sm" style="margin-right: 10px;">Update Status</button>';
                    } else {
                        // $statusSelect .= '<button type="submit" class="btn btn-success btn-sm" style="margin-right: 10px;">Konfirmasi pesanan</button>';
                    }

                    $statusSelect .= '<input name="payment_method" value="' . $order->payment_method . '" type="hidden">';
                    $statusSelect .= '<input name="type" value="historyOrder" type="hidden">';
                    $statusSelect .= '</form>';

                    // Return HTML with select, update status button, and detail button all in one row using flexbox
                    return '
                        <div class="d-flex align-items-center">
                            <a href="' . $detailUrl . '" class="btn btn-info btn-sm" style="margin-right: 10px;">Detail</a>
                            ' . $statusSelect . '
                        </div>
                    ';
                })
                ->editColumn('status', function ($order) {
                    // Define colors based on the status
                    $statusClasses = [
                        'waiting_for_payment' => 'bg-warning text-dark', // Yellow background
                        'on_progress' => 'bg-info',
                        'pick_up' => 'bg-info text-white', // Blue background
                        'finished' => 'bg-success text-white', // Green background
                        'canceled' => 'bg-danger text-white' // Red background
                    ];

                    // Get the class for the current order's status
                    $statusClass = isset($statusClasses[$order->status]) ? $statusClasses[$order->status] : 'bg-secondary text-white';

                    // Return the status wrapped in a div with the appropriate class
                    return '<span class="badge ' . $statusClass . '">' . ucfirst(str_replace('_', ' ', $order->status)) . '</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('order.history', [
            'title' => 'Order History',
            'routePrefix' => 'order'
        ]);
    }


    public function detailOrder($id, Request $request)
    {
        $order = Order::with('items.menu')->findOrFail($id);

        return view('order-detail', [
            'order' => $order,
            'route' => route('update-status-order', ['id' => $id]),
            'statuses' => ['waiting_for_payment', 'finished', 'canceled'], // Updated statuses
            'type' => $request->type
        ]);
    }

    public function updateStatusOrder(Request $request, $id)
    {
        $request->validate([
            'status' => 'required_if:payment_method,pay_on_cashier|in:waiting_for_payment,pick_up,on_progress,finished,canceled',
            'payment_method' => 'required|in:pay_online,pay_in_casheer',
        ]);

        $order = Order::findOrFail($id);
        if($order->payment_method != 'pay_online') {
            $order->status = $request->status;
        }
        $order->kasir_id = auth()->user()->id;
        $order->save();

        $redirectRoute = "";
        if ($request->type == "order") {
            $redirectRoute = 'order-list';
        } else {
            $redirectRoute = 'history-order-list';
        }

        return redirect()->route($redirectRoute)
            ->with('success', 'Order status updated successfully.');
    }

    public function show(MidtransService $midtransService, Order $order)
    {
        // get last payment
        $payment = $order->payments->last();

        if ($payment == null || $payment->status == 'EXPIRED') {
            $snapToken = $midtransService->createSnapToken($order);

            $order->payments()->create([
                'snap_token' => $snapToken,
                'status' => 'PENDING',
            ]);
        } else {
            $snapToken = $payment->snap_token;
        }

        return view('customer.orders.show', compact('order', 'snapToken'));
    }
}
