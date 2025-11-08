<?php

namespace App\Filament\Resources\RecipeItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Recipe;
use App\Models\Product;
use App\Models\Unit;

class RecipeItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2) // Form dua kolom
            ->components([

                // 🔖 Pilih Resep
                Select::make('recipe_id')
                    ->label('Resep')
                    ->options(Recipe::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih resep'),

                // 📦 Pilih Bahan / Produk
                Select::make('product_id')
                    ->label('Bahan / Produk')
                    ->options(Product::where('type', 'material')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih bahan baku'),

                // 🧮 Satuan
                Select::make('unit_id')
                    ->label('Satuan')
                    ->options(Unit::pluck('name', 'id'))
                    ->required()
                    ->placeholder('Pilih satuan'),

                // 🔢 Jumlah
                TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->helperText('Masukkan jumlah bahan baku yang dibutuhkan untuk resep ini'),
            ]);
    }
}
