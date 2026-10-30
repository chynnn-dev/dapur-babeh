<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TableController;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Placeholder sementara, nanti diganti controller asli tiap role
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
//     Route::view('/', 'placeholder', ['title' => 'Dashboard Admin'])->name('admin.dashboard');
// });

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except('show');

    Route::resource('categories', CategoryController::class)->except(['create', 'show']);

    Route::patch('menus/{menu}/availability', [MenuController::class, 'toggleAvailability'])->name('menus.availability');
    Route::resource('menus', MenuController::class)->except('show');

    Route::resource('tables', TableController::class)->except(['create', 'show']);

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::patch('settings/table-management', [SettingController::class, 'toggleTableManagement'])->name('settings.table-management');
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


