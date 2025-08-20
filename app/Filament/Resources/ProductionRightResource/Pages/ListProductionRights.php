<?php

namespace App\Filament\Resources\ProductionRightResource\Pages;

use App\Filament\Resources\ProductionRightResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductionRights extends ListRecords
{
    protected static string $resource = ProductionRightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
