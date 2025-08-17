<?php

namespace App\Filament\Resources\UserGeneratedContentResource\Pages;

use App\Filament\Resources\UserGeneratedContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserGeneratedContent extends EditRecord
{
    protected static string $resource = UserGeneratedContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
