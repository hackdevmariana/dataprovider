<?php

namespace App\Filament\Resources\SocialInteractionResource\Pages;

use App\Filament\Resources\SocialInteractionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocialInteractions extends ListRecords
{
    protected static string $resource = SocialInteractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
