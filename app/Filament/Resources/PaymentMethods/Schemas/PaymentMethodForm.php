<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Metode Pembayaran')
                    ->placeholder('Contoh: Transfer Bank BCA, GoPay, Tunai')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignorable: fn ($record) => $record), // unik tapi bisa update

                TextInput::make('code')
                    ->label('Kode')
                    ->placeholder('Contoh: TF_BCA, CASH, GOPAY')
                    ->helperText('Kode unik untuk internal sistem.')
                    ->maxLength(50)
                    ->unique(ignorable: fn ($record) => $record),

                Select::make('type')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'cash' => 'Tunai',
                        'bank_transfer' => 'Transfer Bank',
                        'card' => 'Kartu',
                        'e_wallet' => 'E-Wallet',
                        'virtual_account' => 'Virtual Account',
                        'other' => 'Lainnya',
                    ])
                    ->required()
                    ->searchable()
                    ->native(false)
                    ->placeholder('Pilih tipe pembayaran'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Matikan jika metode ini tidak digunakan.')
                    ->default(true),
            ]);
    }
}
