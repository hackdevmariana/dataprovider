<?php

namespace App\Filament\Resources\PilgrimageSiteResource\Pages;

use App\Filament\Resources\PilgrimageSiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPilgrimageSite extends EditRecord
{
    protected static string $resource = PilgrimageSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
