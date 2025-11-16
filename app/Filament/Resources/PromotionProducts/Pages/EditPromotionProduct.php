<?php

namespace App\Filament\Resources\PromotionProducts\Pages;

use App\Filament\Resources\PromotionProducts\PromotionProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPromotionProduct extends EditRecord
{
    protected static string $resource = PromotionProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
