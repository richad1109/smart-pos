@extends('layouts.app')

@section('title', 'Kasir POS')
@section('page_heading', 'Kasir Pintar POS')
@section('page_subheading', 'Terminal kasir checkout transaksi dan cetak struk')

@section('content')
<div x-data="posApp()" x-init="init()" class="h-full flex flex-col -mt-4">

    <!-- POS Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- SISI KIRI: Katalog Menu & Produk (8 Kolom) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            <!-- Filter Kategori & Pencarian -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder="Cari menu, produk, atau scan barcode SKU..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:bg-white transition">
                    </div>
                    <button @click="searchQuery = ''; selectedCategory = 'all'" 
                            class="px-3.5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition shrink-0">
                        Reset Filter
                    </button>
                </div>

                <!-- Category Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-semibold no-scrollbar">
                    <button @click="selectedCategory = 'all'"
                            :class="selectedCategory === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3.5 py-2 rounded-xl transition shrink-0 flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                        <span>Semua ({{ count($products) }})</span>
                    </button>
                    @foreach($categories as $category)
                        <button @click="selectedCategory = '{{ $category->id }}'"
                                :class="selectedCategory === '{{ $category->id }}' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3.5 py-2 rounded-xl transition shrink-0">
                            {{ $category->name }} ({{ $category->products_count }})
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Grid Kartu Produk -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="addToCart(product)"
                         :class="product.stock <= 0 ? 'opacity-60 cursor-not-allowed border-dashed bg-slate-100' : 'cursor-pointer hover:border-brand-500 hover:shadow-md bg-white'"
                         class="p-3.5 rounded-2xl border border-slate-200 transition group flex flex-col justify-between select-none relative overflow-hidden">

                        <!-- Stock Badge -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold" x-text="product.barcode_sku"></span>
                            <span :class="product.stock <= product.min_stock_alert ? 'bg-rose-100 text-rose-700 font-bold' : 'bg-emerald-100 text-emerald-700 font-semibold'"
                                  class="text-[10px] px-2 py-0.5 rounded-full" 
                                  x-text="product.stock > 0 ? 'Stok: ' + product.stock : 'Habis'">
                            </span>
                        </div>

                        <!-- Product Info -->
                        <div class="mb-3">
                            <h4 class="font-bold text-slate-900 text-xs sm:text-sm group-hover:text-brand-600 transition line-clamp-2" x-text="product.name"></h4>
                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="product.category ? product.category.name : ''"></p>
                        </div>

                        <!-- Price & Add Action -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 text-xs sm:text-sm" x-text="formatRupiah(product.selling_price)"></span>
                            <button type="button" 
                                    :disabled="product.stock <= 0"
                                    class="w-7 h-7 rounded-lg bg-brand-50 group-hover:bg-brand-600 text-brand-600 group-hover:text-white flex items-center justify-center transition shadow-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State jika Produk Tidak Ditemukan -->
            <div x-show="filteredProducts.length === 0" x-cloak class="bg-white p-12 rounded-2xl border border-slate-200 text-center">
                <i data-lucide="search-x" class="w-10 h-10 mx-auto text-slate-400 mb-2"></i>
                <h4 class="font-bold text-slate-700 text-sm">Tidak ada menu yang sesuai</h4>
                <p class="text-xs text-slate-500 mt-1">Coba kata kunci pencarian lain atau pilih kategori lain.</p>
            </div>
        </div>

        <!-- SISI KANAN: Keranjang & Terminal Checkout (4-5 Kolom) -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md flex flex-col sticky top-20 overflow-hidden">
                
                <!-- Cart Header -->
                <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-indigo-400"></i>
                        <h3 class="font-bold text-sm">Keranjang Transaksi</h3>
                    </div>
                    <span class="px-2.5 py-0.5 bg-indigo-500/30 text-indigo-300 text-xs rounded-full font-bold" x-text="cart.reduce((a, b) => a + b.quantity, 0) + ' Item'"></span>
                </div>

                <!-- Customer / Member Selector -->
                <div class="p-3.5 bg-slate-50 border-b border-slate-200">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pelanggan / Member</label>
                    <select x-model="customerId" class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500">
                        <option value="">-- Tamu Biasa (Guest) --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }} (Poin: {{ $customer->loyalty_points }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Cart Items List -->
                <div class="p-3.5 max-h-[300px] overflow-y-auto divide-y divide-slate-100 min-h-[140px]">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="py-2.5 flex items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h5 class="font-bold text-xs text-slate-900 truncate" x-text="item.name"></h5>
                                <p class="text-[11px] text-slate-500" x-text="formatRupiah(item.selling_price) + ' &times; ' + item.quantity"></p>
                            </div>

                            <!-- Qty Modifier Buttons -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button @click="decrementQty(index)" class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">-</button>
                                <span class="w-7 text-center font-bold text-xs text-slate-900" x-text="item.quantity"></span>
                                <button @click="incrementQty(index)" class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">+</button>
                            </div>

                            <!-- Subtotal Item & Delete -->
                            <div class="text-right shrink-0 min-w-[70px]">
                                <span class="font-bold text-xs text-slate-900 block" x-text="formatRupiah(item.selling_price * item.quantity)"></span>
                                <button @click="removeFromCart(index)" class="text-[10px] text-rose-500 hover:underline">Hapus</button>
                            </div>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="py-8 text-center text-slate-400 text-xs">
                        <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto mb-1 text-slate-300"></i>
                        Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambah.
                    </div>
                </div>

                <!-- Bill Calculations -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-semibold" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Diskon (Rp)</span>
                        <input type="number" x-model.number="discountAmount" min="0" placeholder="0"
                               class="w-24 p-1 text-right bg-white border border-slate-200 rounded text-xs focus:outline-none">
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Pajak Resto ({{ $storeSetting->tax_percentage }}%)</span>
                        <span class="font-semibold" x-text="formatRupiah(taxAmount)"></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex justify-between items-center text-slate-900">
                        <span class="font-extrabold text-sm">Grand Total</span>
                        <span class="font-extrabold text-base md:text-lg text-brand-600" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <!-- Payment Method & Cash Calculator -->
                <div class="p-4 border-t border-slate-200 space-y-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-1.5 text-xs font-semibold">
                            <button type="button" @click="paymentMethod = 'cash'; cashAmount = grandTotal"
                                    :class="paymentMethod === 'cash' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700'"
                                    class="py-2 rounded-xl transition text-center">
                                💵 Tunai
                            </button>
                            <button type="button" @click="paymentMethod = 'qris'; cashAmount = grandTotal"
                                    :class="paymentMethod === 'qris' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700'"
                                    class="py-2 rounded-xl transition text-center">
                                📱 QRIS
                            </button>
                            <button type="button" @click="paymentMethod = 'transfer'; cashAmount = grandTotal"
                                    :class="paymentMethod === 'transfer' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700'"
                                    class="py-2 rounded-xl transition text-center">
                                💳 Transfer
                            </button>
                        </div>
                    </div>

                    <!-- Nominal Uang Tunai & Kembalian -->
                    <div x-show="paymentMethod === 'cash'" class="space-y-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Uang Diterima (Rp)</label>
                        <input type="number" x-model.number="cashAmount" 
                               placeholder="Contoh: 50000"
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:border-brand-500">

                        <!-- Tombol Cepat Pecahan Uang -->
                        <div class="grid grid-cols-4 gap-1 text-[11px] font-semibold text-slate-700">
                            <button type="button" @click="cashAmount = grandTotal" class="p-1 bg-slate-100 hover:bg-slate-200 rounded-lg">Uang Pas</button>
                            <button type="button" @click="cashAmount = 20000" class="p-1 bg-slate-100 hover:bg-slate-200 rounded-lg">20.000</button>
                            <button type="button" @click="cashAmount = 50000" class="p-1 bg-slate-100 hover:bg-slate-200 rounded-lg">50.000</button>
                            <button type="button" @click="cashAmount = 100000" class="p-1 bg-slate-100 hover:bg-slate-200 rounded-lg">100.000</button>
                        </div>

                        <!-- Hitung Kembalian -->
                        <div class="p-2.5 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-between text-xs">
                            <span class="text-indigo-900 font-semibold">Kembalian:</span>
                            <span class="font-extrabold text-sm" 
                                  :class="changeAmount >= 0 ? 'text-indigo-700' : 'text-rose-600'" 
                                  x-text="changeAmount >= 0 ? formatRupiah(changeAmount) : 'Kurang ' + formatRupiah(Math.abs(changeAmount))">
                            </span>
                        </div>
                    </div>

                    <!-- CTA Checkout Button -->
                    <button type="button" 
                            @click="processCheckout()"
                            :disabled="cart.length === 0 || isProcessing || (paymentMethod === 'cash' && changeAmount < 0)"
                            :class="cart.length === 0 || (paymentMethod === 'cash' && changeAmount < 0) ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white shadow-lg shadow-brand-500/30'"
                            class="w-full py-3.5 px-4 rounded-xl font-extrabold text-sm transition flex items-center justify-center gap-2">
                        <template x-if="isProcessing">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Menyimpan Transaksi...</span>
                            </span>
                        </template>
                        <template x-if="!isProcessing">
                            <span class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Bayar Sekarang (F9)</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Popup Sukses & Cetak Struk Kasir -->
    <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-2xl animate-fade-in border border-slate-200">
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="check" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-extrabold text-slate-900">Transaksi Berhasil!</h3>
            <p class="text-xs text-slate-500 mt-1 font-mono" x-text="completedOrder.invoice_number"></p>

            <div class="my-4 p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs text-left">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Tagihan:</span>
                    <span class="font-bold text-slate-900" x-text="formatRupiah(completedOrder.grand_total)"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nominal Bayar:</span>
                    <span class="font-semibold text-slate-900" x-text="formatRupiah(completedOrder.cash_amount)"></span>
                </div>
                <div class="flex justify-between pt-1 border-t border-slate-200 font-bold text-indigo-600">
                    <span>Kembalian:</span>
                    <span x-text="formatRupiah(completedOrder.change_amount)"></span>
                </div>
            </div>

            <div class="space-y-2">
                <a :href="receiptUrl" target="_blank" 
                   class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2 shadow-sm transition">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Struk Kasir (Print)</span>
                </a>
                <button type="button" @click="resetCart()" 
                        class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function posApp() {
        return {
            products: @json($products),
            searchQuery: '',
            selectedCategory: 'all',
            cart: [],
            customerId: '',
            paymentMethod: 'cash',
            cashAmount: 0,
            discountAmount: 0,
            taxPercentage: {{ $storeSetting->tax_percentage ?? 0 }},
            isProcessing: false,
            showSuccessModal: false,
            completedOrder: {},
            receiptUrl: '#',

            init() {
                // Shortcut keyboard F9 untuk checkout cepat
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'F9' && this.cart.length > 0 && !this.isProcessing) {
                        e.preventDefault();
                        this.processCheckout();
                    }
                });
            },

            get filteredProducts() {
                return this.products.filter(product => {
                    const matchCategory = this.selectedCategory === 'all' || product.category_id == this.selectedCategory;
                    const matchSearch = product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                        product.barcode_sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                    return matchCategory && matchSearch;
                });
            },

            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.selling_price * item.quantity), 0);
            },

            get taxAmount() {
                const afterDiscount = Math.max(0, this.subtotal - (this.discountAmount || 0));
                return Math.round((afterDiscount * this.taxPercentage) / 100);
            },

            get grandTotal() {
                const afterDiscount = Math.max(0, this.subtotal - (this.discountAmount || 0));
                return afterDiscount + this.taxAmount;
            },

            get changeAmount() {
                return (this.cashAmount || 0) - this.grandTotal;
            },

            addToCart(product) {
                if (product.stock <= 0) return;

                const existingItem = this.cart.find(item => item.id === product.id);
                if (existingItem) {
                    if (existingItem.quantity < product.stock) {
                        existingItem.quantity++;
                    } else {
                        alert(`Stok produk ${product.name} hanya tersisa ${product.stock}!`);
                    }
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        selling_price: parseFloat(product.selling_price),
                        stock: product.stock,
                        quantity: 1
                    });
                }

                if (this.paymentMethod === 'cash' && this.cashAmount < this.grandTotal) {
                    this.cashAmount = this.grandTotal;
                }
            },

            incrementQty(index) {
                const item = this.cart[index];
                if (item.quantity < item.stock) {
                    item.quantity++;
                    if (this.paymentMethod === 'cash') this.cashAmount = this.grandTotal;
                } else {
                    alert(`Stok maksimal untuk ${item.name} adalah ${item.stock}!`);
                }
            },

            decrementQty(index) {
                const item = this.cart[index];
                if (item.quantity > 1) {
                    item.quantity--;
                    if (this.paymentMethod === 'cash') this.cashAmount = this.grandTotal;
                } else {
                    this.removeFromCart(index);
                }
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
                if (this.paymentMethod === 'cash') this.cashAmount = this.grandTotal;
            },

            formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0);
            },

            async processCheckout() {
                if (this.cart.length === 0) return;
                if (this.paymentMethod === 'cash' && this.changeAmount < 0) {
                    alert('Nominal uang pembayaran kurang dari grand total!');
                    return;
                }

                this.isProcessing = true;

                const payload = {
                    customer_id: this.customerId || null,
                    payment_method: this.paymentMethod,
                    cash_amount: this.cashAmount,
                    discount_amount: this.discountAmount || 0,
                    items: this.cart.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity
                    }))
                };

                try {
                    const response = await fetch("{{ route('pos.checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.completedOrder = data.order;
                        this.receiptUrl = data.receipt_url;
                        this.showSuccessModal = true;

                        // Perbarui stok lokal agar tidak perlu refresh halaman
                        this.cart.forEach(cartItem => {
                            const prod = this.products.find(p => p.id === cartItem.id);
                            if (prod) prod.stock -= cartItem.quantity;
                        });

                        setTimeout(() => lucide.createIcons(), 100);
                    } else {
                        alert(data.message || 'Gagal memproses transaksi. Silakan coba lagi.');
                    }
                } catch (error) {
                    alert('Terjadi kesalahan jaringan atau server: ' + error.message);
                } finally {
                    this.isProcessing = false;
                }
            },

            resetCart() {
                this.cart = [];
                this.cashAmount = 0;
                this.discountAmount = 0;
                this.customerId = '';
                this.showSuccessModal = false;
            }
        };
    }
</script>
@endpush
@endsection
