<?php

namespace App\Filament\Resources\EnergyCertificateResource\Pages;

use App\Filament\Resources\EnergyCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnergyCertificate extends EditRecord
{
    protected static string $resource = EnergyCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
