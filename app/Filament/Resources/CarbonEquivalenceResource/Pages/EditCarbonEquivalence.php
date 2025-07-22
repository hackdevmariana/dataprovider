<?php

namespace App\Filament\Resources\CarbonEquivalenceResource\Pages;

use App\Filament\Resources\CarbonEquivalenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarbonEquivalence extends EditRecord
{
    protected static string $resource = CarbonEquivalenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
