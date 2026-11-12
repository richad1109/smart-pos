<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('barcode_sku', 50)->unique();
            $table->string('name', 150);
            $table->decimal('cost_price', 12, 2)->default(0); // Harga modal
            $table->decimal('selling_price', 12, 2)->default(0); // Harga jual
            $table->integer('stock')->default(0); // Stok realtime
            $table->integer('min_stock_alert')->default(5); // Alert saat stok menipis
            $table->string('unit', 30)->default('pcs'); // pcs, botol, porsi, cup, box
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
