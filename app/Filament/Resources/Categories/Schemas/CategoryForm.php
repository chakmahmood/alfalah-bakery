<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('parent_id')
                ->label('Kategori Induk')
                ->options(Category::whereNull('parent_id')->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Biarkan kosong jika ini kategori utama.'),

            TextInput::make('name')
                ->label('Nama Kategori')
                ->required()
                ->maxLength(100),

            TextInput::make('slug')
                ->label('Slug')
                ->maxLength(100)
                ->helperText('Otomatis diisi jika dikosongkan.'),

            Textarea::make('description')
                ->label('Deskripsi')
                ->default(null)
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }
}
