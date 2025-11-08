<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Product;

class RecipeSeeder extends Seeder
{
    /**
     * Jalankan seeder resep.
     */
    public function run(): void
    {
        // Contoh produk jadi (pastikan sudah ada di tabel products)
        $produk = Product::where('type', 'product')->first();

        if (!$produk) {
            $this->command->warn('⚠️ Tidak ada produk ditemukan. Jalankan ProductSeeder dulu.');
            return;
        }

        // Buat satu resep contoh
        Recipe::updateOrCreate(
            ['product_id' => $produk->id],
            [
                'branch_id' => 1,
                'name' => 'Resep ' . $produk->name,
                'description' => 'Racikan dasar untuk membuat ' . strtolower($produk->name) . '.',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ RecipeSeeder: 1 resep berhasil dibuat untuk produk "' . $produk->name . '".');
    }
}
