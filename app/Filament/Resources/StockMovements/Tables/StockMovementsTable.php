<?php

namespace App\Filament\Resources\StockMovements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 🏢 Cabang
                TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable()
                    ->searchable(),

                // 📦 Produk
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->sortable()
                    ->searchable(),

                // 🔁 Jenis Pergerakan (dengan badge warna)
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->colors([
                        'success' => 'in',          // hijau untuk stok masuk
                        'danger' => 'out',           // merah untuk stok keluar
                        'info' => 'transfer',        // biru untuk transfer
                        'warning' => 'adjustment',   // kuning untuk penyesuaian
                        'gray' => 'production',      // abu untuk produksi
                        'secondary' => 'return',     // ungu untuk retur
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'in' => 'Masuk (IN)',
                        'out' => 'Keluar (OUT)',
                        'transfer' => 'Transfer',
                        'adjustment' => 'Penyesuaian',
                        'production' => 'Produksi',
                        'return' => 'Retur',
                        default => ucfirst($state),
                    }),

                // 🔢 Jumlah
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                // 🧾 Referensi
                TextColumn::make('reference')
                    ->label('Referensi')
                    ->searchable(),

                // 🕒 Waktu Transaksi
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->icon('heroicon-o-trash'),
                ]),
            ]);
    }
}
