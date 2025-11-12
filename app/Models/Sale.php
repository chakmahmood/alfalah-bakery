<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'branch_id',
        'user_id',
        'payment_method_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'note',
        'sale_date',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // 🔗 Relasi
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    // 💡 Helper: total pembayaran masuk (untuk DP & pelunasan)
    public function getPaidAmountAttribute()
    {
        return $this->payments()
            ->where('status', 'confirmed')
            ->sum('amount');
    }

    // 💡 Helper: sisa tagihan
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->total - $this->paid_amount);
    }

    // 💡 Helper: status otomatis
    public function getIsFullyPaidAttribute()
    {
        return $this->paid_amount >= $this->total;
    }
}
