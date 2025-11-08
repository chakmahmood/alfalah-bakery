<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('store_name')
                    ->required()
                    ->default('AL FALAH BAKERY'),
                TextInput::make('store_address')
                    ->default(null),
                TextInput::make('store_phone')
                    ->tel()
                    ->default(null),
                TextInput::make('store_email')
                    ->email()
                    ->default(null),
                TextInput::make('tax_rate')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('printer_name')
                    ->default(null),
                TextInput::make('currency_symbol')
                    ->required()
                    ->default('Rp'),
                TextInput::make('logo_path')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
