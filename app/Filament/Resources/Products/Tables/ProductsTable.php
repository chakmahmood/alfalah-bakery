<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Branch;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40),

                TextColumn::make('name')
                    ->label('Nama Produk / Bahan')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('unit.name')
                    ->label('Satuan')
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'success' => 'product',
                        'warning' => 'material',
                    ])
                    ->formatStateUsing(fn (string $state) => $state === 'product' ? 'Produk Jadi' : 'Bahan Baku'),

                IconColumn::make('is_sellable')
                    ->label('Jual')
                    ->boolean()
                    ->tooltip(fn ($state) => $state ? 'Dijual di kasir' : 'Tidak dijual'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('sell_price')
                    ->label('Harga Jual')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '.')
                    ->prefix('Rp ')
                    ->sortable(),

                TextColumn::make('cost_price')
                    ->label('Harga Modal')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '.')
                    ->prefix('Rp ')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->default('-')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->options(
                        Branch::orderBy('name')->pluck('name', 'id')
                    )
                    ->placeholder('Semua Cabang'),

                SelectFilter::make('type')
                    ->label('Tipe Produk')
                    ->options([
                        'product' => 'Produk Jadi',
                        'material' => 'Bahan Baku',
                    ]),
                    ],FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
