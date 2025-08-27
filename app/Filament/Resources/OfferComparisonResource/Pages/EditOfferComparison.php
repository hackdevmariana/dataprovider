<?php

namespace App\Filament\Resources\OfferComparisonResource\Pages;

use App\Filament\Resources\OfferComparisonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOfferComparison extends EditRecord
{
    protected static string $resource = OfferComparisonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
