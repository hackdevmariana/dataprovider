<?php

namespace App\Filament\Resources\ProjectUpdateResource\Pages;

use App\Filament\Resources\ProjectUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectUpdates extends ListRecords
{
    protected static string $resource = ProjectUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
