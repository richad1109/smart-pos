@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page_heading', 'Riwayat Transaksi Penjualan')
@section('page_subheading', 'Daftar seluruh pesanan kasir, status pembayaran, dan struk')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Cari Invoice</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: INV-2026..."
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Status Transaksi</label>
                <select name="status" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-500">
                    <option value="">-- Semua Status --</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Void)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Tanggal Transaksi</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan Filter</span>
                </button>
                <a href="{{ route('orders.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">No. Invoice</th>
                        <th class="px-5 py-3.5">Waktu Transaksi</th>
                        <th class="px-5 py-3.5">Kasir</th>
                        <th class="px-5 py-3.5">Pelanggan / Member</th>
                        <th class="px-5 py-3.5 text-center">Item</th>
                        <th class="px-5 py-3.5 text-right">Grand Total</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold font-mono text-slate-900">
                                <a href="{{ route('orders.show', $order->id) }}" class="hover:text-brand-600">
                                    {{ $order->invoice_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-3.5">
                                {{ $order->user->name ?? 'Kasir' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($order->customer)
                                    <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">
                                        {{ $order->customer->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">Tamu (Guest)</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center font-semibold">
                                {{ $order->items->sum('quantity') }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                @if($order->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Dibatalkan (Void)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('orders.show', $order->id) }}" title="Lihat Rincian" 
                                       class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" title="Cetak Struk"
                                       class="p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition">
                                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                Tidak ada transaksi yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
