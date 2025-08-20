<?php

namespace App\Filament\Resources\CarbonCalculationResource\Pages;

use App\Filament\Resources\CarbonCalculationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarbonCalculations extends ListRecords
{
    protected static string $resource = CarbonCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
