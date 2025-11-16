<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
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

    protected $appends = ['image_url'];

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

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_product');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // di Product.php
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_products', 'product_id', 'promotion_id')
            ->withPivot('discount_type', 'discount_value')
            ->withTimestamps();
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

    // Product.php
    public function stockForBranch($branchId)
    {
        return Stock::where('product_id', $this->id)
            ->where('branch_id', $branchId)
            ->first();
    }
    public function stockQuantityForBranch($branchId)
    {
        return $this->stocks()
            ->where('branch_id', $branchId)
            ->value('quantity') ?? 0;
    }

    public function getImageUrlAttribute()
    {
        $filename = basename($this->image ?? 'default.jpg');

        // Jika file tidak ada di storage, pakai default
        if (!\Storage::disk('public')->exists("products/{$filename}")) {
            return asset('storage/products/default.jpg');
        }

        return asset("storage/products/{$filename}");
    }

}
