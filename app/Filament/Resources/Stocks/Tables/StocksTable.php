<?php

namespace App\Filament\Resources\Stocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('product.name')
                    ->label('Produk')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('quantity')
                    ->label('Stok')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->color(fn ($record) =>
                        $record->quantity <= $record->min_stock ? 'danger' : 'success'
                    )
                    ->tooltip(fn ($record) =>
                        $record->quantity <= $record->min_stock
                            ? 'Stok menipis — perlu restok segera'
                            : 'Stok aman'
                    ),

                TextColumn::make('min_stock')
                    ->label('Stok Minimum')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->since() // tampil "2 jam lalu"
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus'),
                ]),
            ]);
    }
}
