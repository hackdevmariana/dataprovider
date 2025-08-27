<?php

namespace App\Filament\Resources\DailyAnniversaryResource\Pages;

use App\Filament\Resources\DailyAnniversaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailyAnniversary extends EditRecord
{
    protected static string $resource = DailyAnniversaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
