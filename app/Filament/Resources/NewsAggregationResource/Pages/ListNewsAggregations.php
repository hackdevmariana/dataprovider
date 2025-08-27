<?php

namespace App\Filament\Resources\NewsAggregationResource\Pages;

use App\Filament\Resources\NewsAggregationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsAggregations extends ListRecords
{
    protected static string $resource = NewsAggregationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
