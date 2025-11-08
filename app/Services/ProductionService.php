<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductionService
{
    /**
     * Proses produksi suatu resep di cabang tertentu
     *
     * @param int $recipeId ID resep
     * @param int $branchId ID cabang tempat produksi
     * @param int $jumlahProduksi Jumlah produk jadi yang akan dibuat
     * @return void
     * @throws Exception
     */
    public static function produce(int $recipeId, int $branchId, int $jumlahProduksi): void
    {
        DB::transaction(function () use ($recipeId, $branchId, $jumlahProduksi) {

            $recipe = Recipe::with('items.product.unit', 'product.unit')->findOrFail($recipeId);

            if ($jumlahProduksi <= 0) {
                throw new Exception('Jumlah produksi harus lebih besar dari 0.');
            }

            $reference = "PRODUCTION-{$recipeId}-" . now()->format('Ymd');

            // 🔻 Kurangi stok bahan baku
            foreach ($recipe->items as $item) {
                $totalBahan = $item->quantity * $jumlahProduksi;

                // ✅ Konversi satuan resep ke satuan stok bahan baku
                if ($item->unit_id !== $item->product->unit_id) {
                    $totalBahan = UnitConversionService::convert(
                        $totalBahan,
                        fromUnitId: $item->unit_id,
                        toUnitId: $item->product->unit_id
                    );
                }

                // Ambil stok bahan baku
                $stockBahan = Stock::firstOrCreate(
                    ['branch_id' => $branchId, 'product_id' => $item->product_id],
                    ['quantity' => 0]
                );

                // Pastikan stok cukup
                if ($stockBahan->quantity < $totalBahan) {
                    throw new Exception(
                        "Stok bahan baku '{$item->product->name}' tidak mencukupi. " .
                        "Dibutuhkan: $totalBahan {$item->product->unit->symbol}, " .
                        "Tersedia: {$stockBahan->quantity} {$item->product->unit->symbol}"
                    );
                }

                // Kurangi stok bahan baku
                StockService::move(
                    type: 'out',
                    branchId: $branchId,
                    productId: $item->product_id,
                    quantity: $totalBahan,
                    reference: $reference,
                    note: "Bahan baku untuk produksi {$jumlahProduksi} {$recipe->product->name}"
                );
            }

            // 🔺 Tambah stok produk jadi
            StockService::move(
                type: 'production',
                branchId: $branchId,
                productId: $recipe->product_id,
                quantity: $jumlahProduksi,
                reference: $reference,
                note: "Produk jadi dari resep '{$recipe->name}'"
            );
        });
    }
}
