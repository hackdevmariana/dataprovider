<?php

namespace App\Filament\Resources\PriceForecastResource\Pages;

use App\Filament\Resources\PriceForecastResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPriceForecasts extends ListRecords
{
    protected static string $resource = PriceForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
