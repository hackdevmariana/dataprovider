<?php

namespace App\Filament\Resources\PersonWorkResource\Pages;

use App\Filament\Resources\PersonWorkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonWork extends EditRecord
{
    protected static string $resource = PersonWorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
