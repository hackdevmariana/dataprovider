<?php

namespace App\Filament\Resources\PriceAlertResource\Pages;

use App\Filament\Resources\PriceAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPriceAlert extends ViewRecord
{
    protected static string $resource = PriceAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
