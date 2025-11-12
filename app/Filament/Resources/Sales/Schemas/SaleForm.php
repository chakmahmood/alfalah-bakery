<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Branch;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\User;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // 🧾 INFORMASI TRANSAKSI
            Section::make('🧾 Informasi Transaksi')
                ->schema([
                    TextInput::make('invoice_number')
                        ->label('Nomor Invoice')
                        ->default(fn() => 'SALE-' . (auth()->user()->branch->code ?? 'HQ') . '-' . now()->format('Ymd-His'))
                        ->readOnly(),

                    Select::make('branch_id')
                        ->label('Cabang')
                        ->options(fn() => Branch::pluck('name', 'id'))
                        ->default(fn() => auth()->user()->branch_id ?? null)
                        ->reactive()
                        ->afterStateUpdated(fn($state, callable $set) => $set('items', [])) // reset item saat cabang ganti
                        ->required(),

                    Select::make('user_id')
                        ->label('Kasir')
                        ->options(fn() => User::pluck('name', 'id'))
                        ->default(fn() => auth()->id())
                        ->disabled()
                        ->dehydrated(),

                    DateTimePicker::make('sale_date')
                        ->label('Tanggal Penjualan')
                        ->default(now())
                        ->required(),

                    // Hidden field untuk menyimpan metode pembayaran terakhir
                    Hidden::make('payment_method_id')
                        ->dehydrated(true)
                        ->default(fn(callable $get) => optional(collect($get('payments'))->last())['payment_method_id'] ?? null),

                ])
                ->columns(2),

            // 🛒 ITEM PENJUALAN
            Section::make('🛒 Daftar Item')
                ->schema([
                    Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->label('Produk')
                                ->options(function (callable $get) {
                                    $branchId = $get('../../branch_id');
                                    if (!$branchId) {
                                        return Product::where('is_sellable', true)->pluck('name', 'id');
                                    }

                                    return Product::where('is_sellable', true)
                                        ->whereHas('branches', fn($q) => $q->where('branch_id', $branchId))
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->live(debounce: 500)
                                ->afterStateHydrated(function ($state, callable $set) {
                                    if ($state) {
                                        $product = Product::with('unit')->find($state);
                                        if ($product) {
                                            $set('unit_name', $product->unit->name ?? '-');
                                            $set('price', $product->sell_price ?? 0);
                                        }
                                    }
                                })
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $product = Product::with('unit')->find($state);
                                    if ($product) {
                                        $set('unit_id', $product->unit_id);
                                        $set('unit_name', $product->unit->name ?? '-');
                                        $set('price', $product->sell_price ?? 0);
                                        if (!($get('quantity') ?? null)) {
                                            $set('quantity', 1);
                                        }
                                        $subtotal = ($product->sell_price ?? 0) * ($get('quantity') ?? 1);
                                        $set('subtotal', $subtotal);
                                        self::updateTotals($set, $get);
                                    }
                                }),

                            TextInput::make('unit_name')
                                ->label('Satuan')
                                ->readOnly()
                                ->dehydrated(false),

                            TextInput::make('quantity')
                                ->label('Qty')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live(debounce: 500)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $subtotal = (($get('price') ?? 0) * ($state ?? 0)) - ($get('discount') ?? 0);
                                    $set('subtotal', $subtotal);
                                    self::updateTotals($set, $get);
                                }),

                            TextInput::make('price')
                                ->label('Harga')
                                ->numeric()
                                ->required()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $subtotal = (($state ?? 0) * ($get('quantity') ?? 0)) - ($get('discount') ?? 0);
                                    $set('subtotal', $subtotal);
                                    self::updateTotals($set, $get);
                                })
                                ->dehydrated(),

                            TextInput::make('discount')
                                ->label('Diskon')
                                ->numeric()
                                ->default(0)
                                ->live(debounce: 500)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $subtotal = (($get('price') ?? 0) * ($get('quantity') ?? 0)) - ($state ?? 0);
                                    $set('subtotal', $subtotal);
                                    self::updateTotals($set, $get);
                                }),

                            Hidden::make('unit_id')
                                ->dehydrated(true),

                            TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->numeric()
                                ->readOnly()
                                ->dehydrated(),

                        ])
                        ->columns(2)
                        ->label('Item Penjualan')
                        ->live(debounce: 500)
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),
                ])
                ->columns(1),

            // 💳 PEMBAYARAN
            Section::make('💳 Pembayaran (DP / Lunas)')
                ->schema([
                    Repeater::make('payments')
                        ->relationship()
                        ->schema([
                            Select::make('payment_method_id')
                                ->label('Metode Pembayaran')
                                ->options(fn() => PaymentMethod::where('is_active', true)->pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->dehydrated(),

                            TextInput::make('amount')
                                ->label('Nominal Bayar')
                                ->numeric()
                                ->required()
                                ->live(debounce: 500)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    self::updateTotals($set, $get);
                                }),

                            TextInput::make('reference_number')
                                ->label('No. Referensi (opsional)'),

                            Textarea::make('note')
                                ->label('Catatan')
                                ->rows(1),
                        ])
                        ->columns(1)
                        ->label('Pembayaran')
                        ->live(debounce: 200)
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    TextInput::make('total_payment')
                        ->label('Total Bayar')
                        ->numeric()
                        ->readOnly()
                        ->placeholder('Otomatis dari total pembayaran'),

                    TextInput::make('change_due')
                        ->label('Kembalian / Sisa')
                        ->numeric()
                        ->readOnly()
                        ->placeholder('Terhitung otomatis'),
                ])
                ->columns(1),

            // 🧮 RINGKASAN TOTAL
            Section::make('🧮 Ringkasan Total')
                ->schema([
                    TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->numeric()
                        ->readOnly(),

                    TextInput::make('discount')
                        ->label('Diskon Tambahan')
                        ->numeric()
                        ->default(0)
                        ->live(debounce: 500)
                        ->afterStateUpdated(fn($state, $set, $get) => self::updateTotals($set, $get)),

                    TextInput::make('tax')
                        ->label('Pajak')
                        ->numeric()
                        ->default(0)
                        ->live(debounce: 500)
                        ->afterStateUpdated(fn($state, $set, $get) => self::updateTotals($set, $get)),

                    TextInput::make('total')
                        ->label('Total Akhir')
                        ->numeric()
                        ->readOnly(),
                ])
                ->columns(1),

            // 📝 STATUS & CATATAN
            Section::make('📝 Lain-lain')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'partial' => 'DP / Belum Lunas',
                            'paid' => 'Lunas',
                            'cancelled' => 'Dibatalkan',
                        ])
                        ->default('draft')
                        ->required(),

                    Textarea::make('note')
                        ->label('Catatan Tambahan')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }

    protected static function updateTotals(callable $set, callable $get): void
    {
        $items = $get('../../items') ?? $get('items') ?? [];
        $subtotal = collect($items)->sum(fn($item) => (float) ($item['subtotal'] ?? 0));

        $discount = (float) ($get('../../discount') ?? $get('discount') ?? 0);
        $tax = (float) ($get('../../tax') ?? $get('tax') ?? 0);
        $total = ($subtotal - $discount) + $tax;

        $set('../../subtotal', $subtotal);
        $set('../../total', $total);

        // Total pembayaran
        $payments = $get('../../payments') ?? [];
        $totalPayment = collect($payments)->sum(fn($item) => (float) ($item['amount'] ?? 0));
        $set('../../total_payment', $totalPayment);

        // Kembalian
        $set('../../change_due', $totalPayment - $total);

        // Set metode pembayaran terakhir ke root hidden
        $lastPayment = collect($payments)->last();
        $set('../../payment_method_id', $lastPayment['payment_method_id'] ?? null);

        // Status otomatis
        if ($totalPayment <= 0) {
            $set('../../status', 'draft');
        } elseif ($totalPayment < $total) {
            $set('../../status', 'partial');
        } else {
            $set('../../status', 'paid');
        }
    }
}
