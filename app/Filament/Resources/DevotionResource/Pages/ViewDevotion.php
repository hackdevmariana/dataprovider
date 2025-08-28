<?php

namespace App\Filament\Resources\DevotionResource\Pages;

use App\Filament\Resources\DevotionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDevotion extends ViewRecord
{
    protected static string $resource = DevotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
