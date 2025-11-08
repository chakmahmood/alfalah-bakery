<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockMovement;
use App\Models\Branch;
use App\Models\Product;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();

        StockMovement::factory()->create([
            'branch_id' => $branch->id ?? 1,
            'product_id' => $product->id ?? 1,
            'type' => 'in',
            'quantity' => 50,
            'reference' => 'PURCHASE-001',
            'note' => 'Pembelian awal stok produk',
        ]);

        StockMovement::factory()->create([
            'branch_id' => $branch->id ?? 1,
            'product_id' => $product->id ?? 1,
            'type' => 'out',
            'quantity' => 10,
            'reference' => 'SALE-001',
            'note' => 'Penjualan produk ke pelanggan',
        ]);
    }
}
