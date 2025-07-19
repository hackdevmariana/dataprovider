<?php

namespace App\Filament\Resources\ElectricityOfferResource\Pages;

use App\Filament\Resources\ElectricityOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListElectricityOffers extends ListRecords
{
    protected static string $resource = ElectricityOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
