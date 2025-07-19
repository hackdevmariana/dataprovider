<?php

namespace App\Filament\Resources\EnergyTransactionResource\Pages;

use App\Filament\Resources\EnergyTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnergyTransaction extends EditRecord
{
    protected static string $resource = EnergyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
