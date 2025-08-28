<?php

namespace App\Filament\Resources\TrendingTopicResource\Pages;

use App\Filament\Resources\TrendingTopicResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTrendingTopic extends ViewRecord
{
    protected static string $resource = TrendingTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
