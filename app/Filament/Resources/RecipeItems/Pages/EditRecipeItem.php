<?php

namespace App\Filament\Resources\RecipeItems\Pages;

use App\Filament\Resources\RecipeItems\RecipeItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecipeItem extends EditRecord
{
    protected static string $resource = RecipeItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
