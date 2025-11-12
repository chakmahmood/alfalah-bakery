<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom enum dengan menambahkan opsi 'partial'
        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('draft', 'partial', 'paid', 'cancelled') DEFAULT 'paid'");
    }

    public function down(): void
    {
        // Kembalikan ke semula jika dibatalkan
        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('draft', 'paid', 'cancelled') DEFAULT 'paid'");
    }
};
