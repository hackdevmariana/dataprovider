<?php

namespace App\Filament\Resources\FestivalActivityResource\Pages;

use App\Filament\Resources\FestivalActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFestivalActivities extends ListRecords
{
    protected static string $resource = FestivalActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
