<?php

namespace App\Filament\Resources\PriceForecastResource\Pages;

use App\Filament\Resources\PriceForecastResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPriceForecast extends ViewRecord
{
    protected static string $resource = PriceForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
