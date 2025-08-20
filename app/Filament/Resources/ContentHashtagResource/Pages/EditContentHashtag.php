<?php

namespace App\Filament\Resources\ContentHashtagResource\Pages;

use App\Filament\Resources\ContentHashtagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContentHashtag extends EditRecord
{
    protected static string $resource = ContentHashtagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
