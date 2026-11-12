@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page_heading', 'Tambah Menu / Produk Baru')
@section('page_subheading', 'Tambahkan item baru ke inventaris toko')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Daftar Produk</span>
    </a>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Barcode / SKU -->
                <div>
                    <label for="barcode_sku" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">SKU / Kode Barcode *</label>
                    <input type="text" id="barcode_sku" name="barcode_sku" value="{{ old('barcode_sku') }}" required placeholder="Contoh: CF-005"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono focus:outline-none focus:border-brand-500">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori *</label>
                    <select id="category_id" name="category_id" required
                            class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nama Produk -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Produk / Menu *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Vanilla Hazelnut Latte"
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:border-brand-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Harga Modal (Cost Price) -->
                <div>
                    <label for="cost_price" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Harga Modal (Rp) *</label>
                    <input type="number" id="cost_price" name="cost_price" value="{{ old('cost_price', 0) }}" min="0" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500">
                </div>

                <!-- Harga Jual (Selling Price) -->
                <div>
                    <label for="selling_price" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Harga Jual (Rp) *</label>
                    <input type="number" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" min="0" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-brand-600 focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Stok Awal -->
                <div>
                    <label for="stock" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stok Awal *</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', 20) }}" min="0" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500">
                </div>

                <!-- Min Stock Alert -->
                <div>
                    <label for="min_stock_alert" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alert Stok Menipis</label>
                    <input type="number" id="min_stock_alert" name="min_stock_alert" value="{{ old('min_stock_alert', 5) }}" min="0" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500">
                </div>

                <!-- Satuan Unit -->
                <div>
                    <label for="unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Satuan *</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', 'cup') }}" required placeholder="cup, porsi, pcs, botol"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <!-- Status Aktif -->
            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-xs font-semibold text-slate-700">Tampilkan produk ini di kasir (Aktif)</span>
                </label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('products.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Produk</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
