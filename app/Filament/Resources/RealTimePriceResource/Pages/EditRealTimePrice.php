<?php

namespace App\Filament\Resources\RealTimePriceResource\Pages;

use App\Filament\Resources\RealTimePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRealTimePrice extends EditRecord
{
    protected static string $resource = RealTimePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
