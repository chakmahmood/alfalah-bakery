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
        // dd($record);
        // Update stok
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

        // Hitung total bayar & kembalian dari relasi payments
        $record->load('payments'); // pastikan payments termuat
        $totalPayment = $record->payments->sum('amount');
        $changeDue = $totalPayment - $record->total;
        // Redirect ke halaman print dengan query params
        $this->redirect(route('sales.print', ['sale' => $record->id]) . '?' . http_build_query([
            'total_payment' => $totalPayment,
            'change_due' => max(0, $changeDue),
        ]));


    }

}
