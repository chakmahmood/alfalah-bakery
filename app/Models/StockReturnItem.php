<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReturnItem extends Model
{
    /** @use HasFactory<\Database\Factories\StockReturnItemFactory> */
    use HasFactory;

    protected $fillable = [
        'stock_return_id',
        'product_id',
        'unit_id',
        'quantity',
        'cost_price',
    ];

    /**
     * Relasi ke StockReturn (induk retur)
     */
    public function stockReturn()
    {
        return $this->belongsTo(StockReturn::class);
    }

    /**
     * Relasi ke produk yang diretur
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke satuan produk (opsional)
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
