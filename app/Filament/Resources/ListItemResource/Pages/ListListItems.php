<?php

namespace App\Filament\Resources\ListItemResource\Pages;

use App\Filament\Resources\ListItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListItems extends ListRecords
{
    protected static string $resource = ListItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
