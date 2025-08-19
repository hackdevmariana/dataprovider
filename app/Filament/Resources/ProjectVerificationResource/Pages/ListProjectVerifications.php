<?php

namespace App\Filament\Resources\ProjectVerificationResource\Pages;

use App\Filament\Resources\ProjectVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectVerifications extends ListRecords
{
    protected static string $resource = ProjectVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
