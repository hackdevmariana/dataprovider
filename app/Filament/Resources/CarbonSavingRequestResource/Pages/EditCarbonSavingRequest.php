<?php

namespace App\Filament\Resources\CarbonSavingRequestResource\Pages;

use App\Filament\Resources\CarbonSavingRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarbonSavingRequest extends EditRecord
{
    protected static string $resource = CarbonSavingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
