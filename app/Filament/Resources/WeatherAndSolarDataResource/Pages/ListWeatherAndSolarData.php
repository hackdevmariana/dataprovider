<?php

namespace App\Filament\Resources\WeatherAndSolarDataResource\Pages;

use App\Filament\Resources\WeatherAndSolarDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeatherAndSolarData extends ListRecords
{
    protected static string $resource = WeatherAndSolarDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
