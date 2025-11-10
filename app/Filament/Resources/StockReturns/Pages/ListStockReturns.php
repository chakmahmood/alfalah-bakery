<?php

namespace App\Filament\Resources\StockReturns\Pages;

use App\Filament\Resources\StockReturns\StockReturnResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockReturns extends ListRecords
{
    protected static string $resource = StockReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
