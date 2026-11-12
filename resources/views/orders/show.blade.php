@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->invoice_number)
@section('page_heading', 'Detail Transaksi')
@section('page_subheading', 'Rincian pesanan ' . $order->invoice_number)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="{ showVoidModal: false }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Transaksi</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('orders.receipt', $order->id) }}" target="_blank"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Struk Kasir</span>
            </a>

            @if(Auth::user()->isAdmin() && $order->status === 'completed')
                <button type="button" @click="showVoidModal = true"
                        class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-semibold text-xs rounded-xl transition flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                    <span>Batalkan Transaksi (Void)</span>
                </button>
            @endif
        </div>
    </div>

    @if($order->status === 'cancelled')
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-3">
            <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
            <div class="text-xs">
                <h4 class="font-bold text-rose-900">Transaksi Telah Dibatalkan (Void)</h4>
                <p class="text-rose-700 mt-0.5">Seluruh stok item telah dikembalikan otomatis ke inventaris gudang.</p>
                @if($order->cancellation_reason)
                    <p class="text-rose-800 mt-1 font-semibold">Alasan: "{{ $order->cancellation_reason }}"</p>
                @endif
            </div>
        </div>
    @endif

    <!-- Invoice Card Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">

        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider mb-2 {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ $order->status === 'completed' ? 'Transaksi Selesai' : 'Dibatalkan (Void)' }}
                </span>
                <h3 class="text-xl font-extrabold text-slate-900 font-mono">{{ $order->invoice_number }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Waktu: {{ $order->created_at->translatedFormat('d F Y - H:i:s') }} WIB</p>
            </div>

            <div class="sm:text-right text-xs space-y-1">
                <p class="text-slate-500">Kasir: <span class="font-bold text-slate-800">{{ $order->user->name ?? 'Kasir' }}</span></p>
                <p class="text-slate-500">Metode Bayar: <span class="font-bold text-slate-800 uppercase">{{ $order->payment_method }}</span></p>
                <p class="text-slate-500">Customer: 
                    <span class="font-bold text-indigo-600">{{ $order->customer->name ?? 'Tamu Biasa (Guest)' }}</span>
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <div>
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Rincian Barang yang Dibeli</h4>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                        <tr>
                            <th class="px-4 py-3">Nama Produk</th>
                            <th class="px-4 py-3 text-right">Harga Satuan</th>
                            <th class="px-4 py-3 text-center">Jumlah (Qty)</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    {{ $item->product_name }}
                                </td>
                                <td class="px-4 py-3 text-right text-slate-600">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-3 text-right font-extrabold text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Calculations Breakdown -->
        <div class="flex justify-end pt-4">
            <div class="w-full sm:w-72 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal Barang:</span>
                    <span class="font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Potongan Diskon:</span>
                        <span class="font-semibold text-rose-600">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($order->tax_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Pajak Restoran:</span>
                        <span class="font-semibold">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="pt-2 border-t border-slate-200 flex justify-between font-extrabold text-sm text-slate-900">
                    <span>Grand Total:</span>
                    <span class="text-brand-600 text-base">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Uang Diterima:</span>
                    <span class="font-semibold">Rp {{ number_format($order->cash_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-900 font-bold">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Pembatalan Transaksi (Void) -->
    <div x-show="showVoidModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-fade-in">
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-3">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <h3 class="text-base font-extrabold text-slate-900">Konfirmasi Pembatalan Transaksi (Void)</h3>
            <p class="text-xs text-slate-500 mt-1">
                Tindakan ini akan mengembalikan stok produk ke gudang dan mencatat riwayat audit mutasi pembatalan.
            </p>

            <form method="POST" action="{{ route('orders.void', $order->id) }}" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pembatalan</label>
                    <textarea name="cancellation_reason" required rows="2" placeholder="Contoh: Salah input pesanan / pelanggan membatalkan pesanan"
                              class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-rose-500"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        Ya, Batalkan Transaksi
                    </button>
                    <button type="button" @click="showVoidModal = false" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
