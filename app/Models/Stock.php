<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
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

    /** Helper untuk cek stok aman atau tidak */
    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->min_stock;
    }
}
