<?php

namespace App\Filament\Resources\PriceForecastResource\Pages;

use App\Filament\Resources\PriceForecastResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPriceForecast extends EditRecord
{
    protected static string $resource = PriceForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
