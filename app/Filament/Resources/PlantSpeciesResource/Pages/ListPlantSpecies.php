<?php

namespace App\Filament\Resources\PlantSpeciesResource\Pages;

use App\Filament\Resources\PlantSpeciesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlantSpecies extends ListRecords
{
    protected static string $resource = PlantSpeciesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
