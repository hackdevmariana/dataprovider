<?php

namespace App\Filament\Resources\ActivityFeedResource\Pages;

use App\Filament\Resources\ActivityFeedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityFeed extends EditRecord
{
    protected static string $resource = ActivityFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
