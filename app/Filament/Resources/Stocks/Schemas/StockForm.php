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
                Select::make('branch_id')
                    ->label('Cabang')
                    ->options(fn () => Branch::query()
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih cabang tempat stok disimpan'),

                Select::make('product_id')
                    ->label('Produk')
                    ->options(fn () => Product::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih produk'),

                TextInput::make('quantity')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->suffix('unit')
                    ->default(0)
                    ->required()
                    ->helperText('Jumlah stok saat ini di cabang tersebut.'),

                TextInput::make('min_stock')
                    ->label('Stok Minimum')
                    ->numeric()
                    ->suffix('unit')
                    ->default(0)
                    ->required()
                    ->helperText('Sistem akan menandai stok rendah jika di bawah angka ini.'),
            ]);
    }
}
