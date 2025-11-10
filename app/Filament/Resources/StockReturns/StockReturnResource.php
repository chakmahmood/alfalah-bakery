<?php

namespace App\Filament\Resources\StockReturns;

use App\Filament\Resources\StockReturns\Pages\CreateStockReturn;
use App\Filament\Resources\StockReturns\Pages\EditStockReturn;
use App\Filament\Resources\StockReturns\Pages\ListStockReturns;
use App\Filament\Resources\StockReturns\Schemas\StockReturnForm;
use App\Filament\Resources\StockReturns\Tables\StockReturnsTable;
use App\Models\StockReturn;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockReturnResource extends Resource
{
    protected static ?string $model = StockReturn::class;
    protected static string|UnitEnum|null $navigationGroup = '🔁 Retur Antar Cabang / Penarikan Produk';
    protected static ?string $navigationLabel = 'Retur Stok';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StockReturnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockReturnsTable::configure($table);
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
            'index' => ListStockReturns::route('/'),
            'create' => CreateStockReturn::route('/create'),
            'edit' => EditStockReturn::route('/{record}/edit'),
        ];
    }
}
