<?php

namespace App\Filament\Resources\PersonProfessionResource\Pages;

use App\Filament\Resources\PersonProfessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonProfession extends EditRecord
{
    protected static string $resource = PersonProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
