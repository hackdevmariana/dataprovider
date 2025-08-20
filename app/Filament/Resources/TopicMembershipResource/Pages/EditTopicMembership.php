<?php

namespace App\Filament\Resources\TopicMembershipResource\Pages;

use App\Filament\Resources\TopicMembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTopicMembership extends EditRecord
{
    protected static string $resource = TopicMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
