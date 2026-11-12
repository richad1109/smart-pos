<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StoreSetting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Tampilkan riwayat daftar pesanan/transaksi.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'customer', 'items'])
            ->latest();

        // Filter nomor invoice
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Tampilkan rincian pesanan.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'customer', 'items.product']);
        $storeSetting = StoreSetting::first() ?? new StoreSetting();

        return view('orders.show', compact('order', 'storeSetting'));
    }

    /**
     * Tampilkan format struk thermal kasir (Printable 58mm/80mm).
     */
    public function receipt(Order $order): View
    {
        $order->load(['user', 'customer', 'items.product']);
        $storeSetting = StoreSetting::first() ?? new StoreSetting();

        return view('orders.receipt', compact('order', 'storeSetting'));
    }

    /**
     * Batalkan Transaksi / Void Order (Rollback Stok Otomatis).
     * Fitur ini memenuhi syarat transaksi tingkat lanjut.
     */
    public function void(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ], [
            'cancellation_reason.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Transaksi ini sudah pernah dibatalkan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($order, $request) {
                // 1. Rollback / Kembalikan Stok Seluruh Item ke Gudang/Toko
                foreach ($order->items as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $stockBefore = $product->stock;
                        $stockAfter = $stockBefore + $item->quantity;

                        // Tambahkan kembali stok
                        $product->increment('stock', $item->quantity);

                        // Catat log audit pengembalian stok akibat pembatalan
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id() ?? 1,
                            'type' => 'void_refund',
                            'quantity_change' => $item->quantity,
                            'stock_before' => $stockBefore,
                            'stock_after' => $stockAfter,
                            'reference_no' => $order->invoice_number,
                            'remarks' => "Pembatalan Transaksi / Void (Alasan: {$request->cancellation_reason})",
                        ]);
                    }
                }

                // 2. Rollback Poin Loyalitas Member jika Ada
                if ($order->customer_id) {
                    $earnedPoints = (int) floor($order->grand_total / 10000);
                    if ($earnedPoints > 0) {
                        $order->customer()->decrement('loyalty_points', min($order->customer->loyalty_points, $earnedPoints));
                    }
                }

                // 3. Update Status Order
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $request->cancellation_reason,
                ]);
            });

            return back()->with('success', "Transaksi {$order->invoice_number} berhasil dibatalkan. Stok seluruh item telah dikembalikan otomatis!");

        } catch (Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
