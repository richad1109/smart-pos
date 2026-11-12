@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page_heading', 'Laporan Penjualan & Laba')
@section('page_subheading', 'Rekapitulasi transaksi, omzet pendapatan, dan estimasi laba kotor toko')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Kasir</label>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-500">
                    <option value="">-- Semua Kasir --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Metode Bayar</label>
                <select name="payment_method" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-500">
                    <option value="">-- Semua Metode --</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Tampilkan</span>
                </button>
                <a href="{{ request()->fullUrlWithQuery(['print' => 1]) }}" target="_blank"
                   class="py-2 px-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl transition flex items-center gap-1 border border-indigo-200">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Cetak</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Metrics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Omzet Bersih</span>
            <h3 class="text-xl font-extrabold text-slate-900 mt-1">
                Rp {{ number_format($totalNetSales, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">Dari {{ $orders->where('status', 'completed')->count() }} transaksi sukses</p>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Estimasi Laba Kotor</span>
            <h3 class="text-xl font-extrabold text-emerald-600 mt-1">
                Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">Omzet - Modal Pembelian</p>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Diskon Diberikan</span>
            <h3 class="text-xl font-extrabold text-rose-600 mt-1">
                Rp {{ number_format($totalDiscounts, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">Potongan harga promo</p>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pajak Terkumpul</span>
            <h3 class="text-xl font-extrabold text-indigo-600 mt-1">
                Rp {{ number_format($totalTaxes, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">Pajak resto / PPN</p>
        </div>
    </div>

    <!-- Table of Orders -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h4 class="font-bold text-xs text-slate-700 uppercase tracking-wider">
                Periode: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}
            </h4>
            <span class="text-xs text-slate-500">Total {{ $orders->count() }} Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 text-[10px]">
                    <tr>
                        <th class="px-5 py-3">No. Invoice</th>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Kasir</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3 text-center">Metode</th>
                        <th class="px-5 py-3 text-right">Subtotal</th>
                        <th class="px-5 py-3 text-right">Diskon</th>
                        <th class="px-5 py-3 text-right">Total Akhir</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $o)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-mono font-bold text-slate-900">
                                <a href="{{ route('orders.show', $o->id) }}" class="hover:text-brand-600">
                                    {{ $o->invoice_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-slate-500 whitespace-nowrap">
                                {{ $o->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-3">
                                {{ $o->user->name ?? 'Kasir' }}
                            </td>
                            <td class="px-5 py-3">
                                {{ $o->customer->name ?? 'Tamu (Guest)' }}
                            </td>
                            <td class="px-5 py-3 text-center uppercase font-bold text-[10px] text-slate-700">
                                {{ $o->payment_method }}
                            </td>
                            <td class="px-5 py-3 text-right font-mono text-slate-600">
                                Rp {{ number_format($o->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-right font-mono text-rose-600">
                                {{ $o->discount_amount > 0 ? '-Rp ' . number_format($o->discount_amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3 text-right font-mono font-extrabold text-slate-900">
                                Rp {{ number_format($o->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $o->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $o->status === 'completed' ? 'Selesai' : 'Void' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-slate-400">
                                Tidak ada data penjualan pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
