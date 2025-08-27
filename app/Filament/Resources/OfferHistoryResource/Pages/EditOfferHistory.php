<?php

namespace App\Filament\Resources\OfferHistoryResource\Pages;

use App\Filament\Resources\OfferHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOfferHistory extends EditRecord
{
    protected static string $resource = OfferHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
