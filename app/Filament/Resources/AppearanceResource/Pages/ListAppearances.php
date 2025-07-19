<?php

namespace App\Filament\Resources\AppearanceResource\Pages;

use App\Filament\Resources\AppearanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppearances extends ListRecords
{
    protected static string $resource = AppearanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
