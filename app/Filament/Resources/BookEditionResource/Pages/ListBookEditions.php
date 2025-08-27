<?php

namespace App\Filament\Resources\BookEditionResource\Pages;

use App\Filament\Resources\BookEditionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookEditions extends ListRecords
{
    protected static string $resource = BookEditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
