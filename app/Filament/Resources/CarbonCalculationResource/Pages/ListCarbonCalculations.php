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

    protected function getHeaderWidgets(): array
    {
        return [
            // Se pueden agregar widgets de estadísticas aquí en el futuro
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Se pueden agregar widgets de resumen aquí en el futuro
        ];
    }
}
