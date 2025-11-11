<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ekonomis',
                'slug' => 'ekonomis',
                'description' => 'Kategori produk dengan harga ekonomis',
                'is_active' => true,
            ],
            [
                'name' => 'Medium',
                'slug' => 'medium',
                'description' => 'Kategori produk dengan harga menengah',
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Kategori produk dengan harga premium',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
