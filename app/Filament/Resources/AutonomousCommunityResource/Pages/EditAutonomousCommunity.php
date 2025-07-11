<?php

namespace App\Filament\Resources\AutonomousCommunityResource\Pages;

use App\Filament\Resources\AutonomousCommunityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutonomousCommunity extends EditRecord
{
    protected static string $resource = AutonomousCommunityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
