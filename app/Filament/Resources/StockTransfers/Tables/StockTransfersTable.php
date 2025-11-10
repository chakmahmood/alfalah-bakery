<?php

namespace App\Filament\Resources\StockTransfers\Tables;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class StockTransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fromBranch.name')
                    ->label('Cabang Asal')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('toBranch.name')
                    ->label('Cabang Tujuan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('transfer_date')
                    ->label('Tanggal Transfer')
                    ->date('d M Y')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'received',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'sent' => 'Dikirim',
                        'received' => 'Diterima',
                        default => ucfirst($state),
                    }),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Dikirim',
                        'received' => 'Diterima',
                    ]),
                Tables\Filters\Filter::make('transfer_date_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('transfer_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('transfer_date', '<=', $data['until']));
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->visible(fn($record) => auth()->user()->isOwner() || $record->status === 'draft'),

                Action::make('send')
                    ->label('Kirim')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        $record->status === 'draft' &&
                        auth()->user()->branch_id === $record->from_branch_id // hanya cabang pengirim
                    )
                    ->action(function ($record) {
                        app(\App\Services\StockTransferService::class)->handleSent($record);
                        Notification::make()
                            ->title('Transfer dikirim')
                            ->success()
                            ->send();
                    })
                    ->disabled(fn($record) => $record->status !== 'draft'),

                Action::make('receive')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        $record->status === 'sent' &&
                        auth()->user()->branch_id === $record->to_branch_id // hanya cabang penerima
                    )
                    ->action(function ($record) {
                        app(\App\Services\StockTransferService::class)->handleReceived($record);
                        Notification::make()
                            ->title('Transfer diterima')
                            ->success()
                            ->send();
                    })
                    ->disabled(fn($record) => $record->status !== 'sent'),

            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->visible(fn() => auth()->user()->isOwner()),
                ]),
            ])
            ->defaultSort('transfer_date', 'desc');
    }
}
