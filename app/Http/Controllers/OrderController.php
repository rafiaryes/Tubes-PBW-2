<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $order_method = $request->order_method;

        try {
            DB::transaction(function () use ($userId, $menuId, $quantity, $order_method) {

                $order = Order::firstOrCreate(
                    ['user_id' => $userId, 'status' => 'cart'],
                    ['total_price' => 0, 'order_method' => $order_method]
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
            });

            return response()->json(['message' => 'Item berhasil ditambahkan ke keranjang'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan item', 'error' => $e->getMessage()], 500);
        }
    }

    public function makeOrder(Request $request)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            $userId = $request->input('user_id');
            $paymentMethod = $request->input('payment_method');
            $orderMethod = $request->input('order_method');

            $order = Order::where('user_id', $userId)
                ->where('status', 'cart')
                ->first();

            if (!$order) {
                return redirect()->route('user.home')->with('error', 'No active order found.');
            }

            $order->payment_method = $paymentMethod;
            $order->order_method = $orderMethod;
            $order->status = 'waiting_for_payment';
            $order->save();

            DB::commit();

            session()->flash('success', 'Berhasil membuat order!');
            return redirect()->route('user.home'); // Redirect to a success page
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();

            // Flash error message and redirect back
            session()->flash('error', 'Gagal membuat order. Tolong coba lagi.');
            return back();
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
}
