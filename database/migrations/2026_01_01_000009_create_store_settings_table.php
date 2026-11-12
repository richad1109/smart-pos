<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name', 100)->default('SmartPOS Coffee & Retail');
            $table->string('phone', 25)->default('081234567890');
            $table->string('email', 100)->default('info@smartpos.local');
            $table->text('address')->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0.00); // 0% atau misal 10% / 11% PPN
            $table->text('receipt_footer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
