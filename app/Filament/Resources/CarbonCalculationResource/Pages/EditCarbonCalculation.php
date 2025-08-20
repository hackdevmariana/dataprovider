<?php

namespace App\Filament\Resources\CarbonCalculationResource\Pages;

use App\Filament\Resources\CarbonCalculationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarbonCalculation extends EditRecord
{
    protected static string $resource = CarbonCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
