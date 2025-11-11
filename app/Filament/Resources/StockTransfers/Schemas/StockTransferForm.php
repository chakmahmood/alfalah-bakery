<?php

namespace App\Filament\Resources\StockTransfers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Filament\Facades\Filament;

class StockTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Filament::auth()->user();

        return $schema
            ->columns(2)
            ->components([

                // 🏠 Cabang asal (Owner bisa pilih, user biasa otomatis)
                Select::make('from_branch_id')
                    ->label('Cabang Asal')
                    ->options(Branch::pluck('name', 'id'))
                    ->default(fn() => $user->branch_id)
                    ->disabled(fn() => !$user->isOwner()) // non-owner tidak bisa ubah
                    ->dehydrated(true)
                    ->required()
                    ->placeholder('Pilih cabang asal')
                    ->helperText(fn() => !$user->isOwner() ? 'Otomatis diisi sesuai cabang Anda.' : null),


                // 🏪 Cabang tujuan
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
                    ->required()
                    ->reactive()
                    ->rule('different:from_branch_id')
                    ->placeholder('Pilih cabang tujuan')
                    ->helperText('Cabang asal dan tujuan tidak boleh sama.'),


                // 📅 Tanggal transfer
                DatePicker::make('transfer_date')
                    ->label('Tanggal Transfer')
                    ->required()
                    ->default(now()),

                // 🚚 Status
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

                // 📝 Catatan
                Textarea::make('note')
                    ->label('Catatan')
                    ->rows(3)
                    ->placeholder('Tambahkan catatan jika perlu...')
                    ->columnSpanFull(),

                // 📦 Daftar produk
                Repeater::make('items')
                    ->label('Daftar Barang yang Ditransfer')
                    ->relationship('items')
                    ->schema([

                        // Produk
                        Select::make('product_id')
                            ->label('Produk')
                            ->options(function (callable $get) {
                                $fromBranch = $get('../../from_branch_id');
                                $toBranch = $get('../../to_branch_id');

                                // Pastikan dua cabang sudah dipilih
                                if (!$fromBranch || !$toBranch) {
                                    return [];
                                }

                                // Cari produk yang dimiliki kedua cabang
                                $productIds = \DB::table('branch_product')
                                    ->whereIn('branch_id', [$fromBranch, $toBranch])
                                    ->select('product_id')
                                    ->groupBy('product_id')
                                    ->havingRaw('COUNT(DISTINCT branch_id) = 2') // artinya dimiliki oleh kedua cabang
                                    ->pluck('product_id');

                                // Ambil produk lengkapnya
                                $products = Product::whereIn('id', $productIds)
                                    ->with('unit')
                                    ->get();

                                return $products->mapWithKeys(function ($product) use ($fromBranch) {
                                    // Ambil stok di cabang asal (optional, buat info)
                                    $stock = Stock::where('branch_id', $fromBranch)
                                        ->where('product_id', $product->id)
                                        ->first();

                                    $qty = $stock ? number_format((float) $stock->quantity, 2) : 0;
                                    $unit = $product->unit?->symbol ?? '';

                                    return [
                                        $product->id => "{$product->name} ({$qty} {$unit})",
                                    ];
                                });
                            })
                            ->searchable()
                            ->reactive()
                            ->required()
                            ->placeholder('Pilih produk'),



                        // Jumlah transfer
                        TextInput::make('quantity')
                            ->label('Jumlah Transfer')
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
                                        $fail("Jumlah transfer melebihi stok tersedia ({$stock->quantity}).");
                                    }
                                };
                            }),
                    ])
                    ->columns(2)
                    ->createItemButtonLabel('Tambah Barang')
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

