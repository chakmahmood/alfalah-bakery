<?php

namespace App\Filament\Resources\StockReturns\Pages;

use App\Filament\Resources\StockReturns\StockReturnResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockReturn extends CreateRecord
{
    protected static string $resource = StockReturnResource::class;
}
