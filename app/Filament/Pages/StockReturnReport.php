<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\StockReturnItem;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use BackedEnum;

class StockReturnReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Laporan Retur Stok';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';
    protected string $view = 'filament.pages.stock-return-report';

    protected function getTableQuery()
    {
        // ✅ ambil langsung dari item retur
        return StockReturnItem::query()
            ->with(['product', 'unit', 'stockReturn.fromBranch', 'stockReturn.toBranch'])
            ->whereDate('created_at', today()); // default tampil hari ini
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('stockReturn.id')
                ->label('ID Retur')
                ->sortable(),

            TextColumn::make('stockReturn.fromBranch.name')
                ->label('Dari Cabang')
                ->sortable()
                ->searchable(),

            TextColumn::make('stockReturn.toBranch.name')
                ->label('Ke Cabang')
                ->sortable()
                ->searchable()
                ->placeholder('-'),

            TextColumn::make('product.name')
                ->label('Produk')
                ->searchable(),

            TextColumn::make('product.unit.symbol')
                ->label('Satuan')
                ->placeholder('-'),

            TextColumn::make('quantity')
                ->label('Qty')
                ->numeric(),

            TextColumn::make('cost_price')
                ->label('Harga Modal')
                ->money('IDR', true),

            TextColumn::make('stockReturn.return_type')
                ->label('Tipe Retur')
                ->badge()
                ->color(fn($state) => match ($state) {
                    'to_stock' => 'info',
                    'dispose' => 'danger',
                    default => 'gray',
                })
                ->formatStateUsing(fn($state) => $state === 'to_stock' ? 'Kembali ke Stok' : 'Dibuang'),

            TextColumn::make('stockReturn.status')
                ->label('Status')
                ->badge()
                ->color(fn($state) => match ($state) {
                    'draft' => 'gray',
                    'sent' => 'warning',
                    'received' => 'success',
                    default => 'gray',
                }),

            TextColumn::make('stockReturn.return_date')
                ->label('Tanggal Retur')
                ->date('d M Y'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            // ✅ Filter rentang tanggal retur (bukan tanggal item)
            Filter::make('stockReturn.return_date')
                ->form([
                    DatePicker::make('from')
                        ->label('Dari Tanggal')
                        ->default(today()),
                    DatePicker::make('until')
                        ->label('Sampai Tanggal')
                        ->default(today()),
                ])
                ->query(function ($query, array $data) {
                    return $query->whereHas('stockReturn', function ($q) use ($data) {
                        $q->when($data['from'], fn($q, $date) => $q->whereDate('return_date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('return_date', '<=', $date));
                    });
                }),

            SelectFilter::make('stockReturn.fromBranch')
                ->label('Cabang Asal')
                ->relationship('stockReturn.fromBranch', 'name')
                ->placeholder('Semua Cabang'),


            SelectFilter::make('stockReturn.return_type')
                ->label('Jenis Retur')
                ->options([
                    'to_stock' => 'Kembali ke Stok',
                    'dispose' => 'Dibuang',
                ]),

            SelectFilter::make('stockReturn.status')
                ->label('Status')
                ->options([
                    'draft' => 'Draft',
                    'sent' => 'Dikirim',
                    'received' => 'Diterima',
                ]),
        ];
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns($this->getTableColumns())
            ->filters(
                $this->getTableFilters(),
                layout: FiltersLayout::Modal,
            );
    }
}
