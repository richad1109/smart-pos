@extends('layouts.app')

@section('title', 'Profil Saya & Ganti Password')
@section('page_heading', 'Profil Pengguna & Keamanan')
@section('page_subheading', 'Perbarui informasi akun, email login, dan ganti kata sandi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Card 1: Informasi Profil (Nama & Email / Username) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                <i data-lucide="user" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Informasi Akun Pengguna</h3>
                <p class="text-xs text-slate-500">Ubah nama tampilan dan alamat email login Anda</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Role Badge (Read Only) -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Peran Akses (Role)</label>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }}">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    <span>{{ $user->role === 'admin' ? 'Administrator Toko' : 'Kasir POS' }}</span>
                </div>
            </div>

            <!-- Nama Pengguna -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:border-brand-500">
            </div>

            <!-- Email Login (Username) -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email (Username Login) *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-500">
                <p class="text-[11px] text-slate-400 mt-1">Email ini digunakan untuk masuk ke aplikasi di halaman login.</p>
            </div>

            <!-- No HP -->
            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp / HP</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08123456789"
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono text-slate-800 focus:outline-none focus:border-brand-500">
            </div>

            <div class="pt-3 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Card 2: Keamanan & Ganti Password -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <i data-lucide="lock" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Ganti Kata Sandi (Password)</h3>
                <p class="text-xs text-slate-500">Pastikan gunakan kombinasi password yang kuat dan aman</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Password Saat Ini -->
            <div>
                <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password Saat Ini (Lama) *</label>
                <input type="password" id="current_password" name="current_password" required placeholder="••••••••"
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-500">
            </div>

            <!-- Password Baru -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password Baru (Minimal 6 Karakter) *</label>
                <input type="password" id="password" name="password" required placeholder="••••••••"
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-500">
            </div>

            <!-- Konfirmasi Password Baru -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ulangi Password Baru (Konfirmasi) *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••"
                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-500">
            </div>

            <div class="pt-3 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <i data-lucide="key" class="w-4 h-4"></i>
                    <span>Perbarui Password</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
