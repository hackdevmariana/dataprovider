<?php

namespace App\Filament\Resources\FestivalScheduleResource\Pages;

use App\Filament\Resources\FestivalScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFestivalSchedule extends EditRecord
{
    protected static string $resource = FestivalScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
