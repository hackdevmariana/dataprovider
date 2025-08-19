<?php

namespace App\Filament\Resources\ExpertVerificationResource\Pages;

use App\Filament\Resources\ExpertVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpertVerifications extends ListRecords
{
    protected static string $resource = ExpertVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
