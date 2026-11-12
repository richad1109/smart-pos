@extends('layouts.app')

@section('title', 'Profil Member ' . $customer->name)
@section('page_heading', 'Profil & Riwayat Belanja Member')
@section('page_subheading', 'Rincian data pelanggan ' . $customer->name)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Daftar Pelanggan</span>
    </a>

    <!-- Member Profile Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white font-extrabold text-2xl shadow-lg shadow-brand-500/30">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-slate-900">{{ $customer->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    No. HP: <span class="font-mono text-slate-700">{{ $customer->phone ?: 'Tidak ada' }}</span> &bull;
                    Email: {{ $customer->email ?: 'Tidak ada' }}
                </p>
                <p class="text-xs text-slate-500 mt-1">Alamat: {{ $customer->address ?: '-' }}</p>
            </div>
        </div>

        <div class="text-right sm:border-l sm:border-slate-100 sm:pl-6 shrink-0">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Saldo Poin Loyalitas</p>
            <h4 class="text-3xl font-extrabold text-brand-600 mt-1 flex items-center justify-end gap-1.5">
                <i data-lucide="award" class="w-7 h-7 text-brand-500"></i>
                <span>{{ $customer->loyalty_points }}</span>
            </h4>
            <span class="text-[11px] text-slate-400">1 Poin setiap Rp 10.000 belanja</span>
        </div>
    </div>

    <!-- Riwayat Belanja Member -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <h4 class="font-bold text-xs text-slate-700 uppercase tracking-wider">Riwayat 10 Transaksi Terakhir</h4>
        </div>

        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50/50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 text-[10px]">
                <tr>
                    <th class="px-5 py-3">No. Invoice</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Metode Bayar</th>
                    <th class="px-5 py-3 text-right">Total Belanja</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customer->orders as $order)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-3 font-mono font-bold text-slate-900">
                            {{ $order->invoice_number }}
                        </td>
                        <td class="px-5 py-3 text-slate-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3 uppercase font-semibold text-slate-700">
                            {{ $order->payment_method }}
                        </td>
                        <td class="px-5 py-3 text-right font-extrabold text-slate-900">
                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $order->status === 'completed' ? 'Selesai' : 'Dibatalkan' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('orders.show', $order->id) }}" class="p-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg inline-block">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                            Member ini belum pernah melakukan transaksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
