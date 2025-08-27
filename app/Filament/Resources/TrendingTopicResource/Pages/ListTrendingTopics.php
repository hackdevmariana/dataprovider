<?php

namespace App\Filament\Resources\TrendingTopicResource\Pages;

use App\Filament\Resources\TrendingTopicResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrendingTopics extends ListRecords
{
    protected static string $resource = TrendingTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
