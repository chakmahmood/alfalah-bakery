<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeItemFactory> */
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'product_id',
        'unit_id',
        'quantity',
    ];

    /**
     * Relasi ke header resep
     */
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Relasi ke produk (bahan baku)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
