<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $products = Product::all();

        foreach ($branches as $branch) {
            foreach ($products as $product) {
                Stock::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => rand(10, 100),
                        'min_stock' => 5,
                    ]
                );
            }
        }
    }
}
