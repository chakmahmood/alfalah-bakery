<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use App\Services\StockService;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        foreach ($record->items as $item) {
            StockService::move(
                'out',
                $record->branch_id,
                $item->product_id,
                $item->quantity,
                $record->invoice_number,
                'Penjualan #' . $record->invoice_number
            );
        }
    }

}
