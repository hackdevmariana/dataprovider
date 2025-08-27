<?php

namespace App\Filament\Resources\CompanyCertificationResource\Pages;

use App\Filament\Resources\CompanyCertificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyCertifications extends ListRecords
{
    protected static string $resource = CompanyCertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
