<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();
        $unitPcs = Unit::firstOrCreate(['name' => 'Pcs'], ['symbol' => 'pcs']);
        $unitGram = Unit::firstOrCreate(['name' => 'Gram'], ['symbol' => 'g']);

        // Pastikan kategori sudah ada
        $categoryRoti = Category::firstOrCreate(
            ['name' => 'Roti'],
            ['slug' => 'roti', 'is_active' => true]
        );
        $categoryBahan = Category::firstOrCreate(
            ['name' => 'Bahan Baku'],
            ['slug' => 'bahan-baku', 'is_active' => true]
        );

        // 🧁 Produk Jadi (Sellable)
        $products = [
            [
                'name' => 'Roti Coklat',
                'category_id' => $categoryRoti->id,
                'unit_id' => $unitPcs->id,
                'type' => 'product',
                'is_sellable' => true,
                'sell_price' => 8000,
                'cost_price' => 4000,
                'description' => 'Roti lembut isi coklat premium.',
                'image' => 'seeders/products/roti-coklat.jpg',
            ],
            [
                'name' => 'Roti Keju',
                'category_id' => $categoryRoti->id,
                'unit_id' => $unitPcs->id,
                'type' => 'product',
                'is_sellable' => true,
                'sell_price' => 9000,
                'cost_price' => 4500,
                'description' => 'Roti isi keju meleleh, favorit pelanggan.',
                'image' => 'seeders/products/roti-keju.jpg',
            ],
        ];

        // 🍞 Bahan Baku (Non-sellable)
        $materials = [
            [
                'name' => 'Tepung Terigu',
                'category_id' => $categoryBahan->id,
                'unit_id' => $unitGram->id,
                'type' => 'material',
                'is_sellable' => false,
                'sell_price' => 0,
                'cost_price' => 15000,
                'description' => 'Bahan utama pembuatan roti.',
                'image' => null,
            ],
            [
                'name' => 'Gula Pasir',
                'category_id' => $categoryBahan->id,
                'unit_id' => $unitGram->id,
                'type' => 'material',
                'is_sellable' => false,
                'sell_price' => 0,
                'cost_price' => 12000,
                'description' => 'Bahan pemanis roti.',
                'image' => null,
            ],
        ];

        foreach (array_merge($products, $materials) as $data) {
            Product::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'branch_id' => $branch?->id,
                    'slug' => Str::slug($data['name']),
                    'is_active' => true,
                    'sku' => strtoupper(Str::random(6)),
                ])
            );
        }
    }
}
