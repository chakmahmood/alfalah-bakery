<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Unit;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Produk')
                ->description('Detail dasar produk atau bahan baku.')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Produk / Bahan')
                        ->required()
                        ->placeholder('Contoh: Roti Coklat, Tepung Terigu'),

                    Select::make('type')
                        ->label('Tipe Item')
                        ->options([
                            'product' => 'Produk Jadi',
                            'material' => 'Bahan Baku',
                        ])
                        ->default('product')
                        ->required(),

                    TextInput::make('sku')
                        ->label('Kode SKU')
                        ->placeholder('Biarkan kosong untuk otomatis')
                        ->unique(ignoreRecord: true),

                    Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih kategori'),

                    Select::make('unit_id')
                        ->label('Satuan')
                        ->relationship('unit', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih satuan'),

                    CheckboxList::make('branches')
                        ->label('Cabang')
                        ->options(Branch::where('is_active', true)->pluck('name', 'id')->toArray())
                        ->columns(3)
                        ->helperText('Centang cabang yang memiliki produk ini.')
                        ->afterStateHydrated(function ($component) {
                            $record = $component->getContainer()->getRecord(); // ambil Product model
                            if ($record) {
                                $component->state($record->branches()->pluck('branches.id')->toArray());
                            }
                        }),



                ]),

            Section::make('Harga & Status')
                ->description('Atur harga jual, harga modal, dan status penjualan.')
                ->schema([
                    TextInput::make('sell_price')
                        ->label('Harga Jual')
                        ->prefix('Rp')
                        ->numeric()
                        ->required()
                        ->default(0),

                    TextInput::make('cost_price')
                        ->label('Harga Modal')
                        ->prefix('Rp')
                        ->numeric()
                        ->required()
                        ->default(0),

                    Toggle::make('is_sellable')
                        ->label('Bisa Dijual di Kasir')
                        ->helperText('Nonaktifkan jika item hanya untuk bahan baku.')
                        ->default(true),

                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->helperText('Nonaktifkan jika produk sedang tidak dijual.'),
                ]),

            Section::make('Deskripsi & Foto')
                ->collapsible()
                ->schema([
                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->placeholder('Tuliskan keterangan tambahan atau catatan produk...')
                        ->columnSpanFull(),

                    FileUpload::make('image')
                        ->label('Foto Produk')
                        ->image()
                        ->directory('products')
                        ->imagePreviewHeight('150')
                        ->maxSize(2048),
                ]),
        ]);
    }
}
