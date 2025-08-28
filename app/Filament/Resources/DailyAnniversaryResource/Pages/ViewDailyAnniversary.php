<?php

namespace App\Filament\Resources\DailyAnniversaryResource\Pages;

use App\Filament\Resources\DailyAnniversaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDailyAnniversary extends ViewRecord
{
    protected static string $resource = DailyAnniversaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
