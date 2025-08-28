<?php

namespace App\Filament\Resources\FestivalScheduleResource\Pages;

use App\Filament\Resources\FestivalScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFestivalSchedule extends ViewRecord
{
    protected static string $resource = FestivalScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
