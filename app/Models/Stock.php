<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_id',
        'unit_id',
        'quantity',
        'min_stock',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'min_stock' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Helper cek stok rendah */
    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->min_stock;
    }

    public static function addOrUpdateStock($branchId, $productId, $quantity, $minStock)
    {
        // Cek apakah stok untuk cabang + produk sudah ada
        $stock = self::where('branch_id', $branchId)
            ->where('product_id', $productId)
            ->first();

        if ($stock) {
            // Update stok
            $stock->update([
                'quantity' => $quantity,
                'min_stock' => $minStock,
            ]);

            return [
                'success' => true,
                'message' => 'Stok berhasil diperbarui.',
                'stock' => $stock,
            ];
        }

        // Buat stok baru
        $stock = self::create([
            'branch_id' => $branchId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'min_stock' => $minStock,
        ]);

        return [
            'success' => true,
            'message' => 'Stok berhasil ditambahkan.',
            'stock' => $stock,
        ];
    }

}
