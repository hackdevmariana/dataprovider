<?php

namespace App\Filament\Resources\TopicCommentResource\Pages;

use App\Filament\Resources\TopicCommentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTopicComments extends ListRecords
{
    protected static string $resource = TopicCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
