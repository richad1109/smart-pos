<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna (Admin & Kasir)
        $admin = User::create([
            'name' => 'richadganteng',
            'email' => 'richadganteng@smartpos.test',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('richad ganteng'),
        ]);

        $cashier = User::create([
            'name' => 'Kasir SmartPOS',
            'email' => 'kasir@smartpos.test',
            'phone' => '089876543210',
            'role' => 'cashier',
            'password' => Hash::make('richad ganteng'),
        ]);

        // 2. Pengaturan Toko
        StoreSetting::create([
            'store_name' => 'SmartPOS Coffee & Eatery',
            'phone' => '0812-3456-7890',
            'email' => 'contact@smartpos.local',
            'address' => 'Jl. Raya ITS Gedung TI Lt. 2, Sukolilo, Surabaya',
            'tax_percentage' => 10.00,
            'receipt_footer' => "Terima kasih atas kunjungan Anda!\nPassword WiFi: smartposkopi\nFollow IG: @smartpos.coffee",
        ]);

        // 3. Kategori Produk
        $categoriesData = [
            ['name' => 'Coffee & Espresso', 'slug' => 'coffee-espresso', 'description' => 'Aneka kopi espresso base & manual brew berkualitas'],
            ['name' => 'Non-Coffee & Tea', 'slug' => 'non-coffee-tea', 'description' => 'Minuman segar teh, matcha, cokelat, dan mocktail'],
            ['name' => 'Food & Meals', 'slug' => 'food-meals', 'description' => 'Makanan berat, pasta, dan rice bowl lezat'],
            ['name' => 'Bakery & Snacks', 'slug' => 'bakery-snacks', 'description' => 'Camilan ringan, pastry, croissant, dan dessert'],
            ['name' => 'Retail & Beans', 'slug' => 'retail-beans', 'description' => 'Biji kopi sangrai 200gr dan botolan 1 liter'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::create($cat);
        }

        // 4. Data Produk
        $productsData = [
            [
                'category_id' => $categories['coffee-espresso']->id,
                'barcode_sku' => 'CF-001',
                'name' => 'Espresso Single Shot',
                'cost_price' => 5000,
                'selling_price' => 15000,
                'stock' => 50,
                'min_stock_alert' => 10,
                'unit' => 'cup',
            ],
            [
                'category_id' => $categories['coffee-espresso']->id,
                'barcode_sku' => 'CF-002',
                'name' => 'Iced Americano',
                'cost_price' => 7000,
                'selling_price' => 20000,
                'stock' => 45,
                'min_stock_alert' => 10,
                'unit' => 'cup',
            ],
            [
                'category_id' => $categories['coffee-espresso']->id,
                'barcode_sku' => 'CF-003',
                'name' => 'Caramel Macchiato',
                'cost_price' => 12000,
                'selling_price' => 28000,
                'stock' => 30,
                'min_stock_alert' => 8,
                'unit' => 'cup',
            ],
            [
                'category_id' => $categories['coffee-espresso']->id,
                'barcode_sku' => 'CF-004',
                'name' => 'Kopi Susu Gula Aren',
                'cost_price' => 8000,
                'selling_price' => 22000,
                'stock' => 60,
                'min_stock_alert' => 15,
                'unit' => 'cup',
            ],
            [
                'category_id' => $categories['non-coffee-tea']->id,
                'barcode_sku' => 'NC-001',
                'name' => 'Kyoto Matcha Latte',
                'cost_price' => 11000,
                'selling_price' => 26000,
                'stock' => 25,
                'min_stock_alert' => 5,
                'unit' => 'cup',
            ],
            [
                'category_id' => $categories['non-coffee-tea']->id,
                'barcode_sku' => 'NC-002',
                'name' => 'Signature Dark Chocolate',
                'cost_price' => 10000,
                'selling_price' => 24000,
                'stock' => 20,
                'min_stock_alert' => 5,
                'unit' => 'cup',
            ],
            [
                'category_id' => $categories['non-coffee-tea']->id,
                'barcode_sku' => 'NC-003',
                'name' => 'Lychee Iced Tea',
                'cost_price' => 6000,
                'selling_price' => 18000,
                'stock' => 40,
                'min_stock_alert' => 10,
                'unit' => 'cup',
            ],
            [
                'category_id' => $categories['food-meals']->id,
                'barcode_sku' => 'FD-001',
                'name' => 'Chicken Katsu Curry Rice',
                'cost_price' => 18000,
                'selling_price' => 35000,
                'stock' => 15,
                'min_stock_alert' => 5,
                'unit' => 'porsi',
            ],
            [
                'category_id' => $categories['food-meals']->id,
                'barcode_sku' => 'FD-002',
                'name' => 'Spaghetti Aglio Olio Smoked Beef',
                'cost_price' => 16000,
                'selling_price' => 32000,
                'stock' => 12,
                'min_stock_alert' => 4,
                'unit' => 'porsi',
            ],
            [
                'category_id' => $categories['food-meals']->id,
                'barcode_sku' => 'FD-003',
                'name' => 'Nasi Goreng Kampung Spesial',
                'cost_price' => 14000,
                'selling_price' => 28000,
                'stock' => 20,
                'min_stock_alert' => 5,
                'unit' => 'porsi',
            ],
            [
                'category_id' => $categories['bakery-snacks']->id,
                'barcode_sku' => 'SN-001',
                'name' => 'Butter Croissant',
                'cost_price' => 9000,
                'selling_price' => 18000,
                'stock' => 18,
                'min_stock_alert' => 5,
                'unit' => 'pcs',
            ],
            [
                'category_id' => $categories['bakery-snacks']->id,
                'barcode_sku' => 'SN-002',
                'name' => 'French Fries Truffle Mayo',
                'cost_price' => 10000,
                'selling_price' => 22000,
                'stock' => 30,
                'min_stock_alert' => 5,
                'unit' => 'porsi',
            ],
            [
                'category_id' => $categories['bakery-snacks']->id,
                'barcode_sku' => 'SN-003',
                'name' => 'Choco Lava Cake',
                'cost_price' => 12000,
                'selling_price' => 25000,
                'stock' => 8,
                'min_stock_alert' => 3,
                'unit' => 'porsi',
            ],
            [
                'category_id' => $categories['retail-beans']->id,
                'barcode_sku' => 'RT-001',
                'name' => 'House Blend Coffee Beans 250g',
                'cost_price' => 45000,
                'selling_price' => 85000,
                'stock' => 15,
                'min_stock_alert' => 3,
                'unit' => 'pack',
            ],
            [
                'category_id' => $categories['retail-beans']->id,
                'barcode_sku' => 'RT-002',
                'name' => 'Cold Brew 1 Liter Bottle',
                'cost_price' => 35000,
                'selling_price' => 70000,
                'stock' => 10,
                'min_stock_alert' => 2,
                'unit' => 'botol',
            ],
        ];

        foreach ($productsData as $prod) {
            $product = Product::create($prod);

            // Catat mutasi awal (Initial Stock)
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $admin->id,
                'type' => 'restock',
                'quantity_change' => $product->stock,
                'stock_before' => 0,
                'stock_after' => $product->stock,
                'reference_no' => 'RESTOCK-INIT',
                'remarks' => 'Inisialisasi stok awal toko',
            ]);
        }

        // 5. Data Pelanggan / Member
        Customer::create([
            'name' => 'Ahmad Fauzi (Member Gold)',
            'phone' => '081211112222',
            'email' => 'ahmad.fauzi@example.com',
            'loyalty_points' => 120,
            'address' => 'Sukolilo Dian Regency, Surabaya',
        ]);

        Customer::create([
            'name' => 'Rina Wijaya (Member Silver)',
            'phone' => '081333334444',
            'email' => 'rina.wijaya@example.com',
            'loyalty_points' => 50,
            'address' => 'Keputih Gang 2, Surabaya',
        ]);

        Customer::create([
            'name' => 'Budi Santoso',
            'phone' => '081555556666',
            'email' => 'budi.santoso@example.com',
            'loyalty_points' => 10,
            'address' => 'Gubeng Kertajaya, Surabaya',
        ]);
    }
}
