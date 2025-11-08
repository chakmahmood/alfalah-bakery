<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('min_stock', 15, 2)->default(0);

            $table->timestamps();

            $table->unique(['branch_id', 'product_id']); // stok unik per cabang & produk
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
