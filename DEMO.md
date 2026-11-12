# 🎓 Panduan Demo Live Tugas Besar: SmartPOS

Aplikasi ini dibangun untuk memenuhi kriteria **Opsi 2: Membuat Aplikasi Baru (+5 Poin Tambahan)** pada mata kuliah **Workshop Pemrograman Framework**.

---

## 🔑 1. Kredensial Akun Pengujian

| Peran (Role) | Email | Password | Akses |
|---|---|---|---|
| **Admin Toko** | `admin@smartpos.test` | `password` | Akses penuh (Kasir, Produk, Kategori, Mutasi, Laporan, Void, Pengaturan) |
| **Kasir POS** | `kasir@smartpos.test` | `password` | Khusus operasional kasir (Kasir POS, Riwayat Transaksi, Cetak Struk) |

---

## 🎬 2. Skenario Demo Live (Urutan Presentasi ke Dosen)

Ikuti 8 langkah praktis berikut saat mempresentasikan aplikasi di depan dosen penguji:

### Langkah 1: Login & Demonstrasi Role-Based Access Control (Middleware)
1. Buka browser di `http://127.0.0.1:8000/login`.
2. Klik tombol shortcut **"🛒 Kasir POS"** dan klik **Masuk**.
3. Tunjukkan bahwa Kasir langsung diarahkan ke Dashboard.
4. Coba buka URL khusus admin secara manual (misal: `http://127.0.0.1:8000/settings`).
5. **Poin yang dijelaskan ke dosen:** Tunjukkan respons **403 Forbidden**, jelaskan bahwa route diproteksi oleh **Custom Middleware `CheckRole`**.
6. Logout dan login kembali menggunakan akun **"👤 Admin Toko"**.

### Langkah 2: Buka Modul Kasir Interaktif (POS)
1. Klik menu **"Kasir POS (Checkout)"** atau tekan shortcut di topbar.
2. Tunjukkan filter kategori (Semua, Coffee & Espresso, Food, Snacks) dan kotak pencarian real-time.
3. Tunjukkan indikator stok pada masing-masing kartu menu:
   * Warna **Hijau**: Stok aman.
   * Warna **Kuning/Merah**: Stok menipis (*low stock alert*).
   * Produk stok 0 otomatis dinonaktifkan dari klik.

### Langkah 3: Tambah Menu ke Keranjang & Pilih Member
1. Klik produk `Kopi Susu Gula Aren` (stok awal 60).
2. Tambahkan `Chicken Katsu Curry Rice` (stok awal 15).
3. Ubah quantity menggunakan tombol `+` dan `-`.
4. Pilih pelanggan member: **Ahmad Fauzi (Member Gold)**.
5. Jelaskan bahwa member terdaftar akan otomatis mengumpulkan **Poin Loyalitas** (1 poin tiap kelipatan Rp 10.000).

### Langkah 4: Proses Transaksi Penjualan (Eksekusi ACID Transaction)
1. Pilih metode pembayaran: **💵 Tunai**.
2. Klik tombol pecahan cepat: **Rp 100.000**.
3. Tunjukkan perhitungan otomatis **Kembalian**.
4. Klik tombol **"Bayar Sekarang (F9)"**.
5. Muncul popup sukses beserta nomor invoice unik (contoh: `INV-20261113-0001`).

### Langkah 5: Cetak Struk Kasir Thermal
1. Pada popup sukses, klik **"Cetak Struk Kasir (Print)"**.
2. Terbuka tampilan struk kasir format thermal printer (58mm/80mm) yang rapi, lengkap dengan nama gerai, rincian barang, diskon, pajak resto, poin member, dan footer struk.
3. Tunjukkan bahwa tampilan struk ini siap dicetak langsung (`window.print()`) atau disimpan sebagai file PDF.

### Langkah 6: Bukti Transaksi Riil — Cek Stok Berkurang & Audit Mutasi
> *Ini adalah poin terpenting untuk membuktikan syarat dosen: "Transaksi tidak boleh hanya membuat tabel master yang berelasi dengan tabel dependen lainnya".*

1. Buka menu **"Produk & Menu"**: Tunjukkan bahwa stok `Kopi Susu Gula Aren` dan `Chicken Katsu` **berkurang secara real-time**.
2. Buka menu **"Mutasi & Restock"**:
   * Tunjukkan tabel **Log Audit Mutasi Stok (`stock_movements`)**.
   * Terlihat baris baru bertipe `sale` dengan perubahan `-$qty`, stok sebelum, stok sesudah, dan nomor referensi invoice yang bersangkutan.

