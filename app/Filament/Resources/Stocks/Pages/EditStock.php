<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditStock extends EditRecord
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $branchId  = $data['branch_id'];
        $productId = $data['product_id'];
        $unitId    = $data['unit_id'];
        $quantity  = $data['quantity'];
        $minStock  = $data['min_stock'];

        $result = \App\Models\Stock::addOrUpdateStock(
            $branchId,
            $productId,
            $quantity,
            $minStock,
            $unitId
        );

        // Tampilkan notification
        Notification::make()
            ->title($result['message'])
            ->success()
            ->send();

        // Batalkan update default karena sudah ditangani
        return [];
    }
}
