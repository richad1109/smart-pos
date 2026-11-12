<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_checkout_executes_acid_transaction_and_decrements_stock(): void
    {
        $this->seed();

        $admin = User::where('role', 'admin')->first();
        $product = Product::first();
        $customer = Customer::first();

        $initialStock = $product->stock;
        $qtyToBuy = 2;

        $response = $this->actingAs($admin)->postJson('/pos/checkout', [
            'customer_id' => $customer->id,
            'payment_method' => 'cash',
            'cash_amount' => 100000,
            'discount_amount' => 0,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $qtyToBuy,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verifikasi Stok Berkurang
        $product->refresh();
        $this->assertEquals($initialStock - $qtyToBuy, $product->stock);

        // Verifikasi Tabel Orders & Items
        $latestOrder = Order::latest()->first();
        $this->assertNotNull($latestOrder);
        $this->assertEquals('completed', $latestOrder->status);
        $this->assertCount(1, $latestOrder->items);

        // Verifikasi Tabel Stock Movements mencatat mutasi 'sale'
        $latestMovement = StockMovement::latest('id')->first();
        $this->assertEquals('sale', $latestMovement->type);
        $this->assertEquals(-$qtyToBuy, $latestMovement->quantity_change);
        $this->assertEquals($latestOrder->invoice_number, $latestMovement->reference_no);
    }

    public function test_order_void_restores_stock_and_logs_movement(): void
    {
        $this->seed();

        $admin = User::where('role', 'admin')->first();
        $product = Product::first();

        // 1. Lakukan Checkout 3 item
        $initialStock = $product->stock;
        $this->actingAs($admin)->postJson('/pos/checkout', [
            'payment_method' => 'cash',
            'cash_amount' => 100000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3]
            ],
        ]);

        $order = Order::latest('id')->first();
        $product->refresh();
        $this->assertEquals($initialStock - 3, $product->stock);

        // 2. Batalkan Transaksi (Void)
        $voidResponse = $this->actingAs($admin)->post("/orders/{$order->id}/void", [
            'cancellation_reason' => 'Customer salah pesan menu'
        ]);

        $voidResponse->assertSessionHas('success');

        // 3. Verifikasi Status Order berubah jadi cancelled
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        // 4. Verifikasi Stok Produk Telah Dikembalikan Penuh (Rollback)
        $product->refresh();
        $this->assertEquals($initialStock, $product->stock);

        // 5. Verifikasi Tercatat di Mutasi dengan type 'void_refund'
        $latestMovement = StockMovement::latest('id')->first();
        $this->assertEquals('void_refund', $latestMovement->type);
        $this->assertEquals(3, $latestMovement->quantity_change);
    }
}
