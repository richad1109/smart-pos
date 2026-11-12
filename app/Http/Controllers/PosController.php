<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StoreSetting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Tampilkan antarmuka kasir POS interaktif.
     */
    public function index(): View
    {
        $categories = Category::withCount('products')->get();

        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $customers = Customer::orderBy('name')->get();
        $storeSetting = StoreSetting::first() ?? new StoreSetting();

        return view('pos.index', compact('categories', 'products', 'customers', 'storeSetting'));
    }

    /**
     * Proses transaksi penjualan kasir (ACID Transaction).
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:cash,qris,transfer,card'],
            'cash_amount' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Keranjang belanja tidak boleh kosong.',
            'items.min' => 'Pilih minimal 1 produk untuk checkout.',
            'cash_amount.required' => 'Nominal pembayaran wajib diisi.',
            'payment_method.required' => 'Pilih metode pembayaran.',
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $request) {
                $itemsData = $validated['items'];
                $discountAmount = (float) ($validated['discount_amount'] ?? 0);
                $cashAmount = (float) $validated['cash_amount'];
                $storeSetting = StoreSetting::first();
                $taxPercentage = $storeSetting ? (float) $storeSetting->tax_percentage : 0;

                $subtotal = 0;
                $processedItems = [];

                // 1. Verifikasi dan Kunci Stok (lockForUpdate untuk mencegah race condition)
                foreach ($itemsData as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $qty = (int) $item['quantity'];

                    // Validasi kecukupan stok produk
                    if ($product->stock < $qty) {
                        throw new Exception("Stok untuk produk '{$product->name}' tidak mencukupi! Sisa stok tersedia: {$product->stock} {$product->unit}.");
                    }

                    $itemSubtotal = $product->selling_price * $qty;
                    $subtotal += $itemSubtotal;

                    $processedItems[] = [
                        'product' => $product,
                        'quantity' => $qty,
                        'unit_cost' => $product->cost_price,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                // 2. Hitung Pajak dan Grand Total
                $afterDiscount = max(0, $subtotal - $discountAmount);
                $taxAmount = ($afterDiscount * $taxPercentage) / 100;
                $grandTotal = $afterDiscount + $taxAmount;

                // 3. Validasi Uang Pembayaran
                if ($cashAmount < $grandTotal && $validated['payment_method'] === 'cash') {
                    $selisih = number_format($grandTotal - $cashAmount, 0, ',', '.');
                    throw new Exception("Uang pembayaran kurang Rp {$selisih}! Total tagihan: Rp " . number_format($grandTotal, 0, ',', '.'));
                }

                $changeAmount = max(0, $cashAmount - $grandTotal);

                // 4. Generate Nomor Invoice Unik Otomatis
                $todayDate = Carbon::now()->format('Ymd');
                $countToday = Order::whereDate('created_at', Carbon::today())->count() + 1;
                $invoiceNumber = 'INV-' . $todayDate . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                // 5. Simpan Header Transaksi (orders)
                $order = Order::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => Auth::id() ?? 1,
                    'customer_id' => $validated['customer_id'] ?? null,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'tax_amount' => $taxAmount,
                    'grand_total' => $grandTotal,
                    'cash_amount' => $cashAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed',
                    'notes' => $validated['notes'] ?? null,
                ]);

                // 6. Simpan Rincian Transaksi (order_items) & Potong Stok Produk
                foreach ($processedItems as $item) {
                    $product = $item['product'];
                    $qty = $item['quantity'];

                    // Insert detail item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_cost' => $item['unit_cost'],
                        'unit_price' => $item['unit_price'],
                        'quantity' => $qty,
                        'subtotal' => $item['subtotal'],
                    ]);

                    $stockBefore = $product->stock;
                    $stockAfter = $stockBefore - $qty;

                    // POTONG STOK REALTIME
                    $product->decrement('stock', $qty);

                    // PENCATATAN AUDIT MUTASI STOK OTOMATIS
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id() ?? 1,
                        'type' => 'sale',
                        'quantity_change' => -$qty,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reference_no' => $order->invoice_number,
                        'remarks' => "Penjualan Kasir POS (Invoice: {$order->invoice_number})",
                    ]);
                }

                // 7. Akumulasi Poin Loyalitas Member jika Terdaftar
                if ($order->customer_id) {
                    $customer = Customer::find($order->customer_id);
                    if ($customer) {
                        // Dapat 1 poin setiap kelipatan Rp 10.000 belanja
                        $earnedPoints = (int) floor($grandTotal / 10000);
                        if ($earnedPoints > 0) {
                            $customer->increment('loyalty_points', $earnedPoints);
                        }
                    }
                }

                return $order;
            });

            // Muat relasi lengkap untuk cetak struk
            $order->load(['items.product', 'user', 'customer']);

            return response()->json([
                'success' => true,
                'message' => "Transaksi {$order->invoice_number} berhasil diproses!",
                'order' => $order,
                'receipt_url' => route('orders.receipt', $order->id),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
