<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Menu;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\FacadesDB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

Route::name('user.')->group(function () {
    Route::get('/', function () {
        return view("welcome");
    })->name('welcome');
    Route::get('/order-method', function () {
        return view("order_method");
    })->name('order_method');
    Route::get('/payment-method', function (Request $request) {

        if($request->user_id == null){
            return redirect()->route('user.home');
        }

        $order = Order::where('user_id', $request->user_id)
            ->where('status', 'cart')
            ->first();

        if (!$order) {
            return redirect()->route('user.home');
        }

        return view("payment_method");
    })->name('payment_method');

    Route::get('/home', function (Request $request) {

        if (request()->ajax()) {
            // Ambil data menu dan terapkan pagination
            $menusQuery = Menu::select('id', 'nama', 'price', 'stok', 'image')->where("stok", '>', '0');

            // Jika ada pencarian

            if ($request->input('category') && !empty($request->category)) {
                $menusQuery->where('category', 'like', '%' . $request->category . '%');
            }

            if ($request->input('search') && !empty($request->search)) {
                $menusQuery
                    ->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%');
            }

            // Mengambil data menu dengan pagination
            $menus = $menusQuery->paginate($request->limit ?? 20, ['*'], 'page', $request->page ?? 1);

            // Membangun HTML untuk card menu
            $htmlContent = $menus->map(function ($menu) {
                return view('components.card', compact('menu'))->render(); // Render komponen card
            })->implode(''); // Gabungkan semua card menjadi satu string HTML

            // Menentukan apakah masih ada data untuk dimuat
            $hasMore = $menus->hasMorePages();

            // Kembalikan HTML dan informasi tentang apakah masih ada data
            return response()->json([
                'html' => $htmlContent,
                'hasMore' => $hasMore
            ]);
        }

        return view("home");
    })->name('home');

    Route::get('/{id}/add-menu', function (Request $request) {
        $menu = Menu::find($request->id);
        return view("add_item", compact('menu'));
    })->name('add-menu');

    Route::get('/cart', function (Request $request) {
        return view("cart");
    })->name('cart');

    Route::post('/order', [OrderController::class, 'makeOrder'])->name('order.store');
    Route::post('/payment', [OrderController::class, 'processPayment'])->name('payment.process');
    Route::post('/midtrans/notification', [OrderController::class, 'handleMidtransNotification'])->name('payment.notification');

    Route::post('/add-item', [OrderController::class, 'addItem'])->name('order.add-item');

    Route::get('/get-cart', [OrderController::class, 'getCart'])->name("get-cart");
    Route::delete('/cart/{itemId}', [OrderController::class, 'deleteItem'])->name("remove-item");
    Route::get('/cart/{itemId}/update', [OrderController::class, 'updateQuantity'])->name("update-cart");

    Route::get('/payment/midtrans/{order_id}', [PaymentController::class, 'midtrans'])->name('payment.midtrans');
    Route::get('/payment/qris/{order_id}', [PaymentController::class, 'qris'])->name('payment.qris');
    Route::get('/payment/qris', [PaymentController::class, 'qris'])->name('payment.qris');
    Route::post('/payment/midtrans-callback', [PaymentController::class, 'midtransCallback']);

    Route::get('/history-orders', function (Request $request) {
        $userId = $request->user_id ?? request()->user_id ?? null;
        if (!$userId) {
            $ordersHtml = '<div class="empty-state w-100">User tidak ditemukan.</div>';
        } else {
            $orders = \App\Models\Order::with('items.menu', 'payments')
                ->where('user_id', $userId)
                ->where('status', '!=', 'cart')
                ->orderByDesc('created_at')
                ->get();
            dd($orders);

            // Cek status pembayaran ke Midtrans untuk setiap order yang waiting_for_payment & pay_online
            foreach ($orders as $order) {
                $order->can_continue_payment = false;
                $order->midtrans_status = null;
                if (
                    $order->status === 'waiting_for_payment'
                    && $order->payment_method === 'pay_online'
                    && $order->payments->count() > 0
                    && $order->payments->last()->snap_token
                ) {
                    try {
                        \Midtrans\Config::$serverKey = config('midtrans.server_key');
                        \Midtrans\Config::$isProduction = config('midtrans.is_production');
                        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
                        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
                        $status = \Midtrans\Transaction::status($order->id);
                        $order->midtrans_status = $status->transaction_status ?? null;
                        $order->can_continue_payment = $order->midtrans_status === 'pending';
                    } catch (\Exception $e) {
                        $order->midtrans_status = null;
                    }
                }
            }

            $ordersHtml = view('partials.history_orders_list', compact('orders'))->render();
        }
        return view('history_orders', compact('ordersHtml'));
    })->name('history-orders');

    Route::get('/history-orders-data', function (Request $request) {
        $orders = \App\Models\Order::with('items.menu')
            ->where('user_id', $request->user_id)
            ->where('status', '!=', 'cart')
            ->orderByDesc('created_at')
            ->get();
        $ordersHtml = view('partials.history_orders_list', compact('orders'))->render();
        return response()->json(['html' => $ordersHtml]);
    })->name('get-history-orders');

    Route::get('/history-order-detail', function (Request $request) {
        $order = \App\Models\Order::with('items.menu')
            ->where('id', $request->order_id)
            ->first();
        if (!$order) return response()->json(['html' => '<div class="alert alert-danger">Order tidak ditemukan.</div>']);
        $html = view('partials.history_order_detail', compact('order'))->render();
        return response()->json(['html' => $html]);
    })->name('get-history-order-detail');
});

 // Tambahkan endpoint untuk cek status pembayaran (AJAX)
