<?php

namespace App\Filament\Resources\Recipes\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class RecipesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable()
                    ->toggleable()
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('product.name')
                    ->label('Produk Jadi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nama Resep')
                    ->searchable()
                    ->description(fn ($record) => $record->description ? str($record->description)->limit(50) : null),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Bisa ditambah nanti: filter cabang / status aktif
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Resep Terpilih')
                        ->icon('heroicon-m-trash'),
                ]),
            ]);
    }
}
