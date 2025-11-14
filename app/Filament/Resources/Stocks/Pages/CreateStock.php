<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateStock extends CreateRecord
{
    protected static string $resource = StockResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $branchId  = $data['branch_id'];
        $productId = $data['product_id'];
        $quantity  = $data['quantity'];
        $minStock  = $data['min_stock'];

        $result = \App\Models\Stock::addOrUpdateStock(
            $branchId,
            $productId,
            $quantity,
            $minStock,
        );

        // Tampilkan notification
        Notification::make()
            ->title($result['message'])
            ->success()
            ->send();

        // Batalkan insert default karena sudah ditangani
        return [];
    }
}
