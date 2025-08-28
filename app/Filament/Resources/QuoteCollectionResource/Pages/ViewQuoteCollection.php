<?php

namespace App\Filament\Resources\QuoteCollectionResource\Pages;

use App\Filament\Resources\QuoteCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuoteCollection extends ViewRecord
{
    protected static string $resource = QuoteCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
