<?php

namespace App\Filament\Resources\WeatherAndSolarDataResource\Pages;

use App\Filament\Resources\WeatherAndSolarDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeatherAndSolarData extends EditRecord
{
    protected static string $resource = WeatherAndSolarDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
