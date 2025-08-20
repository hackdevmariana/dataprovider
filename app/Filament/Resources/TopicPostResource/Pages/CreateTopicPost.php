<?php

namespace App\Filament\Resources\TopicPostResource\Pages;

use App\Filament\Resources\TopicPostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTopicPost extends CreateRecord
{
    protected static string $resource = TopicPostResource::class;
}
