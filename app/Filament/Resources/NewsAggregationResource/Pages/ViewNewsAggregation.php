<?php

namespace App\Filament\Resources\NewsAggregationResource\Pages;

use App\Filament\Resources\NewsAggregationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNewsAggregation extends ViewRecord
{
    protected static string $resource = NewsAggregationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),
        ];
    }
}
