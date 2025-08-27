<?php

namespace App\Filament\Resources\PriceAlertResource\Pages;

use App\Filament\Resources\PriceAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPriceAlert extends EditRecord
{
    protected static string $resource = PriceAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
