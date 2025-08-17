<?php

namespace App\Filament\Resources\EnergyCertificateResource\Pages;

use App\Filament\Resources\EnergyCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnergyCertificates extends ListRecords
{
    protected static string $resource = EnergyCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
