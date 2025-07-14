<?php

namespace App\Filament\Resources\VisualIdentityResource\Pages;

use App\Filament\Resources\VisualIdentityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisualIdentity extends EditRecord
{
    protected static string $resource = VisualIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
