<?php

namespace App\Filament\Resources\OfferComparisonResource\Pages;

use App\Filament\Resources\OfferComparisonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfferComparisons extends ListRecords
{
    protected static string $resource = OfferComparisonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
