<?php

namespace App\Filament\Resources\TrendingTopicResource\Pages;

use App\Filament\Resources\TrendingTopicResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrendingTopic extends EditRecord
{
    protected static string $resource = TrendingTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
