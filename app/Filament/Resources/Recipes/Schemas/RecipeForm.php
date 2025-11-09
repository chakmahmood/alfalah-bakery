<?php

namespace App\Filament\Resources\Recipes\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Unit;
use Filament\Forms;

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
                    ->options(Branch::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->placeholder('Pilih cabang'),

                // 🍞 Produk Jadi
                Select::make('product_id')
                    ->label('Produk Jadi')
                    ->options(function (callable $get) {
                        $branchId = $get('branch_id');

                        // Jika belum pilih cabang, tampilkan semua produk jadi
                        if (!$branchId) {
                            return Product::where('type', 'product')
                                ->pluck('name', 'id');
                        }

                        // Produk milik cabang + produk global (branch_id null)
                        return Product::where('type', 'product')
                            ->where(function ($q) use ($branchId) {
                                $q->whereNull('branch_id')
                                  ->orWhere('branch_id', $branchId);
                            })
                            ->pluck('name', 'id');
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

                                if (!$branchId) {
                                    return Product::where('type', 'material')
                                        ->pluck('name', 'id');
                                }

                                // bahan milik cabang atau global
                                return Product::where('type', 'material')
                                    ->where(function ($q) use ($branchId) {
                                        $q->whereNull('branch_id')
                                          ->orWhere('branch_id', $branchId);
                                    })
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),

                        // Pilih satuan
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
