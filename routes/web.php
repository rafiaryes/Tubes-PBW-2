<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\FacadesDB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

Route::name('user.')->group(function () {
    Route::get('/', function () {
        return view("welcome");
    })->name('welcome');
    Route::get('/order-method', function () {
        return view("order_method");
    })->name('order_method');
    Route::get('/payment-method', function () {
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
    Route::post('/payment/midtrans-callback', [PaymentController::class, 'midtransCallback']);
});

Route::get('/dashboard', function () {
    // Check if the current user is an admin
    $isAdmin = auth()->user()->role == 'admin';

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
        ->groupBy('payment_method')
        ->get();

    // Prepare data to pass to the view
    $paymentMethods = [];
    $orderCounts = [];
    foreach ($paymentMethodsData as $data) {
        $paymentMethods[] = $data->payment_method; // Payment method names
        $orderCounts[] = $data->total_orders; // Order counts for each payment method
    }

    // Return data ke view
    return view('admin.dashboard', compact('monthlyEarnings', 'annualEarnings', 'totalOrders', 'topMenu', 'earningsData', 'months', 'paymentMethods', 'orderCounts'));
})->middleware(['auth', 'verified'])->name('dashboard');


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
