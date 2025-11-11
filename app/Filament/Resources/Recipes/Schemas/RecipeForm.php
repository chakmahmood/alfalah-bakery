<?php

namespace App\Filament\Resources\Recipes\Schemas;

use App\Models\Unit;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Branch;
use App\Models\Product;

class RecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // 📍 Cabang
                Select::make('branch_id')
                    ->label('Cabang')
                    ->options(Branch::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->placeholder('Pilih cabang'),

                // 🍞 Produk Jadi
                Select::make('product_id')
                    ->label('Produk Jadi')
                    ->options(function (callable $get) {
                        $branchId = $get('branch_id');

                        $query = Product::where('type', 'product')->where('is_active', true);

                        if ($branchId) {
                            $query->whereHas('branches', fn($q) => $q->where('branch_id', $branchId));
                        }

                        return $query->orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product) {
                                $set('name', 'Resep ' . $product->name);
                            }
                        }
                    })
                    ->placeholder('Pilih produk jadi'),

                // 🔖 Nama Resep / Produk Jadi
                TextInput::make('name')
                    ->label('Nama Resep / Produk Jadi')
                    ->required()
                    ->helperText('Otomatis terisi sesuai produk, bisa diubah manual'),

                // 📝 Deskripsi Resep
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Opsional: keterangan atau catatan resep')
                    ->columnSpanFull(),

                // 🔁 Status Aktif
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),

                // 🔹 Repeater untuk bahan-bahan resep
                Repeater::make('items')
                    ->label('Bahan Baku')
                    ->relationship('items')
                    ->columns(3)
                    ->minItems(1)
                    ->createItemButtonLabel('Tambah Bahan')
                    ->schema([
                        // Pilih bahan
                        Select::make('product_id')
                            ->label('Bahan / Produk')
                            ->options(function (callable $get) {
                                $branchId = $get('../../branch_id');

                                $query = Product::where('type', 'material')->where('is_active', true);

                                if ($branchId) {
                                    $query->whereHas('branches', fn($q) => $q->where('branch_id', $branchId));
                                }

                                return $query->orderBy('name')->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('unit_id', $product->unit_id);
                                    }
                                }
                            }),

                        Select::make('unit_id')
                        ->label('Satuan')
                        ->options(Unit::pluck('name', 'id'))
                        ->required(),

                        // Jumlah bahan
                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->helperText('Masukkan jumlah bahan baku yang dibutuhkan untuk resep ini'),
                    ]),
            ]);
    }
}
