<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Promotion extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'name',
        'type',
        'value',
        'start_date',
        'end_date',
        'is_active',
    ];

    // Cast tipe data otomatis
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

    /**
     * Scope untuk promo yang aktif saat ini
     */
    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where('is_active', true)
                     ->where('start_date', '<=', $today)
                     ->where('end_date', '>=', $today);
    }

    /**
     * Cek apakah promo sedang berlaku
     */
    public function isCurrentlyActive(): bool
    {
        $today = Carbon::today();
        return $this->is_active && $this->start_date <= $today && $this->end_date >= $today;
    }

    /**
     * Helper untuk menghitung diskon dari harga asli
     */
    public function calculateDiscount(float $originalPrice, int $quantity = 1): float
    {
        if (!$this->isCurrentlyActive()) {
            return 0;
        }

        return match($this->type) {
            'percentage' => $originalPrice * ($this->value / 100) * $quantity,
            'fixed' => $this->value * $quantity,
            'buy_x_get_y' => $this->calculateBuyXGetY($quantity),
            default => 0,
        };
    }

    /**
     * Helper khusus promo Buy X Get Y
     * Asumsi: $this->value = jumlah free item per X item (misal Buy 2 Get 1 => value=1)
     */
    protected function calculateBuyXGetY(int $quantity): float
    {
        // Misal kita simulasikan diskon sebagai free item, harga aslinya harus dari relasi Product
        // Untuk model ini kita kembalikan jumlah free item saja, harga dihitung di service/product
        return floor($quantity / ($this->value + 1)) * $this->value;
    }

    /**
     * Relasi ke produk (banyak ke banyak)
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_products', 'promotion_id', 'product_id')
            ->withPivot('discount_value', 'discount_type')
            ->withTimestamps();
    }
}
