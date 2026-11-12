@extends('layouts.app')

@section('title', 'Pelanggan & Member')
@section('page_heading', 'Pelanggan & Member Loyalitas')
@section('page_subheading', 'Kelola data pelanggan setia, nomor kontak, dan poin reward')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('customers.index') }}" class="flex items-center gap-2 flex-1 max-w-md">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau no. telepon member..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-500">
            </div>
            <button type="submit" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition">
                Cari
            </button>
        </form>

        <a href="{{ route('customers.create') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm shrink-0">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Daftar Member Baru</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 text-[10px]">
                <tr>
                    <th class="px-5 py-3.5">Nama Member</th>
                    <th class="px-5 py-3.5">No. Telepon / WA</th>
                    <th class="px-5 py-3.5">Email</th>
                    <th class="px-5 py-3.5 text-center">Poin Loyalitas</th>
                    <th class="px-5 py-3.5 text-center">Total Belanja</th>
                    <th class="px-5 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customers as $c)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-3.5 font-bold text-slate-900">
                            <a href="{{ route('customers.show', $c->id) }}" class="hover:text-brand-600">
                                {{ $c->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-slate-700">
                            {{ $c->phone ?: '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">
                            {{ $c->email ?: '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                <i data-lucide="award" class="w-3.5 h-3.5 text-indigo-500"></i>
                                <span>{{ $c->loyalty_points }} Poin</span>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-slate-800">
                            {{ $c->orders_count }} Transaksi
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('customers.show', $c->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Riwayat Belanja">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('customers.edit', $c->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Edit">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                </a>
                                <form method="POST" action="{{ route('customers.destroy', $c->id) }}" onsubmit="return confirm('Hapus member ini?');" class="inline">
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
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                            Belum ada data pelanggan / member.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
