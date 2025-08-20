<?php

namespace App\Filament\Resources\ProductionRightResource\Pages;

use App\Filament\Resources\ProductionRightResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductionRight extends EditRecord
{
    protected static string $resource = ProductionRightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
