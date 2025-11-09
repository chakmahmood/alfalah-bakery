<?php

namespace App\Filament\Resources\StockMovements\Tables;

use App\Models\Branch;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
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

                TextColumn::make('product.unit.symbol')
                    ->label('Unit')
                    ->sortable()
                    ->default('-'),

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
                 // 🏪 Filter berdasarkan cabang
                SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->options(
                        Branch::orderBy('name')->pluck('name', 'id')
                    )
                    ->placeholder('Semua Cabang'),

                // ⚙️ Filter berdasarkan tipe pergerakan
                SelectFilter::make('type')
                    ->label('Tipe Pergerakan')
                    ->options([
                        'in' => 'Masuk (IN)',
                        'out' => 'Keluar (OUT)',
                        'transfer' => 'Transfer',
                        'adjustment' => 'Penyesuaian',
                        'production' => 'Produksi',
                        'return' => 'Retur',
                    ])
                    ->placeholder('Semua Tipe'),
                Filter::make('created_at_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                    ], FiltersLayout::AboveContent)
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
