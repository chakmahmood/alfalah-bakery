<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotion_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('promotion_id')
                ->constrained('promotions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('discount_value', 12, 2)->nullable(); // Diskon khusus untuk produk ini (opsional)
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable(); // Tipe diskon, bisa override default promotion
            $table->timestamps();

            $table->unique(['promotion_id', 'product_id']); // Satu produk hanya 1x per promo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_products');
    }
};
