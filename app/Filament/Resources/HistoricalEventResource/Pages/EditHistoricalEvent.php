<?php

namespace App\Filament\Resources\HistoricalEventResource\Pages;

use App\Filament\Resources\HistoricalEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistoricalEvent extends EditRecord
{
    protected static string $resource = HistoricalEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
