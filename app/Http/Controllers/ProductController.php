<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Tampilkan katalog daftar produk & menu.
     */
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode_sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Tampilkan formulir tambah produk baru.
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Simpan data produk baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'barcode_sku' => ['required', 'string', 'max:50', 'unique:products,barcode_sku'],
            'name' => ['required', 'string', 'max:150'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
        ], [
            'barcode_sku.unique' => 'Barcode / SKU sudah digunakan produk lain.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'category_id.required' => 'Pilih kategori produk.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        DB::transaction(function () use ($validated) {
            $product = Product::create($validated);

            // Jika ada stok awal, catat di mutasi
            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id() ?? 1,
                    'type' => 'restock',
                    'quantity_change' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => $product->stock,
                    'reference_no' => 'NEW-PRODUCT-INIT',
                    'remarks' => 'Inisialisasi stok saat pembuatan produk baru',
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', "Produk '{$validated['name']}' berhasil ditambahkan ke inventaris!");
    }

    /**
     * Tampilkan formulir edit produk.
     */
    public function edit(Product $product): View
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Perbarui data produk.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'barcode_sku' => ['required', 'string', 'max:50', 'unique:products,barcode_sku,' . $product->id],
            'name' => ['required', 'string', 'max:150'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', "Informasi produk '{$product->name}' berhasil diperbarui!");
    }

    /**
     * Hapus produk.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Cek apakah produk sudah ada di transaksi
        if ($product->orderItems()->exists()) {
            return back()->with('error', "Produk '{$product->name}' tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan. Anda dapat menonaktifkannya.");
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produk '{$productName}' telah dihapus dari sistem.");
    }
}
