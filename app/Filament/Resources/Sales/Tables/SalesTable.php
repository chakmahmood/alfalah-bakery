<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Branch;
use App\Models\User;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable(),

                TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->sortable(),

                TextColumn::make('paymentMethod.name')
                    ->label('Metode Bayar')
                    ->sortable(),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('discount')
                    ->label('Diskon')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('tax')
                    ->label('Pajak')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'partial' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'partial' => 'Belum Lunas / DP',
                        'paid' => 'Lunas',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst($state),
                    })
                    ->sortable(),


                TextColumn::make('sale_date')
                    ->label('Tanggal Penjualan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->options(fn() => Branch::pluck('name', 'id')->toArray()),

                SelectFilter::make('user_id')
                    ->label('Kasir')
                    ->options(fn() => User::pluck('name', 'id')->toArray()),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'partial' => 'Belum Lunas',
                        'paid' => 'Lunas',
                        'cancelled' => 'Dibatalkan',
                    ]),
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
