<?php

namespace App\Filament\Resources\PromotionProducts\Pages;

use App\Filament\Resources\PromotionProducts\PromotionProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPromotionProducts extends ListRecords
{
    protected static string $resource = PromotionProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
