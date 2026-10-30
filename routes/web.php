<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Cashier\CashierController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Kitchen\StockController;

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

Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/pos', [CashierController::class, 'index'])->name('pos');
    Route::post('/transactions', [CashierController::class, 'store'])->name('store');
    Route::get('/transactions/{transaction}/receipt', [CashierController::class, 'receipt'])->name('receipt');
});

//role kitchen
Route::middleware(['auth', 'role:kitchen'])->prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/', [KitchenController::class, 'index'])->name('index');
    Route::get('/orders', [KitchenController::class, 'orders'])->name('orders');
    Route::patch('/orders/{transaction}', [KitchenController::class, 'updateStatus'])->name('update');

    Route::get('/stock', [StockController::class, 'index'])->name('stock');
    Route::patch('/stock/{menu}', [StockController::class, 'toggle'])->name('stock.toggle');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class)->except('show');
});


