@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_heading', 'Dashboard Statistik')
@section('page_subheading', 'Ringkasan operasional dan analitik penjualan')

@section('content')
<div class="space-y-6">

    <!-- 1. Top Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Penjualan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Penjualan Hari Ini</p>
                    <h3 class="text-xl md:text-2xl font-extrabold text-slate-900 mt-1">
                        Rp {{ number_format($todaySales, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-600">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500 gap-1.5">
                <span class="inline-flex items-center text-emerald-600 font-bold">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 mr-0.5"></i>
                    {{ $todayCount }}
                </span>
                <span>transaksi selesai hari ini</span>
            </div>
        </div>

        <!-- Estimasi Laba Kotor -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estimasi Laba Kotor</p>
                    <h3 class="text-xl md:text-2xl font-extrabold text-emerald-600 mt-1">
                        Rp {{ number_format($todayGrossProfit, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <i data-lucide="trending-up" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-slate-500">
                <span>(Harga Jual &minus; Harga Modal)</span>
            </div>
        </div>

        <!-- Total Produk & Menu -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Menu / Produk</p>
                    <h3 class="text-xl md:text-2xl font-extrabold text-slate-900 mt-1">
                        {{ $totalProducts }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                <span>{{ $totalCustomers }} Member terdaftar</span>
                <a href="{{ route('products.index') }}" class="text-brand-600 font-semibold hover:underline">Kelola &rarr;</a>
            </div>
        </div>

        <!-- Alert Stok Menipis -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Alert Stok Menipis</p>
                    <h3 class="text-xl md:text-2xl font-extrabold {{ $lowStockProducts->count() > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1">
                        {{ $lowStockProducts->count() }} <span class="text-xs font-normal text-slate-500">Item</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $lowStockProducts->count() > 0 ? 'bg-rose-50 border border-rose-100 text-rose-600' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs">
                @if($lowStockProducts->count() > 0)
                    <span class="text-rose-600 font-medium">Perlu restock segera!</span>
                    <a href="{{ route('stock.restock.create') }}" class="text-rose-600 font-bold hover:underline">Restock &rarr;</a>
                @else
                    <span class="text-emerald-600 font-medium">Semua stok aman</span>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Interactive Chart (7 Days Sales) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Tren Penjualan 7 Hari Terakhir</h3>
                <p class="text-xs text-slate-500">Performa transaksi kasir harian</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 rounded-lg text-xs font-semibold text-slate-600">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>Seminggu Terakhir</span>
            </span>
        </div>

        <div class="h-64 flex items-end gap-3 pt-6 px-2">
            @php
                $maxSales = max($salesData->max() ?: 1, 100000);
            @endphp
            @foreach($last7Days as $idx => $dayLabel)
                @php
                    $salesAmount = $salesData[$idx] ?? 0;
                    $barHeightPercent = min(100, max(8, ($salesAmount / $maxSales) * 100));
                @endphp
                <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                    <div class="text-[11px] font-bold text-slate-600 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        Rp {{ number_format($salesAmount, 0, ',', '.') }}
                    </div>
                    <div class="w-full max-w-[48px] bg-indigo-100 group-hover:bg-brand-600 rounded-t-xl transition-all duration-300 relative"
                         style="height: {{ $barHeightPercent }}%;">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-600 to-indigo-400 rounded-t-xl opacity-80 group-hover:opacity-100"></div>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 truncate w-full text-center">
                        {{ $dayLabel }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 3. Tables Row: Transaksi Terbaru & Produk Terlaris -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaksi Terbaru -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-base">Transaksi Terbaru</h3>
                    <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">Semua &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 text-sm">
                    @forelse($recentOrders as $order)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('orders.show', $order->id) }}" class="font-bold text-slate-900 hover:text-brand-600 transition truncate block text-xs md:text-sm">
                                    {{ $order->invoice_number }}
                                </a>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $order->created_at->format('H:i') }} &bull; Kasir: {{ $order->user->name ?? 'Kasir' }}
                                    @if($order->customer)
                                        &bull; <span class="text-indigo-600">{{ $order->customer->name }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-extrabold text-slate-900 text-xs md:text-sm">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </p>
                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $order->status === 'completed' ? 'Selesai' : 'Batal' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                            Belum ada transaksi yang tercatat hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('pos.index') }}" class="w-full py-2.5 bg-brand-50 hover:bg-brand-100 text-brand-700 font-semibold text-xs rounded-xl flex items-center justify-center gap-2 transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Buat Transaksi Kasir Baru</span>
                </a>
            </div>
        </div>

        <!-- Menu / Produk Terlaris -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-base">Top 5 Produk Terlaris</h3>
                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">Lihat Menu &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 text-sm">
                    @forelse($topProducts as $idx => $prod)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-6 h-6 rounded-full bg-slate-100 font-bold text-slate-600 text-xs flex items-center justify-center shrink-0">
                                    #{{ $idx + 1 }}
                                </span>
                                <div class="truncate">
                                    <h4 class="font-bold text-slate-900 text-xs md:text-sm truncate">{{ $prod->product_name }}</h4>
                                    <p class="text-[11px] text-slate-500">Terjual {{ $prod->total_qty }} porsi/cup</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-extrabold text-slate-900 text-xs md:text-sm">
                                    Rp {{ number_format($prod->total_sales, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            <i data-lucide="package" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                            Data produk terlaris akan muncul setelah transaksi penjualan.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
                <span>Diperbarui otomatis secara realtime</span>
                <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
            </div>
        </div>
    </div>

</div>
@endsection
