<?php

namespace App\Filament\Resources\TopicPostResource\Pages;

use App\Filament\Resources\TopicPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTopicPosts extends ListRecords
{
    protected static string $resource = TopicPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
