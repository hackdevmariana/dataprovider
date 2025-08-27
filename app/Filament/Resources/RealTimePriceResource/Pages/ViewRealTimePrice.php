<?php

namespace App\Filament\Resources\RealTimePriceResource\Pages;

use App\Filament\Resources\RealTimePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRealTimePrice extends ViewRecord
{
    protected static string $resource = RealTimePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
