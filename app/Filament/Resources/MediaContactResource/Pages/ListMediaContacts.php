<?php

namespace App\Filament\Resources\MediaContactResource\Pages;

use App\Filament\Resources\MediaContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMediaContacts extends ListRecords
{
    protected static string $resource = MediaContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
