<?php

namespace App\Filament\Resources\NewsSourceResource\Pages;

use App\Filament\Resources\NewsSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNewsSource extends ViewRecord
{
    protected static string $resource = NewsSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
