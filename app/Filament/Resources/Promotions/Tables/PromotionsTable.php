<?php

namespace App\Filament\Resources\Promotions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Promo')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('type')
                    ->label('Tipe Promo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'percentage' => 'primary',
                        'fixed' => 'success',
                        'buy_x_get_y' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'percentage' => 'Persentase (%)',
                        'fixed' => 'Nominal Tetap',
                        'buy_x_get_y' => 'Beli X Gratis Y',
                        default => ucfirst($state),
                    })
                    ->sortable(),


                TextColumn::make('value')
                    ->label('Nilai Diskon / Free Item')
                    ->numeric()
                    ->formatStateUsing(fn($state, $record) => match ($record->type) {
                        'percentage' => $state . '%',
                        'fixed' => 'Rp ' . number_format($state, 0, ',', '.'),
                        'buy_x_get_y' => $state . ' item',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Mulai Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Sampai Tanggal')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Tipe Promo')
                    ->options([
                        'percentage' => 'Persentase',
                        'fixed' => 'Nominal Tetap',
                        'buy_x_get_y' => 'Beli X Gratis Y',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
