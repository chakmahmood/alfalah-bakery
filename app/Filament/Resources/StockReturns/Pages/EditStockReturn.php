<?php

namespace App\Filament\Resources\StockReturns\Pages;

use App\Filament\Resources\StockReturns\StockReturnResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockReturn extends EditRecord
{
    protected static string $resource = StockReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
