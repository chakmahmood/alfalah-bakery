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

            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Jenis pergerakan stok
            $table->enum('type', [
                'in',          // stok masuk
                'out',         // stok keluar
                'transfer',    // pindah antar cabang
                'adjustment',  // penyesuaian manual
                'production',  // hasil produksi
                'return',      // retur barang
            ])->default('in');

            // Jumlah pergerakan
            $table->decimal('quantity', 15, 2)->default(0);

            // Keterangan tambahan (misalnya nomor transaksi)
            $table->string('reference')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
