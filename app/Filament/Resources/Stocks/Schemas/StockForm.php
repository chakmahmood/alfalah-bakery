<?php

namespace App\Filament\Resources\Stocks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Branch;
use App\Models\Product;

class StockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                // Pilih cabang
                Select::make('branch_id')
                    ->label('Cabang')
                    ->options(fn () => Branch::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih cabang tempat stok disimpan'),

                // Pilih produk
                Select::make('product_id')
                    ->label('Produk')
                    ->options(fn () => Product::where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($set, $state) => $set('unit_id', Product::find($state)?->unit_id)),

                // Jumlah stok
                TextInput::make('quantity')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->required()
                    ->suffix(fn ($get) => Product::find($get('product_id'))?->unit?->symbol ?? null)
                    ->helperText('Jumlah stok saat ini di cabang tersebut.'),

                // Stok minimum
                TextInput::make('min_stock')
                    ->label('Stok Minimum')
                    ->numeric()
                    ->required()
                    ->suffix(fn ($get) => Product::find($get('product_id'))?->unit?->symbol ?? null)
                    ->helperText('Sistem akan menandai stok rendah jika di bawah angka ini.'),

                // unit_id (hidden, otomatis)
                Select::make('unit_id')
                    ->label('Satuan')
                    ->options(fn () => Product::where('is_active', true)->pluck('unit.name', 'unit.id'))
                    ->hidden()
            ]);
    }
}
