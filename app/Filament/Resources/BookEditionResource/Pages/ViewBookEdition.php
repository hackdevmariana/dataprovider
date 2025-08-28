<?php

namespace App\Filament\Resources\BookEditionResource\Pages;

use App\Filament\Resources\BookEditionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookEdition extends ViewRecord
{
    protected static string $resource = BookEditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
