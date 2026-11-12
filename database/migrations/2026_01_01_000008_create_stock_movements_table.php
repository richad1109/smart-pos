<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['sale', 'restock', 'void_refund', 'adjustment']);
            $table->integer('quantity_change'); // misal: -2 untuk sale, +10 untuk restock
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reference_no', 50)->nullable(); // Nomor invoice atau surat jalan
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
