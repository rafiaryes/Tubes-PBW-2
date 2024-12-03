<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Menu;
use Illuminate\Http\Request;
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
                $menusQuery->where('nama', 'like', '%' . $request->search . '%');
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
    Route::post('/add-item', [OrderController::class, 'addItem'])->name('order.add-item');

    Route::get('/get-cart', [OrderController::class, 'getCart'])->name("get-cart");
    Route::delete('/cart/{itemId}', [OrderController::class, 'deleteItem'])->name("remove-item");
    Route::get('/cart/{itemId}/update', [OrderController::class, 'updateQuantity'])->name("update-cart");
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
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
});

require __DIR__ . '/auth.php';
