<?php

namespace App\Filament\Resources\PilgrimageSiteResource\Pages;

use App\Filament\Resources\PilgrimageSiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPilgrimageSite extends ViewRecord
{
    protected static string $resource = PilgrimageSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
