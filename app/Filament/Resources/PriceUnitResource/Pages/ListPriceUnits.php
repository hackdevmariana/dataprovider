<?php

namespace App\Filament\Resources\PriceUnitResource\Pages;

use App\Filament\Resources\PriceUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPriceUnits extends ListRecords
{
    protected static string $resource = PriceUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
