<?php

namespace App\Filament\Resources\TopicCommentResource\Pages;

use App\Filament\Resources\TopicCommentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTopicComment extends CreateRecord
{
    protected static string $resource = TopicCommentResource::class;
}
