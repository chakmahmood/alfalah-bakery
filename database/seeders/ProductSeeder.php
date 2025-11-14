<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        // Generate 1000 produk & bahan baku
        Product::factory(1000)->create()->each(function ($product) use ($branches) {
            // Attach 1-2 branch random via pivot
            $productBranches = $branches->random(rand(1, 2))->pluck('id');
            $product->branches()->attach($productBranches);
        });
    }
}
