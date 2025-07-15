<?php

namespace App\Filament\Resources\MediaContactResource\Pages;

use App\Filament\Resources\MediaContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMediaContact extends EditRecord
{
    protected static string $resource = MediaContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
