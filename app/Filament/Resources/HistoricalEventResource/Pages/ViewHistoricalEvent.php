<?php

namespace App\Filament\Resources\HistoricalEventResource\Pages;

use App\Filament\Resources\HistoricalEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHistoricalEvent extends ViewRecord
{
    protected static string $resource = HistoricalEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
