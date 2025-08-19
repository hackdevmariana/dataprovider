<?php

namespace App\Filament\Resources\ConsultationServiceResource\Pages;

use App\Filament\Resources\ConsultationServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsultationServices extends ListRecords
{
    protected static string $resource = ConsultationServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
