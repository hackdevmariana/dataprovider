<?php

namespace App\Filament\Resources\HistoricalEventResource\Pages;

use App\Filament\Resources\HistoricalEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistoricalEvents extends ListRecords
{
    protected static string $resource = HistoricalEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
