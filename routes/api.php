<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes (Standar Modul Pertemuan 9 - 10)
|--------------------------------------------------------------------------
*/

Route::get('/ping', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'SmartPOS REST API v1.0 aktif!',
        'server_time' => now()->toIso8601String(),
    ]);
});

// Endpoint 1: Daftar Produk untuk Aplikasi Kasir / Mobile APK
Route::get('/products', function (Request $request) {
    $query = Product::with('category')->where('is_active', true);

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('barcode_sku', 'like', "%{$search}%");
        });
    }

    $products = $query->get()->map(function ($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'barcode' => $p->barcode_sku,
            'category' => $p->category->name ?? 'Uncategorized',
            'price' => (float) $p->selling_price,
            'stock' => $p->stock,
            'unit' => $p->unit,
            'is_low_stock' => $p->isLowStock(),
        ];
    });

    return response()->json([
        'status' => 'success',
        'count' => $products->count(),
        'data' => $products,
    ]);
});

// Endpoint 2: Kategori
Route::get('/categories', function () {
    return response()->json([
        'status' => 'success',
        'data' => Category::all(['id', 'name', 'slug', 'description']),
    ]);
});

// Endpoint 3: Customer / Member
Route::get('/customers', function () {
    return response()->json([
        'status' => 'success',
        'data' => Customer::all(['id', 'name', 'phone', 'email', 'loyalty_points']),
    ]);
});

// Endpoint 4: Metrik Statistik Realtime (Digunakan Dashboard & Postman)
Route::get('/stats', function () {
    $today = Carbon::today();
    $todayOrders = Order::whereDate('created_at', $today)->where('status', 'completed');

    return response()->json([
        'status' => 'success',
        'data' => [
            'today_sales' => (float) (clone $todayOrders)->sum('grand_total'),
            'today_transactions' => (clone $todayOrders)->count(),
            'total_products' => Product::count(),
            'low_stock_count' => Product::whereColumn('stock', '<=', 'min_stock_alert')->count(),
            'total_members' => Customer::count(),
        ],
    ]);
});

// Endpoint 5: Checkout Kasir via API
Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout']);
