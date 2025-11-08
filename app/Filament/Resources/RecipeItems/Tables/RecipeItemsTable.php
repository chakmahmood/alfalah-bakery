<?php

namespace App\Filament\Resources\RecipeItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Recipe;
use App\Models\Product;
use App\Models\Unit;

class RecipeItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan nama resep
                TextColumn::make('recipe.name')
                    ->label('Resep')
                    ->searchable()
                    ->sortable(),

                // Menampilkan nama produk / bahan
                TextColumn::make('product.name')
                    ->label('Bahan / Produk')
                    ->searchable()
                    ->sortable(),

                // Menampilkan satuan
                TextColumn::make('unit.name')
                    ->label('Satuan')
                    ->sortable(),

                // Jumlah bahan
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                // Tanggal dibuat
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Tanggal diubah
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Bisa ditambahkan filter per resep atau produk
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
