@extends('layouts.app')

@section('title', 'Mutasi & Restock Stok')
@section('page_heading', 'Mutasi & Manajemen Stok Inventaris')
@section('page_subheading', 'Audit log pergerakan keluar-masuk barang dan restock supplier')

@section('content')
<div class="space-y-6">

    <!-- Alert Produk Menipis (Jika Ada) -->
    @if($lowStockProducts->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2 text-amber-800 font-bold text-xs sm:text-sm">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                    <span>Peringatan Stok Menipis (Perlu Restock Segera):</span>
                </div>
                <a href="{{ route('stock.restock.create') }}" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                    + Restock Sekarang
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 text-xs">
                @foreach($lowStockProducts as $low)
                    <div class="p-2.5 bg-white/80 rounded-xl border border-amber-200/80 flex items-center justify-between">
                        <div class="truncate mr-2">
                            <span class="font-bold text-slate-800 block truncate">{{ $low->name }}</span>
                            <span class="text-[10px] text-slate-500">Min Alert: {{ $low->min_stock_alert }} {{ $low->unit }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full font-bold text-[11px] bg-rose-100 text-rose-700 shrink-0">
                            {{ $low->stock }} {{ $low->unit }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action & Filter Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('stock.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <select name="type" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500">
                <option value="">-- Semua Jenis Mutasi --</option>
                <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Penjualan Kasir (Sale)</option>
                <option value="restock" {{ request('type') === 'restock' ? 'selected' : '' }}>Barang Masuk (Restock)</option>
                <option value="void_refund" {{ request('type') === 'void_refund' ? 'selected' : '' }}>Pembatalan Transaksi (Void)</option>
                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian (Adjustment)</option>
            </select>

            <select name="product_id" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500">
                <option value="">-- Semua Produk --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition">
                Filter
            </button>
            <a href="{{ route('stock.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition">
                Reset
            </a>
        </form>

        @if(Auth::user()->isAdmin())
            <a href="{{ route('stock.restock.create') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm shrink-0">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Input Barang Masuk (Restock)</span>
            </a>
        @endif
    </div>

    <!-- Stock Movements Table (Audit Trail) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h4 class="font-bold text-xs text-slate-700 uppercase tracking-wider">Log Audit Mutasi Stok Realtime</h4>
            <span class="text-[11px] text-slate-500">Mencatat otomatis setiap ada penjualan, restock, atau pembatalan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 text-[10px]">
                    <tr>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Nama Produk</th>
                        <th class="px-5 py-3 text-center">Jenis Mutasi</th>
                        <th class="px-5 py-3 text-center">Stok Awal</th>
                        <th class="px-5 py-3 text-center">Perubahan (Qty)</th>
                        <th class="px-5 py-3 text-center">Stok Akhir</th>
                        <th class="px-5 py-3">No. Referensi</th>
                        <th class="px-5 py-3">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movements as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500">
                                {{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-3 font-bold text-slate-900">
                                {{ $m->product->name ?? 'Produk Dihapus' }}
                                <span class="text-[10px] text-slate-400 font-mono block">SKU: {{ $m->product->barcode_sku ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($m->type === 'sale')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Penjualan (Kasir)
                                    </span>
                                @elseif($m->type === 'restock')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Restock Masuk
                                    </span>
                                @elseif($m->type === 'void_refund')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Void / Refund
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        Penyesuaian
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center font-mono">
                                {{ $m->stock_before }}
                            </td>
                            <td class="px-5 py-3 text-center font-mono font-bold">
                                @if($m->quantity_change > 0)
                                    <span class="text-emerald-600">+{{ $m->quantity_change }}</span>
                                @else
                                    <span class="text-rose-600">{{ $m->quantity_change }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center font-mono font-extrabold text-slate-900">
                                {{ $m->stock_after }}
                            </td>
                            <td class="px-5 py-3 font-mono text-[11px] text-slate-700">
                                {{ $m->reference_no ?? '-' }}
                            </td>
                            <td class="px-5 py-3 text-slate-500">
                                {{ $m->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                Belum ada catatan mutasi stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
