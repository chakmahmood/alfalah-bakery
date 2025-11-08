<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class StockService
{
    /**
     * Mencatat dan mengelola pergerakan stok produk.
     *
     * @param string $type Jenis pergerakan stok (in, out, transfer, adjustment, production, return)
     * @param int $branchId Cabang asal
     * @param int $productId Produk
     * @param float $quantity Jumlah stok
     * @param string|null $reference Referensi transaksi (contoh: SALE-101)
     * @param string|null $note Catatan tambahan
     * @param int|null $targetBranchId Cabang tujuan (untuk transfer)
     *
     * @return bool
     * @throws Exception
     */
    public static function move(
        string $type,
        int $branchId,
        int $productId,
        float $quantity,
        ?string $reference = null,
        ?string $note = null,
        ?int $targetBranchId = null
    ): bool {
        try {
            return DB::transaction(function () use (
                $type,
                $branchId,
                $productId,
                $quantity,
                $reference,
                $note,
                $targetBranchId
            ) {
                if ($quantity <= 0) {
                    throw new Exception('Jumlah stok harus lebih besar dari 0.');
                }

                // Ambil stok di cabang asal
                $stock = Stock::firstOrCreate(
                    ['branch_id' => $branchId, 'product_id' => $productId],
                    ['quantity' => 0]
                );

                switch ($type) {
                    case 'in':
                    case 'return':
                    case 'production':
                        $stock->quantity += $quantity;
                        break;

                    case 'out':
                        if ($stock->quantity < $quantity) {
                            throw new Exception('Stok tidak mencukupi untuk pengeluaran.');
                        }
                        $stock->quantity -= $quantity;
                        break;

                    case 'adjustment':
                        $stock->quantity += $quantity;
                        break;

                    case 'transfer':
                        // Validasi stok asal
                        if ($stock->quantity < $quantity) {
                            throw new Exception('Stok tidak cukup untuk transfer.');
                        }

                        // Kurangi stok cabang asal
                        $stock->quantity -= $quantity;
                        $stock->save();

                        // Validasi cabang tujuan
                        if (!$targetBranchId) {
                            throw new Exception('Cabang tujuan transfer harus diisi.');
                        }

                        // Tambahkan stok ke cabang tujuan
                        $targetStock = Stock::firstOrCreate(
                            ['branch_id' => $targetBranchId, 'product_id' => $productId],
                            ['quantity' => 0]
                        );
                        $targetStock->quantity += $quantity;
                        $targetStock->save();

                        // Catat kedua pergerakan (asal dan tujuan)
                        StockMovement::create([
                            'branch_id' => $branchId,
                            'product_id' => $productId,
                            'type' => 'transfer',
                            'quantity' => -$quantity,
                            'reference' => $reference,
                            'note' => $note ?? 'Transfer ke cabang ID: ' . $targetBranchId,
                        ]);

                        StockMovement::create([
                            'branch_id' => $targetBranchId,
                            'product_id' => $productId,
                            'type' => 'transfer',
                            'quantity' => $quantity,
                            'reference' => $reference,
                            'note' => $note ?? 'Transfer dari cabang ID: ' . $branchId,
                        ]);

                        return true;

                    default:
                        throw new Exception("Tipe pergerakan stok tidak dikenal: $type");
                }

                // Simpan stok hasil perhitungan
                $stock->save();

                // Catat log pergerakan stok
                StockMovement::create([
                    'branch_id' => $branchId,
                    'product_id' => $productId,
                    'type' => $type,
                    'quantity' => $quantity,
                    'reference' => $reference,
                    'note' => $note,
                ]);

                return true;
            });
        } catch (Exception $e) {
            // Tangani error dan lempar ulang agar bisa ditangkap di level atas
            throw new Exception("Gagal memproses pergerakan stok: " . $e->getMessage());
        }
    }
}
