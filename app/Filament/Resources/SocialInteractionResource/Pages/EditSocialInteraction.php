<?php

namespace App\Filament\Resources\SocialInteractionResource\Pages;

use App\Filament\Resources\SocialInteractionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSocialInteraction extends EditRecord
{
    protected static string $resource = SocialInteractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
