<?php

namespace App\Filament\Resources\CompanyCertificationResource\Pages;

use App\Filament\Resources\CompanyCertificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyCertification extends ViewRecord
{
    protected static string $resource = CompanyCertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
