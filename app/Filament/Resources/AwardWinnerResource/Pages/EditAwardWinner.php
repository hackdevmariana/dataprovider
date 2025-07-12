<?php

namespace App\Filament\Resources\AwardWinnerResource\Pages;

use App\Filament\Resources\AwardWinnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwardWinner extends EditRecord
{
    protected static string $resource = AwardWinnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
