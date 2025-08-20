<?php

namespace App\Filament\Resources\RoofMarketplaceResource\Pages;

use App\Filament\Resources\RoofMarketplaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoofMarketplaces extends ListRecords
{
    protected static string $resource = RoofMarketplaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
