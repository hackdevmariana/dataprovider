<?php

namespace App\Filament\Resources\VisualIdentityResource\Pages;

use App\Filament\Resources\VisualIdentityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisualIdentities extends ListRecords
{
    protected static string $resource = VisualIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
