<?php

namespace App\Filament\Resources\ElectricityPriceIntervalResource\Pages;

use App\Filament\Resources\ElectricityPriceIntervalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElectricityPriceInterval extends EditRecord
{
    protected static string $resource = ElectricityPriceIntervalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
