<?php

namespace App\Filament\Resources\FestivalScheduleResource\Pages;

use App\Filament\Resources\FestivalScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFestivalSchedules extends ListRecords
{
    protected static string $resource = FestivalScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
