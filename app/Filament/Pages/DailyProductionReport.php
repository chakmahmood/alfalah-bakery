<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\StockMovement;
use Carbon\Carbon;
use Livewire\Attributes\On;
use BackedEnum;

class DailyProductionReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Laporan Produksi Harian';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.pages.daily-production-report';

    public ?string $date = null;

    public function mount(): void
    {
        // Default tanggal hari ini
        $this->date = Carbon::today()->toDateString();
    }

    #[On('refreshTable')]
    public function refreshTable(): void
    {
        // Trigger reload tabel Filament
        $this->resetTable();
    }

    protected function getTableQuery()
    {
        // Pastikan tanggal valid, fallback ke hari ini
        $date = $this->date ? Carbon::parse($this->date) : Carbon::today();

        return StockMovement::query()
            ->with(['product.unit', 'branch'])
            ->whereDate('created_at', $date);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.name')
                ->label('Produk')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('branch.name')
                ->label('Cabang')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('type')
                ->label('Tipe')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'production' => 'success',
                    'out' => 'danger',
                    'in' => 'info',
                    default => 'gray',
                }),

            Tables\Columns\TextColumn::make('quantity')
                ->label('Jumlah')
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('product.unit.symbol')
                ->label('Unit'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Waktu')
                ->dateTime('d M Y H:i'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('branch_id')
                ->label('Cabang')
                ->options(fn () => Branch::orderBy('name')->pluck('name', 'id'))
                ->placeholder('Semua Cabang'),

            Tables\Filters\SelectFilter::make('type')
                ->label('Tipe Pergerakan')
                ->options([
                    'production' => 'Produksi',
                    'out' => 'Pengeluaran Bahan Baku',
                    'in' => 'Penambahan Stok',
                ]),
        ];
    }
}
