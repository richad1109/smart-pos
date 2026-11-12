@extends('layouts.app')

@section('title', 'Input Restock Barang')
@section('page_heading', 'Input Barang Masuk (Restock)')
@section('page_subheading', 'Tambah pasokan stok produk dari supplier ke gudang toko')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('stock.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Riwayat Mutasi</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('stock.restock.store') }}" class="space-y-4">
            @csrf

            <!-- Pilih Produk -->
            <div>
                <label for="product_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Produk / Menu *</label>
                <select name="product_id" id="product_id" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:border-brand-500">
                    <option value="">-- Pilih Produk yang Masuk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} (SKU: {{ $p->barcode_sku }}) &bull; Sisa Stok: {{ $p->stock }} {{ $p->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jumlah Barang Masuk (Qty) -->
            <div>
                <label for="quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah Barang Masuk (Qty) *</label>
                <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity', 10) }}" required
                       placeholder="Contoh: 20"
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:border-brand-500">
                <p class="text-[11px] text-slate-400 mt-1">Stok produk akan bertambah otomatis sesuai jumlah yang diinput.</p>
            </div>

            <!-- Nomor Referensi / Surat Jalan / PO -->
            <div>
                <label for="reference_no" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor PO / Surat Jalan (Opsional)</label>
                <input type="text" id="reference_no" name="reference_no" value="{{ old('reference_no') }}"
                       placeholder="Contoh: SJ-SUPPLIER-2026/11/01"
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-500">
            </div>

            <!-- Catatan / Nama Supplier -->
            <div>
                <label for="remarks" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keterangan / Supplier</label>
                <textarea id="remarks" name="remarks" rows="2" placeholder="Contoh: Kiriman supplier PT Kopi Nusantara"
                          class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-500">{{ old('remarks') }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('stock.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan & Tambah Stok</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
