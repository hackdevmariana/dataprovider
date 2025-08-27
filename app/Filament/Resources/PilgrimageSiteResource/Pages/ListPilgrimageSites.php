<?php

namespace App\Filament\Resources\PilgrimageSiteResource\Pages;

use App\Filament\Resources\PilgrimageSiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPilgrimageSites extends ListRecords
{
    protected static string $resource = PilgrimageSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
