<?php

namespace App\Filament\Resources\ElectricityPriceResource\Pages;

use App\Filament\Resources\ElectricityPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElectricityPrice extends EditRecord
{
    protected static string $resource = ElectricityPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
