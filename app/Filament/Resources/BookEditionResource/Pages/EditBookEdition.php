<?php

namespace App\Filament\Resources\BookEditionResource\Pages;

use App\Filament\Resources\BookEditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookEdition extends EditRecord
{
    protected static string $resource = BookEditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
