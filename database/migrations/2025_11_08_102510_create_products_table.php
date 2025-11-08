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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // 🔗 Relasi ke cabang, kategori, dan satuan
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('Cabang khusus (null = berlaku untuk semua cabang)');

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('Kategori produk');

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('Satuan produk (pcs, box, pack, dll)');

            // 🧾 Informasi dasar produk
            $table->string('sku', 100)
                ->nullable()
                ->unique()
                ->comment('Kode unik produk');
            $table->string('name')->comment('Nama produk');
            $table->string('slug')->nullable()->comment('Slug untuk URL / SEO');

            // ⚙️ Jenis produk: product = jadi, material = bahan baku
            $table->enum('type', ['product', 'material'])
                ->default('product')
                ->comment('Jenis item: product = jadi, material = bahan baku');

            $table->boolean('is_sellable')
                ->default(false)
                ->comment('Apakah bisa dijual di kasir');
            $table->boolean('is_active')
                ->default(true)
                ->comment('Status aktif produk');

            // 💰 Harga
            $table->decimal('sell_price', 15, 2)
                ->default(0)
                ->comment('Harga jual (produk jadi)');
            $table->decimal('cost_price', 15, 2)
                ->default(0)
                ->comment('Harga pokok / modal');

            // 🖼️ Deskripsi dan gambar
            $table->text('description')->nullable()->comment('Deskripsi produk');
            $table->string('image')->nullable()->comment('Foto utama produk');

            // 🕒 Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
