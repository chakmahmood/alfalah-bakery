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
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();

            // Relasi ke resep
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();

            // Relasi ke produk (bahan baku)
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Relasi ke unit
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('quantity', 15, 2)->default(0); // jumlah bahan baku
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
