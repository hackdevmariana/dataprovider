<?php

namespace App\Filament\Resources\NewsAggregationResource\Pages;

use App\Filament\Resources\NewsAggregationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewsAggregation extends EditRecord
{
    protected static string $resource = NewsAggregationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
