<?php

namespace App\Filament\Resources\CarbonEquivalenceResource\Pages;

use App\Filament\Resources\CarbonEquivalenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarbonEquivalences extends ListRecords
{
    protected static string $resource = CarbonEquivalenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
