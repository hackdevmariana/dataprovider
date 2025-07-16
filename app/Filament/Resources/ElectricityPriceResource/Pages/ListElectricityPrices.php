<?php

namespace App\Filament\Resources\ElectricityPriceResource\Pages;

use App\Filament\Resources\ElectricityPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListElectricityPrices extends ListRecords
{
    protected static string $resource = ElectricityPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
