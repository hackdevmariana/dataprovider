<?php

namespace App\Filament\Resources\LiturgicalCalendarResource\Pages;

use App\Filament\Resources\LiturgicalCalendarResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLiturgicalCalendar extends ViewRecord
{
    protected static string $resource = LiturgicalCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
