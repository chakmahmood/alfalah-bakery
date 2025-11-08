<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @use HasFactory<\Database\Factories\SettingFactory> */
    use HasFactory;
    protected $fillable = [
        'store_name',
        'store_address',
        'store_phone',
        'store_email',
        'tax_rate',
        'printer_name',
        'currency_symbol',
        'logo_path',
        'is_active',
    ];
}
