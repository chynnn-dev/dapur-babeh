<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Placeholder sementara, nanti diganti controller asli tiap role
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::view('/', 'placeholder', ['title' => 'Dashboard Admin'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->group(function () {
    Route::view('/pos', 'placeholder', ['title' => 'POS Kasir'])->name('cashier.pos');
});

Route::middleware(['auth', 'role:kitchen'])->prefix('kitchen')->group(function () {
    Route::view('/', 'placeholder', ['title' => 'Layar Dapur'])->name('kitchen.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class)->except('show');
});
