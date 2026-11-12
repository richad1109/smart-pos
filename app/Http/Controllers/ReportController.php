<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Halaman Laporan Penjualan Lengkap.
     */
    public function sales(Request $request): View
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $query = Order::with(['user', 'customer', 'items'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $orders = $query->latest()->get();

        // Kalkulasi Statistik Laporan
        $completedOrders = $orders->where('status', 'completed');
        $totalGrossSales = $completedOrders->sum('subtotal');
        $totalDiscounts = $completedOrders->sum('discount_amount');
        $totalTaxes = $completedOrders->sum('tax_amount');
        $totalNetSales = $completedOrders->sum('grand_total');

        // Total Laba Kotor
        $totalGrossProfit = 0;
        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                $totalGrossProfit += ($item->unit_price - $item->unit_cost) * $item->quantity;
            }
        }

        $users = User::all();
        $storeSetting = StoreSetting::first() ?? new StoreSetting();

        // Mode Cetak Struk / Print Laporan
        if ($request->boolean('print')) {
            return view('reports.sales_print', compact(
                'orders',
                'startDate',
                'endDate',
                'totalGrossSales',
                'totalDiscounts',
                'totalTaxes',
                'totalNetSales',
                'totalGrossProfit',
                'storeSetting'
            ));
        }

        return view('reports.sales', compact(
            'orders',
            'startDate',
            'endDate',
            'totalGrossSales',
            'totalDiscounts',
            'totalTaxes',
            'totalNetSales',
            'totalGrossProfit',
            'users',
            'storeSetting'
        ));
    }
}