Route::get('/api/payment-status', function (Request $request) {
    $order = \App\Models\Order::where('id', $request->order_id)->first();
    $status = $order?->status ?? 'unknown';
    $statusText = match ($status) {
        'pick_up', 'finished' => 'Lunas',
        'waiting_for_payment' => 'Menunggu Pembayaran',
        'canceled' => 'Dibatalkan',
        default => ucfirst(str_replace('_', ' ', $status)),
    };

    // Cek status ke Midtrans jika waiting_for_payment dan pay_online
    $midtrans_status = null;
    $can_continue_payment = false;
    $expired_at = null;
    if ($order && $order->status === 'waiting_for_payment' && $order->payment_method === 'pay_online') {
        $payment = $order->payments()->latest()->first();
        if ($payment && $payment->snap_token) {
            try {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
                \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
                $payment = $order->payments()->latest()->first();
                $midtrans = \Midtrans\Transaction::status($payment->snap_token);
                $midtrans_status = $midtrans->transaction_status ?? null;
                $can_continue_payment = $midtrans_status === 'pending';
                $expired_at = Carbon::parse($midtrans->expiry_time);
                $now = Carbon::now();
                if ($expired_at && $now->greaterThan($expired_at)) {
                    $can_continue_payment = false;
                }
            } catch (\Exception $e) {
                dd($e);
                $midtrans_status = null;
            }
        }
    }

    return response()->json([
        'status' => $status,
        'status_text' => $statusText,
        'midtrans_status' => $midtrans_status,
        'can_continue_payment' => $can_continue_payment,
        'expired_at' => $expired_at,
        'now' => now()->timestamp,
    ]);
});

