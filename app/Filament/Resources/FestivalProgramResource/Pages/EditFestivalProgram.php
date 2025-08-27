<?php

namespace App\Filament\Resources\FestivalProgramResource\Pages;

use App\Filament\Resources\FestivalProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFestivalProgram extends EditRecord
{
    protected static string $resource = FestivalProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
