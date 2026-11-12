<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard atau login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Autentikasi Pengguna
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Seluruh Route Aplikasi yang Memerlukan Login
Route::middleware('auth')->group(function () {

    // 1. Dashboard Statistik
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Modul Kasir Interaktif POS (Checkout Realtime)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // 3. Riwayat Transaksi & Struk Kasir
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');

    // Pembatalan Transaksi / Void (Hanya Admin)
    Route::post('/orders/{order}/void', [OrderController::class, 'void'])
        ->name('orders.void')
        ->middleware('role:admin');

    // 4. Mutasi Stok & Restock Barang
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/restock', [StockController::class, 'createRestock'])->name('stock.restock.create')->middleware('role:admin');
    Route::post('/stock/restock', [StockController::class, 'storeRestock'])->name('stock.restock.store')->middleware('role:admin');

    // 5. CRUD Produk & Menu
    Route::resource('products', ProductController::class);

    // 6. CRUD Kategori (Hanya Admin)
    Route::resource('categories', CategoryController::class)->middleware('role:admin');

    // 7. CRUD Pelanggan / Member
    Route::resource('customers', CustomerController::class);

    // 8. Laporan & Pengaturan Toko (Hanya Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // 9. Profil Pengguna & Ganti Password
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
