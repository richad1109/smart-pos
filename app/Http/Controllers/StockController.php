<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Tampilkan riwayat log mutasi stok (Audit Trail Inventaris).
     */
    public function index(Request $request): View
    {
        $query = StockMovement::with(['product.category', 'user'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $movements = $query->paginate(15)->withQueryString();
        $products = Product::orderBy('name')->get();

        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock_alert')->get();

        return view('stock.index', compact('movements', 'products', 'lowStockProducts'));
    }

    /**
     * Tampilkan formulir restock / barang masuk supplier.
     */
    public function createRestock(): View
    {
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        return view('stock.restock', compact('products'));
    }

    /**
     * Simpan proses restock barang masuk (Penambahan Stok + Log Mutasi).
     */
    public function storeRestock(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_no' => ['nullable', 'string', 'max:50'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ], [
            'product_id.required' => 'Pilih produk yang ingin direstock.',
            'quantity.required' => 'Jumlah barang masuk wajib diisi.',
            'quantity.min' => 'Jumlah barang masuk minimal 1.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $product = Product::lockForUpdate()->findOrFail($request->product_id);
                $qty = (int) $request->quantity;

                $stockBefore = $product->stock;
                $stockAfter = $stockBefore + $qty;

                // Tambahkan stok produk
                $product->increment('stock', $qty);

                // Catat di mutasi stok
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id() ?? 1,
                    'type' => 'restock',
                    'quantity_change' => $qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference_no' => $request->reference_no ?: 'PO-' . date('Ymd') . '-' . rand(100, 999),
                    'remarks' => $request->remarks ?: 'Restock pasokan barang masuk',
                ]);
            });

            return redirect()->route('stock.index')
                ->with('success', 'Stok produk berhasil ditambahkan dan tercatat pada riwayat mutasi!');

        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal restock: ' . $e->getMessage());
        }
    }
}
