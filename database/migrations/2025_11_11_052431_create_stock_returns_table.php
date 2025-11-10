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
        Schema::create('stock_returns', function (Blueprint $table) {
            $table->id();

            // Cabang yang mengembalikan barang
            $table->foreignId('from_branch_id')->constrained('branches')->cascadeOnDelete();

            // Biasanya kembali ke pusat, tapi tetap fleksibel
            $table->foreignId('to_branch_id')->nullable()->constrained('branches')->nullOnDelete();

            // Jenis retur: ke stok atau dibuang
            $table->enum('return_type', ['to_stock', 'dispose'])->default('dispose');

            // Tanggal retur (tanggal diambil dari tempat titip)
            $table->date('return_date');

            // Status proses retur: draft, sent, received
            $table->enum('status', ['draft', 'sent', 'received'])->default('draft');

            // Keterangan tambahan
            $table->text('note')->nullable();

            // User yang membuat retur
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Untuk retur dispose (barang rusak)
            $table->date('disposal_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_returns');
    }
};
