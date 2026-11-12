@extends('layouts.app')

@section('title', 'Kategori Menu')
@section('page_heading', 'Manajemen Kategori')
@section('page_subheading', 'Pengelompokan jenis produk: Coffee, Non-Coffee, Food, Snacks, dll')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-bold text-slate-800 text-sm">Daftar Kategori Terdaftar</h3>
            <p class="text-xs text-slate-500">Kategori digunakan untuk filter tombol pada terminal kasir POS</p>
        </div>
        <a href="{{ route('categories.create') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Kategori</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 text-[10px]">
                <tr>
                    <th class="px-5 py-3.5">Nama Kategori</th>
                    <th class="px-5 py-3.5">Slug</th>
                    <th class="px-5 py-3.5">Deskripsi</th>
                    <th class="px-5 py-3.5 text-center">Jumlah Produk</th>
                    <th class="px-5 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $cat)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-3.5 font-bold text-slate-900">
                            {{ $cat->name }}
                        </td>
                        <td class="px-5 py-3.5 font-mono text-slate-500">
                            {{ $cat->slug }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">
                            {{ $cat->description ?: '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-center font-bold text-brand-600">
                            {{ $cat->products_count }} Menu
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('categories.edit', $cat->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Edit">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                </a>
                                <form method="POST" action="{{ route('categories.destroy', $cat->id) }}" onsubmit="return confirm('Hapus kategori ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                            Belum ada data kategori.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($categories->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
