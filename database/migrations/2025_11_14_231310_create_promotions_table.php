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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama promo
            $table->enum('type', ['percentage', 'fixed', 'buy_x_get_y'])->default('fixed'); // Tipe promo
            $table->decimal('value', 12, 2)->nullable(); // Nilai diskon atau jumlah free item
            $table->date('start_date'); // Mulai promo
            $table->date('end_date');   // Berakhir promo
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
