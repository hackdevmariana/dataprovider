<?php

namespace App\Filament\Resources\EnergyServiceResource\Pages;

use App\Filament\Resources\EnergyServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEnergyService extends ViewRecord
{
    protected static string $resource = EnergyServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
