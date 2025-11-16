<?php

namespace App\Filament\Resources\PromotionProducts;

use App\Filament\Resources\PromotionProducts\Pages\CreatePromotionProduct;
use App\Filament\Resources\PromotionProducts\Pages\EditPromotionProduct;
use App\Filament\Resources\PromotionProducts\Pages\ListPromotionProducts;
use App\Filament\Resources\PromotionProducts\Schemas\PromotionProductForm;
use App\Filament\Resources\PromotionProducts\Tables\PromotionProductsTable;
use App\Models\PromotionProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PromotionProductResource extends Resource
{
    protected static ?string $model = PromotionProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PromotionProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromotionProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromotionProducts::route('/'),
            'create' => CreatePromotionProduct::route('/create'),
            'edit' => EditPromotionProduct::route('/{record}/edit'),
        ];
    }
}
