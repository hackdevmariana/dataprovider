<?php

namespace App\Filament\Resources\PersonWorkResource\Pages;

use App\Filament\Resources\PersonWorkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonWorks extends ListRecords
{
    protected static string $resource = PersonWorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
