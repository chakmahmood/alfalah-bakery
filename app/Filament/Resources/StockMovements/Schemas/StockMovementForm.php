<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\Branch;
use App\Models\Product;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // 📍 Pilih Cabang
                Select::make('branch_id')
                    ->label('Cabang')
                    ->options(fn() => Branch::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('product_id', null))
                    ->placeholder('Pilih cabang'),

                // 📦 Pilih Produk (terfilter berdasarkan cabang)
                Select::make('product_id')
                    ->label('Produk')
                    ->options(function (callable $get) {
                        $branchId = $get('branch_id');

                        // Jika cabang dipilih, tampilkan produk milik cabang tsb atau global (branch_id null)
                        if ($branchId) {
                            return Product::where(function ($q) use ($branchId) {
                                $q->where('branch_id', $branchId)
                                  ->orWhereNull('branch_id');
                            })
                            ->orderBy('name')
                            ->pluck('name', 'id');
                        }

                        // Jika belum pilih cabang, tampilkan semua produk aktif
                        return Product::where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih produk yang sesuai cabang')
                    ->helperText('Daftar produk menyesuaikan cabang yang dipilih.'),

                // 🔁 Jenis Pergerakan
                Select::make('type')
                    ->label('Tipe Pergerakan')
                    ->options([
                        'in' => 'Masuk (IN)',
                        'out' => 'Keluar (OUT)',
                        'transfer' => 'Transfer Antar Cabang',
                        'adjustment' => 'Penyesuaian Manual',
                        'production' => 'Produksi',
                        'return' => 'Retur / Pengembalian',
                    ])
                    ->default('in')
                    ->required()
                    ->helperText('Pilih jenis aktivitas yang menyebabkan perubahan stok.'),

                // 🔢 Jumlah
                TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->suffix('unit')
                    ->helperText('Masukkan jumlah barang yang berubah stoknya.'),

                // 🧾 Referensi Transaksi
                TextInput::make('reference')
                    ->label('Referensi Transaksi')
                    ->placeholder('Contoh: SALE-1001 atau TRANSFER-2002')
                    ->maxLength(50),

                // 📝 Catatan Tambahan
                Textarea::make('note')
                    ->label('Catatan')
                    ->placeholder('Tulis alasan atau keterangan tambahan di sini...')
                    ->columnSpanFull(),
            ]);
    }
}
