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

class RecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1) // Form utama satu kolom
            ->components([
                // 📍 Cabang
                Select::make('branch_id')
                    ->label('Cabang')
                    ->options(Branch::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih cabang'),

                // 🔖 Nama Resep / Produk Jadi
                TextInput::make('name')
                    ->label('Nama Resep / Produk Jadi')
                    ->required(),

                // 📝 Deskripsi Resep
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Opsional: keterangan atau catatan resep')
                    ->columnSpanFull(),

                // 🔁 Status Aktif
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),

                // 🔹 Repeater untuk bahan-bahan resep
                Repeater::make('items')
                    ->label('Bahan Baku')
                    ->relationship('items') // Hubungkan ke RecipeItem
                    ->columns(3)
                    ->minItems(1)
                    ->createItemButtonLabel('Tambah Bahan')
                    ->schema([
                        // Pilih bahan
                        Select::make('product_id')
                            ->label('Bahan / Produk')
                            ->options(Product::where('type', 'material')->pluck('name', 'id'))
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
