<?php

namespace App\Filament\Resources\FestivalProgramResource\Pages;

use App\Filament\Resources\FestivalProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFestivalProgram extends ViewRecord
{
    protected static string $resource = FestivalProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
