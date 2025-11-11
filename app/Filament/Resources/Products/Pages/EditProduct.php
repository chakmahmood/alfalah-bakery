<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $branches = $data['branches'] ?? [];
        unset($data['branches']);

        $record = parent::mutateFormDataBeforeSave($data);

        // Sync pivot dengan timestamps
        $this->record->branches()->sync(
            collect($branches)
                ->mapWithKeys(fn($id) => [$id => ['updated_at' => now(), 'created_at' => now()]])
                ->toArray()
        );

        return $record;
    }
}
