<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTransferService
{
    /**
     * Saat transfer dikirim:
     * - Kurangi stok cabang asal
     * - Buat log stock_movement type 'transfer' (keluar)
     */
    public function handleSent(StockTransfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                $fromStock = Stock::firstOrCreate(
                    [
                        'branch_id' => $transfer->from_branch_id,
                        'product_id' => $item->product_id,
                    ],
                    [
                        'unit_id' => $item->product->unit_id,
                        'quantity' => 0,
                        'min_stock' => 0,
                    ]
                );

                if ($fromStock->quantity < $item->quantity) {
                    throw new Exception("Stok untuk {$item->product->name} tidak mencukupi di cabang asal.");
                }

                // Kurangi stok cabang asal
                $fromStock->decrement('quantity', $item->quantity);

                // Catat pergerakan stok keluar
                StockMovement::create([
                    'branch_id' => $transfer->from_branch_id,
                    'product_id' => $item->product_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'reference' => "Transfer #{$transfer->id}",
                    'note' => "Kirim ke cabang {$transfer->toBranch->name}",
                ]);
            }

            // Ganti status
            $transfer->update(['status' => 'sent']);
        });
    }


    /**
     * Saat transfer diterima:
     * - Tambahkan stok di cabang tujuan
     * - Catat pergerakan stok type 'transfer' (masuk)
     */
    public function handleReceived(StockTransfer $transfer): void
    {
        if ($transfer->status !== 'sent') {
            throw new Exception("Transfer belum dikirim, tidak bisa diterima.");
        }

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                $toStock = Stock::firstOrCreate(
                    [
                        'branch_id' => $transfer->to_branch_id,
                        'product_id' => $item->product_id,
                    ],
                    [
                        'unit_id' => $item->product->unit_id,
                        'quantity' => 0,
                        'min_stock' => 0,
                    ]
                );

                $toStock->increment('quantity', $item->quantity);

                StockMovement::create([
                    'branch_id' => $transfer->to_branch_id,
                    'product_id' => $item->product_id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'reference' => "Transfer #{$transfer->id}",
                    'note' => "Diterima dari cabang {$transfer->fromBranch->name}",
                ]);
            }

            $transfer->update(['status' => 'received']);
        });
    }


    /**
     * Jika transfer dibatalkan atau dikembalikan ke draft:
     * - Kembalikan stok asal (jika sudah dikirim)
     * - Hapus log pergerakan stok terkait
     */
    public function rollback(StockTransfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            // Jika status sebelumnya "sent", kembalikan stok asal
            if ($transfer->status === 'sent') {
                foreach ($transfer->items as $item) {
                    $fromStock = Stock::where('branch_id', $transfer->from_branch_id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($fromStock) {
                        $fromStock->increment('quantity', $item->quantity);
                    }
                }
            }

            // Hapus log pergerakan stok yang terkait dengan transfer ini
            StockMovement::where('reference', "Transfer #{$transfer->id}")
                ->delete();

            $transfer->update(['status' => 'draft']);
        });
    }
}
