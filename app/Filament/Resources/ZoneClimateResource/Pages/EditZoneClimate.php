<?php

namespace App\Filament\Resources\ZoneClimateResource\Pages;

use App\Filament\Resources\ZoneClimateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditZoneClimate extends EditRecord
{
    protected static string $resource = ZoneClimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
