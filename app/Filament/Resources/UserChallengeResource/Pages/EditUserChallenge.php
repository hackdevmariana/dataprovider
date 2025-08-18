<?php

namespace App\Filament\Resources\UserChallengeResource\Pages;

use App\Filament\Resources\UserChallengeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserChallenge extends EditRecord
{
    protected static string $resource = UserChallengeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
