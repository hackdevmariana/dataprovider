<?php

namespace App\Filament\Resources\EnergyServiceResource\Pages;

use App\Filament\Resources\EnergyServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnergyServices extends ListRecords
{
    protected static string $resource = EnergyServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
