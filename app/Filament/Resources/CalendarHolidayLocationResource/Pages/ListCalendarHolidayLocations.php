<?php

namespace App\Filament\Resources\CalendarHolidayLocationResource\Pages;

use App\Filament\Resources\CalendarHolidayLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalendarHolidayLocations extends ListRecords
{
    protected static string $resource = CalendarHolidayLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
