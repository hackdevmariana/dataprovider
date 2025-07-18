<?php

namespace App\Filament\Resources\TagGroupResource\Pages;

use App\Filament\Resources\TagGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTagGroup extends EditRecord
{
    protected static string $resource = TagGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
