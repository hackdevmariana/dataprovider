<?php

namespace App\Filament\Resources\PlantSpeciesResource\Pages;

use App\Filament\Resources\PlantSpeciesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlantSpecies extends EditRecord
{
    protected static string $resource = PlantSpeciesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
