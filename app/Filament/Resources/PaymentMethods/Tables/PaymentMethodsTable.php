<?php

namespace App\Filament\Resources\PaymentMethods\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentMethodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 💳 Nama metode
                TextColumn::make('name')
                    ->label('Metode Pembayaran')
                    ->searchable()
                    ->sortable(),

                // 🔤 Kode internal
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->searchable(),

                // 🏷️ Jenis metode (badge warna berbeda)
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cash' => 'success',
                        'bank_transfer' => 'info',
                        'card' => 'warning',
                        'e_wallet' => 'primary',
                        'virtual_account' => 'purple',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'bank_transfer' => 'Transfer Bank',
                        'card' => 'Kartu',
                        'e_wallet' => 'E-Wallet',
                        'virtual_account' => 'Virtual Account',
                        'other' => 'Lainnya',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                // ✅ Status aktif/tidak
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                // 🕒 Tanggal dibuat
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 🔄 Tanggal update
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            // 🎛️ Filter tipe metode
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'cash' => 'Tunai',
                        'bank_transfer' => 'Transfer Bank',
                        'card' => 'Kartu',
                        'e_wallet' => 'E-Wallet',
                        'virtual_account' => 'Virtual Account',
                        'other' => 'Lainnya',
                    ])
                    ->searchable()
                    ->placeholder('Semua Tipe'),
            ])

            // 🧍 Aksi tiap baris
            ->recordActions([
                EditAction::make(),
            ])

            // 🧹 Aksi massal
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
