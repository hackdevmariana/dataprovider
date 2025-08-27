<?php

namespace App\Filament\Resources\EnergyServiceResource\Pages;

use App\Filament\Resources\EnergyServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnergyService extends EditRecord
{
    protected static string $resource = EnergyServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
