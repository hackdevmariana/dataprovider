<?php

namespace App\Filament\Resources\RoofMarketplaceResource\Pages;

use App\Filament\Resources\RoofMarketplaceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoofMarketplace extends EditRecord
{
    protected static string $resource = RoofMarketplaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
