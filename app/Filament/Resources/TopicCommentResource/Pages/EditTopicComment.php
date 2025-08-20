<?php

namespace App\Filament\Resources\TopicCommentResource\Pages;

use App\Filament\Resources\TopicCommentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTopicComment extends EditRecord
{
    protected static string $resource = TopicCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
