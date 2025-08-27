<?php

namespace App\Filament\Resources\LiturgicalCalendarResource\Pages;

use App\Filament\Resources\LiturgicalCalendarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLiturgicalCalendar extends EditRecord
{
    protected static string $resource = LiturgicalCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
