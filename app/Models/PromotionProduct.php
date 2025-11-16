<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionProduct extends Model
{
    use HasFactory;

    protected $table = 'promotion_products';

    protected $fillable = [
        'promotion_id',
        'product_id',
        'discount_value',
        'discount_type',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
    ];

    /**
     * Relasi ke Promotion
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Hitung diskon untuk produk ini
     * @param float $originalPrice Harga asli produk
     * @param int $quantity Jumlah produk
     * @return float
     */
    public function calculateDiscount(float $originalPrice, int $quantity = 1): float
    {
        // Jika ada diskon khusus di pivot table, gunakan itu
        if ($this->discount_value !== null && $this->discount_type !== null) {
            return match($this->discount_type) {
                'percentage' => $originalPrice * ($this->discount_value / 100) * $quantity,
                'fixed' => $this->discount_value * $quantity,
                default => 0,
            };
        }

        // Jika tidak ada override, gunakan diskon dari promotion utama
        if ($this->promotion) {
            return $this->promotion->calculateDiscount($originalPrice, $quantity);
        }

        return 0;
    }

    /**
     * Cek apakah promo untuk produk ini sedang aktif
     */
    public function isActive(): bool
    {
        return $this->promotion?->isCurrentlyActive() ?? false;
    }
}
