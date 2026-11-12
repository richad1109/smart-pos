<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard dengan metrik statistik & analitik.
     */
    public function index(): View
    {
        $today = Carbon::today();

        // 1. Metrik Hari Ini
        $todayOrders = Order::whereDate('created_at', $today)->where('status', 'completed');
        $todaySales = (clone $todayOrders)->sum('grand_total');
        $todayCount = (clone $todayOrders)->count();

        // 2. Metrik Keseluruhan
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock_alert')->get();

        // 3. Estimasi Laba Kotor Hari Ini (Penjualan - Modal)
        $todayGrossProfit = OrderItem::whereHas('order', function ($query) use ($today) {
            $query->whereDate('created_at', $today)->where('status', 'completed');
        })->selectRaw('SUM((unit_price - unit_cost) * quantity) as gross_profit')->value('gross_profit') ?? 0;

        // 4. Data Penjualan 7 Hari Terakhir untuk Grafik
        $last7Days = collect();
        $salesData = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days->push($date->translatedFormat('D, d M'));

            $dailyTotal = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('grand_total');

            $salesData->push($dailyTotal);
        }

        // 5. Transaksi Terbaru
        $recentOrders = Order::with(['user', 'customer'])
            ->latest()
            ->take(6)
            ->get();

        // 6. Produk Terlaris
        $topProducts = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'todaySales',
            'todayCount',
            'todayGrossProfit',
            'totalProducts',
            'totalCustomers',
            'lowStockProducts',
            'last7Days',
            'salesData',
            'recentOrders',
            'topProducts'
        ));
    }
}
