<?php

namespace App\Filament\Resources\UserEndorsementResource\Pages;

use App\Filament\Resources\UserEndorsementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserEndorsements extends ListRecords
{
    protected static string $resource = UserEndorsementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
