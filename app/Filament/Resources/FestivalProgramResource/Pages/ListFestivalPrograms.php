<?php

namespace App\Filament\Resources\FestivalProgramResource\Pages;

use App\Filament\Resources\FestivalProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFestivalPrograms extends ListRecords
{
    protected static string $resource = FestivalProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
