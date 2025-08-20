<?php

namespace App\Filament\Resources\UserEndorsementResource\Pages;

use App\Filament\Resources\UserEndorsementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserEndorsement extends EditRecord
{
    protected static string $resource = UserEndorsementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
