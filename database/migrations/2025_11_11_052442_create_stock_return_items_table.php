<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_return_id')->constrained('stock_returns')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();

            $table->decimal('quantity', 12, 2);
            $table->decimal('cost_price', 12, 2)->nullable(); // optional: untuk menghitung kerugian retur

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_return_items');
    }
};
