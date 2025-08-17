<?php

namespace App\Filament\Resources\CalendarHolidayLocationResource\Pages;

use App\Filament\Resources\CalendarHolidayLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalendarHolidayLocation extends EditRecord
{
    protected static string $resource = CalendarHolidayLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
