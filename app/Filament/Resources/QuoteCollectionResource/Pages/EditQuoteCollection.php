<?php

namespace App\Filament\Resources\QuoteCollectionResource\Pages;

use App\Filament\Resources\QuoteCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuoteCollection extends EditRecord
{
    protected static string $resource = QuoteCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
