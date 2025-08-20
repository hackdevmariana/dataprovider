<?php

namespace App\Filament\Resources\ContentHashtagResource\Pages;

use App\Filament\Resources\ContentHashtagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContentHashtags extends ListRecords
{
    protected static string $resource = ContentHashtagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
