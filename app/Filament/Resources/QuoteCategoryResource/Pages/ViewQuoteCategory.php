<?php

namespace App\Filament\Resources\QuoteCategoryResource\Pages;

use App\Filament\Resources\QuoteCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuoteCategory extends ViewRecord
{
    protected static string $resource = QuoteCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
