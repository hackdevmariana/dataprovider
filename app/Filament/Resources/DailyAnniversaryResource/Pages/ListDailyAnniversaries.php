<?php

namespace App\Filament\Resources\DailyAnniversaryResource\Pages;

use App\Filament\Resources\DailyAnniversaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyAnniversaries extends ListRecords
{
    protected static string $resource = DailyAnniversaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
