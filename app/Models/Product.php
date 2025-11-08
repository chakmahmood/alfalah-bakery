<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'category_id',
        'unit_id',
        'sku',
        'name',
        'slug',
        'type',
        'is_sellable',
        'is_active',
        'sell_price',
        'cost_price',
        'description',
        'image',
    ];

    protected $casts = [
        'is_sellable' => 'boolean',
        'is_active' => 'boolean',
        'sell_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            // auto SKU kalau belum diisi
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'product' ? 'Produk Jadi' : 'Bahan Baku';
    }
}
