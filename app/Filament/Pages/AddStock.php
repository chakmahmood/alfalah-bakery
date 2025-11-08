<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\Branch;
use App\Models\Product;
use App\Services\StockService;
use BackedEnum;

class AddStock extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationLabel = 'Tambah Stok';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-plus';
    protected string $view = 'filament.pages.add-stock';

    // ⚡ Tambahkan properti publik untuk Livewire Repeater
    public array $items = [];

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('items')
                ->label('Daftar Produk')
                ->default([]) // Repeater kosong default
                ->schema([
                    Select::make('branch_id')
                        ->label('Cabang')
                        ->options(fn () => Branch::pluck('name', 'id'))
                        ->required()
                        ->searchable(),

                    Select::make('product_id')
                        ->label('Produk')
                        ->options(fn () => Product::where('is_active', true)->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($set, $state) {
                            $product = Product::find($state);
                            $set('unit', $product?->unit?->symbol ?? '');
                        }),

                    TextInput::make('quantity')
                        ->label('Jumlah Stok')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->suffix(fn ($get) => $get('unit') ?? ''),

                ])
                ->columns(3)
                ->createItemButtonLabel('Tambah Produk Baru'),
        ];
    }

    public function submit()
    {
        if (empty($this->items)) {
            Notification::make()
                ->title('Tidak ada data yang ditambahkan')
                ->warning()
                ->send();
            return;
        }

        foreach ($this->items as $item) {
            try {
                StockService::move(
                    type: 'in',
                    branchId: $item['branch_id'],
                    productId: $item['product_id'],
                    quantity: $item['quantity'],
                    note: 'Penambahan stok manual via AddStock page'
                );
            } catch (\Exception $e) {
                Notification::make()
                    ->title("Gagal menambahkan stok untuk produk ID {$item['product_id']}")
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }

        Notification::make()
            ->title('Semua stok berhasil ditambahkan')
            ->success()
            ->send();

        // reset Repeater tapi tetap tampil
        $this->items = [];
    }

    protected function getFormModel(): string
    {
        return ''; // Tidak pakai model
    }
}
