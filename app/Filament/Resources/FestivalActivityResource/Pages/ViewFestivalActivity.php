<?php

namespace App\Filament\Resources\FestivalActivityResource\Pages;

use App\Filament\Resources\FestivalActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFestivalActivity extends ViewRecord
{
    protected static string $resource = FestivalActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
