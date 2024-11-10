<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    Route::prefix('admin/master_data')->name('admin.')->group(function () {
        Route::resource('menu', MenuController::class);
        Route::post('menu/{menu}/status', [MenuController::class, 'status'])->name('menu.status');
    });
    
});

require __DIR__.'/auth.php';