Route::get('/dashboard', function () {
    // Check if the current user is an admin
    $isAdmin = auth()->user()->hasRole('admin');

    // Pendapatan Bulanan
    $monthlyEarnings = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->whereMonth('orders.created_at', now()->month)
        ->where('orders.status', 'finished')
        ->when(!$isAdmin, function ($query) {
            $query->where('orders.kasir_id', auth()->user()->id); // Filter berdasarkan kasir_id hanya jika bukan admin
        })
        ->sum(DB::raw('order_items.price * order_items.quantity'));

    // Pendapatan Tahunan
    $annualEarnings = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->whereYear('orders.created_at', now()->year)
        ->where('orders.status', 'finished')
        ->when(!$isAdmin, function ($query) {
            $query->where('orders.kasir_id', auth()->user()->id); // Filter berdasarkan kasir_id hanya jika bukan admin
        })
        ->sum(DB::raw('order_items.price * order_items.quantity'));

    // Total Pesanan
    $totalOrders = DB::table('orders')
        ->where('status', 'finished')
        ->when(!$isAdmin, function ($query) {
            $query->where('kasir_id', auth()->user()->id); // Filter berdasarkan kasir_id hanya jika bukan admin
        })
        ->count();

    // Menu Terbanyak Terjual
    $topMenu = DB::table('order_items')
        ->join('menu', 'order_items.menu_id', '=', 'menu.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('orders.status', 'finished')
        ->when(!$isAdmin, function ($query) {
            $query->where('orders.kasir_id', auth()->user()->id); // Filter berdasarkan kasir_id hanya jika bukan admin
        })
        ->select('menu.nama', DB::raw('SUM(order_items.quantity) as total'))
        ->groupBy('menu.nama')
        ->orderByDesc('total')
        ->first();

    // Get total earnings for each month of the current year
    $monthlyChartEarnings = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.price * order_items.quantity) as total_earnings')
        ->whereYear('orders.created_at', now()->year)
        ->where('orders.status', 'finished') // Assuming 'finished' means completed orders
        ->when(!$isAdmin, function ($query) {
            $query->where('orders.kasir_id', auth()->user()->id); // Filter berdasarkan kasir_id hanya jika bukan admin
        })
        ->groupBy(DB::raw('MONTH(orders.created_at)'))
        ->orderBy(DB::raw('MONTH(orders.created_at)'))
        ->get();

    // Prepare data for the chart (total earnings per month)
    $earningsData = [];
    foreach ($monthlyChartEarnings as $earnings) {
        $earningsData[] = (float) $earnings->total_earnings;
    }

    // Prepare labels (months) for the chart
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Total Pesanan per Bulan per Tahun
    $monthlyChartOrders = DB::table('orders')
        ->select(DB::raw('MONTH(orders.created_at) as month'), DB::raw('COUNT(*) as total'))
        ->whereYear('orders.created_at', now()->year)
        ->where('orders.status', 'finished')
        ->when(!$isAdmin, function ($query) {
            $query->where('orders.kasir_id', auth()->user()->id); // Filter berdasarkan kasir_id hanya jika bukan admin
        })
        ->groupBy(DB::raw('MONTH(orders.created_at)'))
        ->orderBy(DB::raw('MONTH(orders.created_at)'))
        ->get();

    $paymentMethodsData = DB::table('orders')
        ->select('payment_method', DB::raw('COUNT(*) as total_orders'))
        ->when(!$isAdmin, function ($query) {
            $query->where('orders.kasir_id', auth()->user()->id);
        })
        ->groupBy('payment_method')
        ->get();

    // Prepare data to pass to the view
    $paymentMethods = [];
    $orderCounts = [];
    foreach ($paymentMethodsData as $data) {
        $paymentMethods[] = $data->payment_method; // Payment method names
        $orderCounts[] = $data->total_orders; // Order counts for each payment method
    }

    if(count($paymentMethods) == 0){
        $paymentMethods = ['pay_in_cashier', 'pay_online'];
        $orderCounts = [0, 0];
    }

    // Return data ke view
    return view('admin.dashboard', compact('monthlyEarnings', 'annualEarnings', 'totalOrders', 'topMenu', 'earningsData', 'months', 'paymentMethods', 'orderCounts'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Route print dashboard
Route::get('/dashboard/print', function (Request $request) {
    $isAdmin = auth()->user()->hasRole('admin');
    $tahun = $request->tahun ?? now()->year;
    $isBulanan = !($request->bulanan === "0" || $request->bulanan === 0);

    if ($isBulanan) {
        $bulan = (int) ($request->bulan ?? now()->month); // pastikan integer
        $ordersQuery = \App\Models\Order::with(['items.menu', 'kasir'])
            ->where('status', 'finished')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun);
        $bulanNama = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
    } else {
        $ordersQuery = \App\Models\Order::with(['items.menu', 'kasir'])
            ->where('status', 'finished')
            ->whereYear('created_at', $tahun);
        $bulan = null;
        $bulanNama = null;
    }

    if (!$isAdmin) {
        $ordersQuery->where('kasir_id', auth()->user()->id);
    }

    $orders = $ordersQuery->orderBy('kasir_id')->orderBy('created_at')->get();

    $grouped = $isAdmin
        ? $orders->groupBy('kasir_id')
        : collect([auth()->user()->id => $orders]);

    // orders group by again by user_id
    $grouped = $grouped->map(function ($orders) {
        return $orders->groupBy('user_id');
    });
    
    $pdf = Pdf::loadView('admin.dashboard_print', [
        'grouped' => $grouped,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'bulanNama' => $bulanNama,
        'isAdmin' => $isAdmin,
        'isBulanan' => $isBulanan,
    ])->setPaper('a4', 'landscape');

    $filename = $isBulanan
        ? "rekapan_dashboard_{$bulan}_{$tahun}.pdf"
        : "rekapan_dashboard_tahunan_{$tahun}.pdf";

    return $pdf->stream($filename);
})->middleware(['auth', 'verified'])->name('dashboard.print');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::prefix('master_data')->name('master_data.')->group(function () {
            Route::resource('menu', MenuController::class);
            Route::post('menu/{menu}/status', [MenuController::class, 'status'])->name('menu.status');

            Route::resource('role', RoleController::class);
            Route::resource('permission', PermissionController::class);

            Route::resource('user', UserController::class);
            Route::post('user/{user}/status', [UserController::class, 'status'])->name('user.status');
        });
    });

    Route::middleware(['role:admin|kasir'])->group(function () {
        Route::get('order', [OrderController::class, 'orderList'])->name('order-list'); // All orders not finished and kasir_id null
        Route::get('history-order', [OrderController::class, 'historyOrderList'])->name('history-order-list'); // All orders that kasir has taken
        Route::get('detail-order/{id}', [OrderController::class, 'detailOrder'])->name('detail-order');
        Route::post('update-status-order/{id}', [OrderController::class, 'updateStatusOrder'])->name('update-status-order');
    });
});

require __DIR__ . '/auth.php';
