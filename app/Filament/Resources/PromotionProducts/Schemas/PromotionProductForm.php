<?php

namespace App\Filament\Resources\PromotionProducts\Schemas;

use App\Models\Promotion;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PromotionProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | PROMO (HANYA YANG AKTIF)
                |--------------------------------------------------------------------------
                */
                Select::make('promotion_id')
                    ->label('Promo')
                    ->options(
                        Promotion::where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required()
                    ->reactive(),



                /*
                |--------------------------------------------------------------------------
                | PRODUK (FILTER BERDASARKAN CABANG PROMO)
                |--------------------------------------------------------------------------
                */
                Select::make('product_id')
                    ->label('Produk')
                    ->options(function (callable $get) {

                        $branchId = $get('../../branch_id');

                        // Jika tidak ada branch → tampil semua sellable
                        if (!$branchId) {
                            return Product::where('is_sellable', true)
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        }

                        // Jika branch ada → filter berdasarkan cabang
                        return Product::where('is_sellable', true)
                            ->whereHas(
                                'branches',
                                fn($q) =>
                                $q->where('branch_id', $branchId)
                            )
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),


                /*
                |--------------------------------------------------------------------------
                | DISKON (OPSIONAL)
                |--------------------------------------------------------------------------
                */
                Select::make('discount_type')
                    ->label('Tipe Diskon Override')
                    ->options([
                        'percentage' => 'Persentase (%)',
                        'fixed' => 'Nominal',
                    ])
                    ->nullable()
                    ->placeholder('Ikuti diskon utama promo'),

                TextInput::make('discount_value')
                    ->label('Nilai Diskon')
                    ->numeric()
                    ->nullable()
                    ->placeholder('Opsional'),
            ]);
    }
}
