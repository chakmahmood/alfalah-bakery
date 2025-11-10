<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReturn extends Model
{
    /** @use HasFactory<\Database\Factories\StockReturnFactory> */
    use HasFactory;

    protected $fillable = [
        'from_branch_id',
        'to_branch_id',
        'return_type',
        'return_date',
        'status',
        'note',
        'user_id',
        'disposal_date',
    ];

    /**
     * Cabang yang mengembalikan barang
     */
    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    /**
     * Cabang tujuan (biasanya pusat)
     */
    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    /**
     * User yang membuat retur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Detail barang yang diretur
     */
    public function items()
    {
        return $this->hasMany(StockReturnItem::class);
    }

    /**
     * Apakah retur ini tipe buang (dispose)
     */
    public function isDispose()
    {
        return $this->return_type === 'dispose';
    }

    /**
     * Apakah retur ini sudah diterima
     */
    public function isReceived()
    {
        return $this->status === 'received';
    }
}
