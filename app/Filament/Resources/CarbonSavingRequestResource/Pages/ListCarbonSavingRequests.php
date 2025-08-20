<?php

namespace App\Filament\Resources\CarbonSavingRequestResource\Pages;

use App\Filament\Resources\CarbonSavingRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarbonSavingRequests extends ListRecords
{
    protected static string $resource = CarbonSavingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
