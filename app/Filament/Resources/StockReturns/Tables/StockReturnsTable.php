<?php

namespace App\Filament\Resources\StockReturns\Tables;

use App\Services\StockReturnService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;

class StockReturnsTable
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
                    ->label('Tujuan Retur')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-'),

                BadgeColumn::make('return_type')
                    ->label('Jenis Retur')
                    ->colors([
                        'success' => 'to_stock',
                        'danger' => 'dispose',
                    ])
                    ->formatStateUsing(fn(string $state) => $state === 'to_stock' ? 'Kembali ke Stok' : 'Dibuang'),

                TextColumn::make('return_date')
                    ->label('Tanggal Retur')
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

                TextColumn::make('disposal_date')
                    ->label('Tanggal Pembuangan')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(40)
                    ->wrap(),

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

                Tables\Filters\SelectFilter::make('return_type')
                    ->label('Jenis Retur')
                    ->options([
                        'to_stock' => 'Kembali ke Stok',
                        'dispose' => 'Dibuang',
                    ]),

                Tables\Filters\Filter::make('return_date_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('return_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('return_date', '<=', $data['until']));
                    }),
            ])

            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->visible(fn($record) => auth()->user()->isOwner() || $record->status === 'draft'),

                Action::make('send')
                    ->label('Kirim Retur')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn($record) =>
                        $record->status === 'draft' &&
                        auth()->user()->branch_id === $record->from_branch_id
                    )
                    ->action(function ($record) {
                        app(StockReturnService::class)->handleSent($record);
                        Notification::make()
                            ->title('Retur berhasil dikirim')
                            ->success()
                            ->send();
                    }),

                Action::make('receive')
                    ->label('Terima Retur')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($record) =>
                        $record->status === 'sent' &&
                        auth()->user()->branch_id === $record->to_branch_id
                    )
                    ->action(function ($record) {
                        app(StockReturnService::class)->handleReceived($record);
                        Notification::make()
                            ->title('Retur diterima')
                            ->success()
                            ->send();
                    }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn() => auth()->user()->isOwner()),
                ]),
            ])

            ->defaultSort('return_date', 'desc');
    }
}
