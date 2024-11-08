<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class,'logout']);

Route::get('register', [RegisterController::class,'index']);
Route::post('register', [RegisterController::class,'store']);

Route::get('/dashboard', [DashboardController::class,'index'])->middleware('auth');

