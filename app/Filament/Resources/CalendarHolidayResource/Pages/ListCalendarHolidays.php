<?php

namespace App\Filament\Resources\CalendarHolidayResource\Pages;

use App\Filament\Resources\CalendarHolidayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalendarHolidays extends ListRecords
{
    protected static string $resource = CalendarHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
