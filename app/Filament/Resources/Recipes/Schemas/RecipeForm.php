<?php

namespace App\Filament\Resources\Recipes\Schemas;

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
            ->components([
                Select::make('branch_id')
                    ->label('Cabang Produksi')
                    ->options(Branch::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->helperText('Pilih cabang tempat resep ini digunakan.'),

                Select::make('product_id')
                    ->label('Produk Jadi')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->helperText('Produk hasil akhir dari resep ini.'),

                TextInput::make('name')
                    ->label('Nama Resep')
                    ->placeholder('Contoh: Resep Kopi Susu Gula Aren')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi / Catatan')
                    ->placeholder('Tuliskan deskripsi singkat atau langkah pembuatan...')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Resep Aktif')
                    ->default(true)
                    ->helperText('Nonaktifkan jika resep ini sudah tidak digunakan.'),
            ]);
    }
}
