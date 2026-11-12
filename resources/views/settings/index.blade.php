@extends('layouts.app')

@section('title', 'Pengaturan Toko')
@section('page_heading', 'Pengaturan Profil Toko & Struk')
@section('page_subheading', 'Konfigurasi identitas gerai, tarif pajak, dan footer struk kasir')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Nama Toko -->
            <div>
                <label for="store_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Toko / Kafe *</label>
                <input type="text" id="store_name" name="store_name" value="{{ old('store_name', $setting->store_name) }}" required
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:outline-none focus:border-brand-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- No Telepon -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Telepon Toko *</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $setting->phone) }}" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Toko</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $setting->email) }}"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Lengkap Toko</label>
                <textarea id="address" name="address" rows="2"
                          class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500">{{ old('address', $setting->address) }}</textarea>
            </div>

            <!-- Tarif Pajak PPN / Resto -->
            <div>
                <label for="tax_percentage" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tarif Pajak Restoran / PPN (%) *</label>
                <div class="relative max-w-xs">
                    <input type="number" step="0.01" id="tax_percentage" name="tax_percentage" value="{{ old('tax_percentage', (float)$setting->tax_percentage) }}" required min="0" max="100"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:outline-none focus:border-brand-500 pr-8">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-xs">%</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Set ke 0 jika tidak memungut pajak.</p>
            </div>

            <!-- Footer Struk Kasir -->
            <div>
                <label for="receipt_footer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Kaki Struk Kasir (Receipt Footer)</label>
                <textarea id="receipt_footer" name="receipt_footer" rows="3"
                          class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono text-slate-800 focus:outline-none focus:border-brand-500">{{ old('receipt_footer', $setting->receipt_footer) }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Teks yang akan tercetak di bagian bawah struk (ucapan terima kasih, password WiFi, dll).</p>
            </div>

            <div class="pt-4 flex items-center justify-end border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
