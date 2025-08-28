<?php

namespace App\Filament\Resources\OfferComparisonResource\Pages;

use App\Filament\Resources\OfferComparisonResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOfferComparison extends ViewRecord
{
    protected static string $resource = OfferComparisonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
