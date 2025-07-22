<?php

namespace App\Filament\Resources\CarbonSavingLogResource\Pages;

use App\Filament\Resources\CarbonSavingLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarbonSavingLog extends EditRecord
{
    protected static string $resource = CarbonSavingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
