<?php

namespace App\Filament\Resources\ContentVoteResource\Pages;

use App\Filament\Resources\ContentVoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContentVote extends EditRecord
{
    protected static string $resource = ContentVoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