### Langkah 7: Demonstrasi Void / Pembatalan Transaksi (Rollback Stok)
1. Buka menu **"Riwayat Transaksi"** &rarr; klik ikon detail pada transaksi tadi.
2. Klik tombol **"Batalkan Transaksi (Void)"**.
3. Masukkan alasan (misal: *"Pelanggan salah pesan"*).
4. Klik konfirmasi pembatalan.
5. Kembali ke menu **"Mutasi & Restock"**:
   * Tunjukkan bahwa sistem mencatat mutasi baru bertipe `void_refund`.
   * **Stok produk otomatis dikembalikan bertambah** ke jumlah semula!

### Langkah 8: Dashboard Analitik & Laporan Penjualan
1. Buka menu **"Dashboard"**:
   * Tunjukkan grafik tren omzet 7 hari terakhir.
   * Tunjukkan kartu omzet hari ini, laba kotor, dan top 5 menu terlaris.
2. Buka menu **"Laporan Penjualan"**:
   * Filter rentang tanggal atau kasir tertentu.
   * Klik tombol **Cetak Laporan** untuk mencetak rekapitulasi penjualan berformat resmi.

### Langkah 9: Demonstrasi REST API (Postman / Browser)
Tunjukkan endpoint JSON siap pakai untuk aplikasi Android / mobile:
* `GET http://127.0.0.1:8000/api/products` &rarr; Katalog produk JSON.
* `GET http://127.0.0.1:8000/api/stats` &rarr; Metrik omzet, total transaksi, dan alert stok menipis.
* `POST http://127.0.0.1:8000/api/pos/checkout` &rarr; Transaksi kasir via API.

---

## 💬 3. Bocoran Pertanyaan Konsep Dosen & Cara Menjawabnya

### Q1: *"Mengapa transaksi checkout di aplikasi Anda menggunakan `DB::transaction`?"*
> **Jawaban:**  
> Karena proses checkout melibatkan multi-tabel (`orders`, `order_items`, `products`, `stock_movements`, dan `customers`). Jika terjadi kegagalan (misalnya koneksi putus atau stok mendadak tidak cukup saat proses penyimpanan detail), `DB::transaction` menjamin prinsip **Atomicity** — seluruh perubahan di-*rollback* ke kondisi semula sehingga database tidak mengalami data korup atau stok terpotong tanpa ada invoice.

### Q2: *"Apa fungsi tabel `stock_movements` di samping tabel `products`?"*
> **Jawaban:**  
> Tabel `products` hanya menyimpan angka stok saat ini (*current state*). Sedangkan tabel `stock_movements` berfungsi sebagai **audit trail log** (*historical ledger*). Setiap kali stok bertambah (karena restock pasokan atau void) atau berkurang (karena penjualan kasir), sistem mencatat siapa petugasnya, kapan terjadinya, stok sebelum, dan stok sesudah. Hal ini mencegah manipulasi data stok oleh staf.

### Q3: *"Bagaimana cara mencegah race condition jika dua kasir mengklik beli barang yang sama di saat bersamaan?"*
> **Jawaban:**  
> Kami menerapkan **Pessimistic Locking** menggunakan method Eloquent `Product::lockForUpdate()->findOrFail($id)` di dalam blok transaksi database. Baris data produk tersebut dikunci sementara sampai transaksi kasir pertama selesai, sehingga kasir kedua tidak bisa membaca nilai stok usang (*dirty read*).

### Q4: *"Bagaimana aplikasi ini bisa dibuka di HP Android menjadi APK?"*
> **Jawaban:**  
> Aplikasi SmartPOS didesain **Mobile-First & Responsif** menggunakan Tailwind CSS dan sudah dilengkapi spesifikasi **PWA (Progressive Web App)** dengan file `manifest.json` dan `sw.js`. Saat dibuka di HP Android, aplikasi dapat langsung diinstal ke layar utama layaknya aplikasi native, atau dibungkus menjadi file `.apk` menggunakan wrapper **Capacitor / Android Studio WebView** yang terhubung langsung ke REST API Laravel yang telah kami sediakan di `/api/*`.
