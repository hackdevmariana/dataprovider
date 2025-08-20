<?php

namespace App\Filament\Resources\TopicPostResource\Pages;

use App\Filament\Resources\TopicPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTopicPost extends EditRecord
{
    protected static string $resource = TopicPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
