<?php

namespace App\Filament\Resources\TimezoneResource\Pages;

use App\Filament\Resources\TimezoneResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimezone extends EditRecord
{
    protected static string $resource = TimezoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
