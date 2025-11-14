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
                    ->options(fn() => Branch::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive(),

                // Pilih produk (hanya yang tersedia di cabang)
                Select::make('product_id')
                    ->label('Produk')
                    ->options(function ($get) {
                        $branchId = $get('branch_id');
                        if (!$branchId)
                            return [];

                        return Product::where('is_active', true)
                            ->whereHas('branches', fn($q) => $q->where('branch_id', $branchId))
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state) {
                            $product = Product::find($state);
                            $set('unit_id', $product?->unit_id);
                        } else {
                            $set('unit_id', null);
                        }
                    })
                    ->rule(function ($get) {
                        $branchId = $get('branch_id');
                        $productId = $get('product_id');

                        return function ($attribute, $value, $fail) use ($branchId, $productId, $get) {
                            if ($branchId && $productId) {
                                $query = \App\Models\Stock::where('branch_id', $branchId)
                                    ->where('product_id', $productId);

                                // Abaikan record yang sedang diedit
                                if ($get('id')) {
                                    $query->where('id', '!=', $get('id'));
                                }

                                if ($query->exists()) {
                                    $fail('Stok untuk cabang dan produk ini sudah ada.');
                                }
                            }
                        };

                    }),


                // Jumlah stok
                TextInput::make('quantity')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->required()
                    ->suffix(fn($get) => Product::find($get('product_id'))?->unit?->symbol ?? null)
                    ->helperText('Jumlah stok saat ini di cabang tersebut.'),

                // Stok minimum
                TextInput::make('min_stock')
                    ->label('Stok Minimum')
                    ->numeric()
                    ->required()
                    ->suffix(fn($get) => Product::find($get('product_id'))?->unit?->symbol ?? null)
                    ->helperText('Sistem akan menandai stok rendah jika di bawah angka ini.'),

                // unit_id hidden
                TextInput::make('unit_id')
                    ->hidden()
                    ->required()
                    ->dehydrated()
                    ->afterStateHydrated(function ($component, $state, $get, $record) {
                        // $record hanya tersedia saat edit
                        if (!$state) {
                            if ($get('product_id')) {
                                $component->state(Product::find($get('product_id'))?->unit_id);
                            } elseif ($record) {
                                // fallback: ambil unit dari record yang sedang diedit
                                $component->state($record->unit_id);
                            }
                        }
                    }),

            ]);
    }
}
