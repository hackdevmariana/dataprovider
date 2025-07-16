<?php

namespace App\Filament\Resources\EnergyCompanyResource\Pages;

use App\Filament\Resources\EnergyCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnergyCompanies extends ListRecords
{
    protected static string $resource = EnergyCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
