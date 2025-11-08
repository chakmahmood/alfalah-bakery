<?php

namespace App\Filament\Resources\RecipeItems\Pages;

use App\Filament\Resources\RecipeItems\RecipeItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecipeItems extends ListRecords
{
    protected static string $resource = RecipeItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
