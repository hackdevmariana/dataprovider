<?php

namespace App\Filament\Resources\TopicFollowingResource\Pages;

use App\Filament\Resources\TopicFollowingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTopicFollowing extends EditRecord
{
    protected static string $resource = TopicFollowingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
