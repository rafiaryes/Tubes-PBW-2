<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
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

require __DIR__.'/auth.php';
