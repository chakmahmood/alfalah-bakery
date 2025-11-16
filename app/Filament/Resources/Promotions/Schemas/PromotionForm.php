<?php

namespace App\Filament\Resources\Promotions\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Promotion Details')
                    ->description('Isi informasi dasar promo')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Promo')
                            ->placeholder('Misal: Diskon Lebaran')
                            ->required(),

                        Select::make('type')
                            ->label('Tipe Promo')
                            ->options([
                                'percentage' => 'Persentase (%)',
                                'fixed' => 'Nominal Tetap',
                                'buy_x_get_y' => 'Beli X Gratis Y',
                            ])
                            ->default('fixed')
                            ->required(),

                        TextInput::make('value')
                            ->label('Nilai Diskon / Free Item')
                            ->numeric()
                            ->placeholder('Masukkan jumlah atau nominal')
                            ->minValue(0)
                            ->helperText(fn($get) => $get('type') === 'buy_x_get_y'
                                ? 'Isi jumlah item gratis'
                                : 'Isi nominal diskon atau persen')
                            ->required(fn($get) => $get('type') !== 'buy_x_get_y'),
                    ]),

                Section::make('Tanggal Promo')
                    ->description('Atur periode promo')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('start_date')
                                ->label('Mulai Tanggal')
                                ->required(),
                            DatePicker::make('end_date')
                                ->label('Sampai Tanggal')
                                ->required(),
                        ]),
                    ]),


                Toggle::make('is_active')
                    ->label('Aktifkan Promo')
                    ->helperText('Nonaktifkan jika promo sudah selesai atau ingin dijeda')
                    ->default(true),

            ]);
    }
}
