<?php

namespace App\Filament\Resources\Stocks\Tables;

use App\Models\Branch;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
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
                    ->formatStateUsing(fn ($state, $record) =>
                        number_format($state) . ' ' . optional($record->product->unit)->symbol
                    )
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
                    ->formatStateUsing(fn ($state, $record) =>
                        number_format($state) . ' ' . optional($record->product->unit)->symbol
                    )
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->since() // tampil "2 jam lalu"
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
             // 🏪 Filter berdasarkan cabang
                SelectFilter::make('branch_id')
    ->label('Cabang')
    ->options(Branch::pluck('name', 'id'))
    ->placeholder('Semua Cabang')
    ->query(fn ($query, $state) => $state ? $query->where('branch_id', $state) : null),

                // ⚙️ Filter berdasarkan tipe produk (join relasi product)
                SelectFilter::make('product_type')
                    ->label('Tipe Produk')
                    ->options([
                        'product' => 'Produk Jadi',
                        'material' => 'Bahan Baku',
                    ])
                    ->placeholder('Semua Tipe')
                    ->query(function ($query, $state) {
                        if ($state) {
                            $query->whereHas('product', fn ($q) =>
                                $q->where('type', $state)
                            );
                        }
                    }),
                    ],FiltersLayout::AboveContent)
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
