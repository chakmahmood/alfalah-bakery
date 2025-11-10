<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockReturn;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class StockReturnService
{
    /**
     * Saat retur dikirim dari cabang (barang diambil atau dibuang)
     * - Semua tipe retur mengurangi stok cabang asal
     * - Jika to_stock → barang dikirim ke cabang tujuan
     * - Jika dispose → barang dibuang
     */
    public function handleSent(StockReturn $return): void
    {
        DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                $fromStock = Stock::firstOrCreate(
                    [
                        'branch_id' => $return->from_branch_id,
                        'product_id' => $item->product_id,
                    ],
                    [
                        'unit_id' => $item->unit_id ?? $item->product->unit_id,
                        'quantity' => 0,
                        'min_stock' => 0,
                    ]
                );

                // Validasi stok cukup
                if ($fromStock->quantity < $item->quantity) {
                    throw new Exception("Stok untuk {$item->product->name} tidak mencukupi di cabang asal.");
                }

                // Kurangi stok untuk semua tipe retur
                $fromStock->decrement('quantity', $item->quantity);

                // Log pergerakan keluar (barang diretur / dibuang)
                StockMovement::create([
                    'branch_id' => $return->from_branch_id,
                    'product_id' => $item->product_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'reference' => "Return #{$return->id}",
                    'note' => $return->return_type === 'to_stock'
                        ? "Retur dikirim ke cabang {$return->toBranch->name}"
                        : "Barang dibuang / dispose dari cabang {$return->fromBranch->name}",
                ]);
            }

            $return->update(['status' => 'sent']);
        });
    }

    /**
     * Saat retur diterima oleh pusat / cabang tujuan
     * - Jika to_stock → stok cabang tujuan bertambah
     * - Jika dispose → hanya log, tidak menambah stok
     */
    public function handleReceived(StockReturn $return): void
    {
        if ($return->status !== 'sent') {
            throw new Exception("Retur belum dikirim, tidak bisa diterima.");
        }

        DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                if ($return->return_type === 'to_stock') {
                    $toStock = Stock::firstOrCreate(
                        [
                            'branch_id' => $return->to_branch_id,
                            'product_id' => $item->product_id,
                        ],
                        [
                            'unit_id' => $item->unit_id ?? $item->product->unit_id,
                            'quantity' => 0,
                            'min_stock' => 0,
                        ]
                    );

                    $toStock->increment('quantity', $item->quantity);

                    StockMovement::create([
                        'branch_id' => $return->to_branch_id,
                        'product_id' => $item->product_id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'reference' => "Return #{$return->id}",
                        'note' => "Retur diterima oleh cabang {$return->toBranch->name}",
                    ]);
                } else {
                    // Barang dibuang / rusak
                    StockMovement::create([
                        'branch_id' => $return->to_branch_id ?? $return->from_branch_id,
                        'product_id' => $item->product_id,
                        'type' => 'dispose',
                        'quantity' => $item->quantity,
                        'reference' => "Return #{$return->id}",
                        'note' => "Barang rusak / kadaluarsa (dispose)",
                    ]);
                }
            }

            $return->update([
                'status' => 'received',
                'disposal_date' => $return->return_type === 'dispose' ? now() : null,
            ]);
        });
    }

    /**
     * Membatalkan retur (rollback)
     * - Menghapus log stock_movement
     * - Mengembalikan stok asal
     */
    public function rollback(StockReturn $return): void
    {
        DB::transaction(function () use ($return) {
            // Jika retur sudah dikirim, kembalikan stok asal
            if ($return->status === 'sent') {
                foreach ($return->items as $item) {
                    $fromStock = Stock::where('branch_id', $return->from_branch_id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($fromStock) {
                        $fromStock->increment('quantity', $item->quantity);
                    }
                }
            }

            // Hapus log pergerakan
            StockMovement::where('reference', "Return #{$return->id}")->delete();

            $return->update(['status' => 'draft']);
        });
    }
}
