<?php

namespace App\Filament\Resources\CalendarHolidayResource\Pages;

use App\Filament\Resources\CalendarHolidayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalendarHoliday extends EditRecord
{
    protected static string $resource = CalendarHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
