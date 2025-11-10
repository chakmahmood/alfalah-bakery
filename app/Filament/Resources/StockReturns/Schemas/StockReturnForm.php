<?php

namespace App\Filament\Resources\StockReturns\Schemas;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockReturnForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Filament::auth()->user();

        return $schema
            ->columns(2)
            ->components([

                // 🏠 Cabang Asal
                Select::make('from_branch_id')
                    ->label('Cabang Asal')
                    ->options(Branch::pluck('name', 'id'))
                    ->default(fn() => $user->branch_id)
                    ->disabled(fn() => !$user->isOwner()) // non-owner tidak bisa ubah
                    ->dehydrated(true)
                    ->required()
                    ->placeholder('Pilih cabang asal')
                    ->helperText(fn() => !$user->isOwner() ? 'Otomatis diisi sesuai cabang Anda.' : null),

                // 🏢 Cabang Tujuan (biasanya pusat)
                Select::make('to_branch_id')
                    ->label('Cabang Tujuan')
                    ->options(function (callable $get) {
                        $from = $get('from_branch_id');
                        $branches = Branch::query();

                        if ($from) {
                            $branches->where('id', '!=', $from);
                        }

                        return $branches->pluck('name', 'id');
                    })
                    ->searchable()
                    ->reactive()
                    ->placeholder('Pilih cabang tujuan (biasanya pusat)')
                    ->helperText('Tempat barang retur dikirim. Bisa dikosongkan jika dibuang di tempat.'),

                // 🔁 Jenis retur
                Select::make('return_type')
                    ->label('Jenis Retur')
                    ->options([
                        'to_stock' => 'Kembali ke Stok',
                        'dispose' => 'Dibuang / Rusak',
                    ])
                    ->default('dispose')
                    ->required()
                    ->native(false)
                    ->helperText('Pilih "Kembali ke Stok" jika masih layak, atau "Dibuang" jika rusak / kadaluarsa.'),

                // 📅 Tanggal retur
                DatePicker::make('return_date')
                    ->label('Tanggal Retur')
                    ->required()
                    ->default(now()),

                // 🚚 Status retur
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Dikirim',
                        'received' => 'Diterima',
                    ])
                    ->default('draft')
                    ->required()
                    ->native(false),

                // 🗑️ Tanggal pembuangan (untuk return_type = dispose)
                DatePicker::make('disposal_date')
                    ->label('Tanggal Dibuang')
                    ->visible(fn(callable $get) => $get('return_type') === 'dispose')
                    ->helperText('Isi jika barang rusak / kadaluarsa dibuang.'),

                // 📝 Catatan tambahan
                Textarea::make('note')
                    ->label('Catatan')
                    ->rows(3)
                    ->placeholder('Tambahkan catatan jika perlu...')
                    ->columnSpanFull(),

                // 📦 Daftar barang yang diretur
                Repeater::make('items')
                    ->label('Daftar Barang Diretur')
                    ->relationship('items')
                    ->schema([
                        // Produk
                        Select::make('product_id')
                            ->label('Produk')
                            ->options(function (callable $get) {
                                $branchId = $get('../../from_branch_id');
                                if (!$branchId)
                                    return [];

                                $stocks = Stock::where('branch_id', $branchId)
                                    ->with('product.unit')
                                    ->get();

                                return $stocks->mapWithKeys(function ($stock) {
                                    $name = $stock->product->name;
                                    $unit = $stock->product->unit->symbol ?? '';
                                    $qty = (float) $stock->quantity;
                                    return [
                                        $stock->product_id => "{$name} ({$qty} {$unit})",
                                    ];
                                });
                            })
                            ->searchable()
                            ->reactive()
                            ->required()
                            ->placeholder('Pilih produk'),

                        // Jumlah retur
                        TextInput::make('quantity')
                            ->label('Jumlah Retur')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->suffix(function (callable $get) {
                                $productId = $get('product_id');
                                if (!$productId)
                                    return '';
                                $product = Product::find($productId);
                                return $product?->unit?->symbol ?? '';
                            })
                            ->helperText(function (callable $get) {
                                $branchId = $get('../../from_branch_id');
                                $productId = $get('product_id');
                                if (!$branchId || !$productId)
                                    return null;

                                $stock = Stock::where('branch_id', $branchId)
                                    ->where('product_id', $productId)
                                    ->first();

                                if (!$stock) {
                                    return "Tidak ada stok di cabang ini.";
                                }

                                $qty = number_format((float) $stock->quantity, 2);
                                $unit = $stock->product?->unit?->symbol ?? '';
                                return "Stok saat ini: {$qty} {$unit}";
                            })
                            ->rule(function (callable $get) {
                                return function (string $attribute, $value, $fail) use ($get) {
                                    $branchId = $get('../../from_branch_id');
                                    $productId = $get('product_id');
                                    if (!$branchId || !$productId)
                                        return;

                                    $stock = Stock::where('branch_id', $branchId)
                                        ->where('product_id', $productId)
                                        ->first();

                                    if ($stock && $value > $stock->quantity) {
                                        $fail("Jumlah retur melebihi stok tersedia ({$stock->quantity}).");
                                    }
                                };
                            }),
                    ])
                    ->columns(2)
                    ->createItemButtonLabel('Tambah Barang Retur')
                    ->columnSpanFull(),

                // 👤 User login otomatis
                Select::make('user_id')
                    ->label('Dibuat Oleh')
                    ->options(User::pluck('name', 'id'))
                    ->default(fn() => $user->id)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull(),
            ]);
    }
}
