<?php

namespace App\Filament\Resources\ElectricityOfferResource\Pages;

use App\Filament\Resources\ElectricityOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElectricityOffer extends EditRecord
{
    protected static string $resource = ElectricityOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
