<?php

namespace App\Filament\Resources\PersonProfessionResource\Pages;

use App\Filament\Resources\PersonProfessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonProfessions extends ListRecords
{
    protected static string $resource = PersonProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
