<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // hapus branches dari data utama
        unset($data['branches']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $branches = $this->form->getState()['branches'] ?? [];

        // sync pivot dengan timestamps
        $this->record->branches()->sync(
            collect($branches)
                ->mapWithKeys(fn($id) => [$id => ['updated_at' => now(), 'created_at' => now()]])
                ->toArray()
        );
    }
}
