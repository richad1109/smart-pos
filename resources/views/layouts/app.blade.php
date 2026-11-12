<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SmartPOS Coffee & Retail</title>

    <!-- PWA / Mobile Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SmartPOS">

    <!-- Tailwind CSS CDN & Custom Font -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col text-slate-800" x-data="{ sidebarOpen: false }">

    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar Desktop & Mobile Off-canvas -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out md:static md:translate-x-0 flex flex-col justify-between shadow-2xl md:shadow-none"
        >
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center justify-between px-6 bg-slate-950/60 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white shadow-lg shadow-brand-500/30">
                            <i data-lucide="coffee" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h1 class="font-bold text-white tracking-tight leading-none text-base">SmartPOS</h1>
                            <span class="text-[11px] text-brand-400 font-medium tracking-wider uppercase">Coffee & Retail</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1.5 text-sm font-medium">
                    <!-- Kasir POS Action Highlight -->
                    <a href="{{ route('pos.index') }}" 
                       class="flex items-center justify-between px-4 py-3 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 text-white font-semibold shadow-md shadow-brand-500/20 hover:brightness-110 transition group">
                        <div class="flex items-center gap-3">
                            <i data-lucide="layout-grid" class="w-5 h-5"></i>
                            <span>Kasir POS (Checkout)</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] bg-white/20 rounded-full font-bold uppercase">Kasir</span>
                    </a>

                    <div class="pt-3 pb-1.5 px-3 text-[11px] font-bold text-slate-500 tracking-wider uppercase">Menu Utama</div>

                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white font-semibold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-indigo-400"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('orders.index') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('orders.*') ? 'bg-slate-800 text-white font-semibold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i data-lucide="receipt" class="w-5 h-5 text-emerald-400"></i>
                        <span>Riwayat Transaksi</span>
                    </a>

                    <div class="pt-3 pb-1.5 px-3 text-[11px] font-bold text-slate-500 tracking-wider uppercase">Inventaris & Data</div>

                    <a href="{{ route('products.index') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('products.*') ? 'bg-slate-800 text-white font-semibold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i data-lucide="package" class="w-5 h-5 text-amber-400"></i>
                        <span>Produk & Menu</span>
                    </a>

                    <a href="{{ route('categories.index') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-white font-semibold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i data-lucide="tags" class="w-5 h-5 text-sky-400"></i>
                        <span>Kategori</span>
                    </a>

                    <a href="{{ route('stock.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 rounded-xl transition {{ request()->routeIs('stock.*') ? 'bg-slate-800 text-white font-semibold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="arrow-left-right" class="w-5 h-5 text-rose-400"></i>
                            <span>Mutasi & Restock</span>
                        </div>
                        @php
                            $lowStockCount = \App\Models\Product::whereColumn('stock', '<=', 'min_stock_alert')->count();
                        @endphp
                        @if($lowStockCount > 0)
                            <span class="px-2 py-0.5 text-xs bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-full font-bold">{{ $lowStockCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('customers.index') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('customers.*') ? 'bg-slate-800 text-white font-semibold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i data-lucide="users" class="w-5 h-5 text-purple-400"></i>
                        <span>Pelanggan / Member</span>
                    </a>

                    @if(Auth::user() && Auth::user()->isAdmin())
                        <div class="pt-3 pb-1.5 px-3 text-[11px] font-bold text-slate-500 tracking-wider uppercase">Laporan & Pengaturan</div>

                        <a href="{{ route('reports.sales') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-white font-semibold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 text-teal-400"></i>
                            <span>Laporan Penjualan</span>
                        </a>

                        <a href="{{ route('settings.index') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white font-semibold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                            <i data-lucide="settings" class="w-5 h-5 text-slate-400"></i>
                            <span>Pengaturan Toko</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- User Info & Logout -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <a href="{{ route('profile.index') }}" class="flex items-center gap-3 mb-3 group hover:opacity-90 transition">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-sm group-hover:bg-brand-600 transition shrink-0">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate group-hover:text-indigo-300 transition">{{ Auth::user()->name ?? 'User' }}</p>
                        <span class="inline-block px-1.5 py-0.5 text-[10px] rounded font-bold uppercase tracking-wider {{ Auth::user()->role === 'admin' ? 'bg-purple-900/60 text-purple-300' : 'bg-emerald-900/60 text-emerald-300' }}">
                            {{ Auth::user()->role ?? 'Kasir' }}
                        </span>
                    </div>
                </a>

                <div class="space-y-1.5 mb-2">
                    <a href="{{ route('profile.index') }}" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">
                        <i data-lucide="user-cog" class="w-3.5 h-3.5"></i>
                        <span>Profil & Ganti Password</span>
                    </a>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 rounded-lg transition border border-rose-500/20">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Backdrop for Mobile Sidebar -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-cloak 
             class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm md:hidden"></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Topbar Header -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-4 md:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <div>
                        <h2 class="text-base md:text-lg font-bold text-slate-900 leading-tight">@yield('page_heading', 'Dashboard')</h2>
                        <p class="text-xs text-slate-500 hidden sm:block">@yield('page_subheading', 'SmartPOS Coffee & Retail Management')</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Quick Button POS -->
                    <a href="{{ route('pos.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        <span>Buka Kasir</span>
                    </a>

                    <!-- Profile Link -->
                    <a href="{{ route('profile.index') }}" title="Profil & Ganti Password" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition">
                        <i data-lucide="user-cog" class="w-5 h-5"></i>
                    </a>

                    <!-- Status Badge -->
                    <div class="hidden lg:flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full border border-slate-200 text-xs text-slate-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Online (Demo Ready)</span>
                    </div>
                </div>
            </header>

            <!-- Alerts / Notifications -->
            <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto pb-20 md:pb-8">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 shadow-sm animate-fade-in">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                        <div class="text-sm font-medium text-emerald-800">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3 shadow-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                        <div class="text-sm font-medium text-rose-800">{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 p-4 rounded-xl bg-sky-50 border border-sky-200 flex items-start gap-3 shadow-sm">
                        <i data-lucide="info" class="w-5 h-5 text-sky-600 shrink-0 mt-0.5"></i>
                        <div class="text-sm font-medium text-sky-800">{{ session('info') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 shadow-sm">
                        <div class="flex items-center gap-2 font-bold text-sm mb-1">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                            <span>Mohon periksa kesalahan input berikut:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-0.5 ml-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation (APK / Mobile Friendly) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 px-4 py-2 flex items-center justify-around z-30 shadow-lg">
        <a href="{{ route('pos.index') }}" class="flex flex-col items-center gap-1 text-xs {{ request()->routeIs('pos.*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="layout-grid" class="w-5 h-5"></i>
            <span>Kasir</span>
        </a>
        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 text-xs {{ request()->routeIs('orders.*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="receipt" class="w-5 h-5"></i>
            <span>Pesanan</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-1 text-xs {{ request()->routeIs('products.*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="package" class="w-5 h-5"></i>
            <span>Produk</span>
        </a>
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-xs {{ request()->routeIs('dashboard') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="pie-chart" class="w-5 h-5"></i>
            <span>Statistik</span>
        </a>
    </nav>

    <script>
        // Inisialisasi ikon Lucide
        lucide.createIcons();

        // Register Service Worker untuk PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(err => {
                    console.log('SW registration failed:', err);
                });
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
