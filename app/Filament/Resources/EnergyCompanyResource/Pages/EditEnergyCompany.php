<?php

namespace App\Filament\Resources\EnergyCompanyResource\Pages;

use App\Filament\Resources\EnergyCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnergyCompany extends EditRecord
{
    protected static string $resource = EnergyCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
