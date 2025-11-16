<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Promotion;
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
            self::transactionInfoSection(),
            self::itemsSection(),
            self::paymentsSection(),
            self::totalsSection(),
            self::statusSection(),
        ]);
    }

    // ------------------- Section 1: Informasi Transaksi -------------------
    protected static function transactionInfoSection(): Section
    {
        return Section::make('🧾 Informasi Transaksi')
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
                    ->afterStateUpdated(fn($state, callable $set) => $set('items', []))
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

                Hidden::make('payment_method_id')
                    ->dehydrated(true)
                    ->default(fn(callable $get) => optional(collect($get('payments'))->last())['payment_method_id'] ?? null),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    // ------------------- Section 2: Item Penjualan -------------------
    protected static function itemsSection(): Section
    {
        return Section::make('🛒 Daftar Item')
            ->schema([
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        self::productSelect(),
                        TextInput::make('unit_name')->label('Satuan')->readOnly()->dehydrated(false),
                        self::quantityInput(),
                        self::priceInput(),
                        self::discountItemInput(),
                        Hidden::make('unit_id')->dehydrated(true),
                        TextInput::make('item_subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(false),
                    ])
                    ->columns(3)
                    ->label('Item Penjualan')
                    ->reactive(),
            ])
            ->columnSpanFull();
    }

    protected static function productSelect(): Select
    {
        return Select::make('product_id')
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
            ->reactive()
            ->afterStateHydrated(function ($state, callable $set) {
                if ($state) {
                    $product = Product::with('unit')->find($state);
                    if ($product) {
                        $set('unit_name', $product->unit->name ?? '-');
                        $set('price', $product->sell_price ?? 0);
                        $set('quantity', 1);
                        $set('item_subtotal', $product->sell_price ?? 0);
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
                    //TODO: panggil fungsi updateFormData
                    self::updateFormData($set, $get);
                }
            });
    }

    protected static function quantityInput(): TextInput
    {
        return TextInput::make('quantity')
            ->label('Qty')
            ->numeric()
            ->default(1)
            ->required()
            ->live(debounce: 500)
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                self::updateFormData($set, $get);
            });
    }

    protected static function priceInput(): TextInput
    {
        return TextInput::make('price')
            ->label('Harga')
            ->numeric()
            ->required()
            ->live(debounce: 500)
            ->readOnly()
            ->dehydrated();
    }

    protected static function discountItemInput(): TextInput
    {
        return TextInput::make('discount_item')
            ->label('Diskon Item')
            ->numeric()
            ->default(0)
            ->live(debounce: 500)
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                self::updateFormData($set, $get);
            });
    }

    // ------------------- Section 3: Pembayaran -------------------
    protected static function paymentsSection(): Section
    {
        return Section::make('💳 Pembayaran (DP / Lunas)')
            ->schema([
                Repeater::make('payments')
                    ->relationship()
                    ->schema([
                        Select::make('payment_method_id')
                            ->label('Metode Pembayaran')
                            ->options(fn() => PaymentMethod::where('is_active', true)->pluck('name', 'id'))
                            ->required(),
                        TextInput::make('amount')
                            ->label('Nominal Bayar')
                            ->numeric()
                            ->required()
                            ->live(debounce: 500),
                        // ->afterStateUpdated(fn($state, $set, $get) => self::updateTotals($set, $get)),
                        TextInput::make('reference_number')->label('No. Referensi (opsional)'),
                        Textarea::make('note')->label('Catatan')->rows(1),
                    ])
                    ->columns(1)
                    ->label('Pembayaran')
                    ->reactive(),
                // ->afterStateUpdated(fn($state, $set, $get) => self::updateTotals($set, $get)),

                TextInput::make('total_payment')->label('Total Bayar')->numeric()->readOnly(),
                TextInput::make('change_due')->label('Kembalian / Sisa')->numeric()->readOnly(),
            ])
            ->columnSpanFull()
            ->columns(1);
    }

    // ------------------- Section 4: Ringkasan Total -------------------
    protected static function totalsSection(): Section
    {
        return Section::make('🧮 Ringkasan Total')
            ->schema([
                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->readOnly(),

                Select::make('promotion_id')
                    ->label('Pilih Promo')
                    ->options(fn() => Promotion::active()->pluck('name', 'id'))
                    ->reactive()
                    ->nullable()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        self::handlePromo($state, $set, $get);
                    }),
                TextInput::make('discount_additional')
                    ->label('Diskon Tambahan')
                    ->numeric()
                    ->default(0)
                    ->live(debounce: 500),

                TextInput::make('tax')
                    ->label('Pajak')
                    ->numeric()
                    ->default(0),


                TextInput::make('total')
                    ->label('Total Akhir')
                    ->numeric()
                    ->readOnly(),
            ])
            ->columnSpanFull()
            ->columns(1);
    }

    protected static function updateFormData(callable $set, callable $get): void
    {
        $formData = $get('../../..');
        $items = $formData['items'] ?? [];
        $subtotal = 0;
        foreach ($items as $index => $item) {
            $price = $item['price'] ?? 0;
            $qty = $item['quantity'] ?? 1;
            $discountItem = $item['discount_item'] ?? 0;

            // Hitung subtotal item
            $itemSubtotal = $price * $qty - $discountItem;
            $subtotal += $itemSubtotal;
        }

        $price = $get('price') ?? 0;
        $qty = $get('quantity') ?? 1;
        $discountItem = $get('discount_item') ?? 0;

        // Hitung subtotal item
        $itemSubtotal = ($price * $qty) - $discountItem;

        $set('item_subtotal', $itemSubtotal);


        $set('../../subtotal', $subtotal);

        self::recalcAll($set, $get);

        dump($get('../../..'));
    }

    // ------------------- Section 5: Status & Catatan -------------------
    protected static function statusSection(): Section
    {
        return Section::make('📝 Lain-lain')
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
                Textarea::make('note')->label('Catatan Tambahan')->rows(2)->columnSpanFull(),
            ])
            ->columns(1)
            ->columnSpanFull();
    }

    protected static function handlePromo($state, callable $set, callable $get)
    {
        $subtotal = $get('subtotal');

        if ($state) {
            $promo = Promotion::find($state);
            if ($promo && $promo->isCurrentlyActive()) {
                $discountAmount = match ($promo->type) {
                    'percentage' => $subtotal * ($promo->value / 100),
                    'fixed' => $promo->value,
                    default => 0,
                };

                $set('discount_additional', $discountAmount);
            } else {
                $set('discount_additional', 0);
            }
        } else {
            $set('discount_additional', 0);
        }

        $total = $subtotal - ($get('discount_additional') ?? 0) + ($get('../../tax') ?? 0);
        $set('total', $total);
    }

    protected static function recalcAll(callable $set, callable $get)
    {
        $subtotal = $get('../../subtotal') ?? 0;
        $promoId = $get('../../promotion_id');

        // hitung promo
        if ($promoId) {
            $promo = Promotion::find($promoId);
            if ($promo && $promo->isCurrentlyActive()) {
                $discount = $promo->type === 'percentage'
                    ? $subtotal * ($promo->value / 100)
                    : $promo->value;
                $set('../../discount_additional', $discount);
            } else {
                $set('../../discount_additional', 0);
            }
        } else {
            $set('../../discount_additional', 0);
        }

        // hitung total akhir
        $total = $subtotal - ($get('../../discount_additional') ?? 0) + ($get('../../tax') ?? 0);
        $set('../../total', $total);
    }

}
