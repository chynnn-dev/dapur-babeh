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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\FrontPageController;
use App\Http\Controllers\Admin\PostController;
// use App\Http\Controllers\Admin\ReportController; // Sudah ada

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Route khusus Admin (Digabung jadi satu dan ditambahkan report)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');

    Route::resource('users', UserController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except(['create', 'show']);

    Route::patch('menus/{menu}/availability', [MenuController::class, 'toggleAvailability'])->name('menus.availability');
    Route::resource('menus', MenuController::class)->except('show');

    Route::resource('tables', TableController::class)->except(['create', 'show']);

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::patch('settings/table-management', [SettingController::class, 'toggleTableManagement'])->name('settings.table-management');
Route::patch('settings/payment/{method}', [SettingController::class, 'togglePayment'])->where('method', 'qris|debit')->name('settings.payment');
Route::post('settings/qris', [SettingController::class, 'uploadQris'])->name('settings.qris.upload');
Route::delete('settings/qris', [SettingController::class, 'removeQris'])->name('settings.qris.remove');
Route::get('front-page', [FrontPageController::class, 'edit'])->name('front.edit');
Route::put('front-page', [FrontPageController::class, 'update'])->name('front.update');

Route::resource('posts', PostController::class)->except('show');
    // TAMBAHKAN ROUTE REPORTS DI SINI
    // Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    // Jika Anda butuh resource lengkap untuk reports, bisa ganti baris atas dengan:
    // Route::resource('reports', ReportController::class)->only(['index', 'show']);
});

// Role Cashier
Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/pos', [CashierController::class, 'index'])->name('pos');
    Route::post('/transactions', [CashierController::class, 'store'])->name('store');
    Route::get('/transactions/{transaction}/receipt', [CashierController::class, 'receipt'])->name('receipt');
    Route::get('/tables', [CashierController::class, 'tables'])->name('tables');
    Route::patch('/tables/{table}/reservation', [CashierController::class, 'toggleReservation'])->name('tables.reservation');
});

// Role Kitchen
Route::middleware(['auth', 'role:kitchen'])->prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/', [KitchenController::class, 'index'])->name('index');
    Route::get('/orders', [KitchenController::class, 'orders'])->name('orders');
    Route::patch('/orders/{transaction}', [KitchenController::class, 'updateStatus'])->name('update');

    Route::get('/stock', [StockController::class, 'index'])->name('stock');
    Route::patch('/stock/{menu}', [StockController::class, 'toggle'])->name('stock.toggle');
});