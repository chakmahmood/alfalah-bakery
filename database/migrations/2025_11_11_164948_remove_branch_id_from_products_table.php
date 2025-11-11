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
        Schema::table('products', function (Blueprint $table) {
            // Hapus kolom branch_id
            $table->dropForeign(['branch_id']); // hapus foreign key dulu
            $table->dropColumn('branch_id');    // lalu hapus kolom
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Untuk rollback, tambahkan kolom branch_id lagi
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('id')
                ->comment('Cabang khusus (null = berlaku untuk semua cabang)');
        });
    }
};
