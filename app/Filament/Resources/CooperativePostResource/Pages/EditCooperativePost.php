<?php

namespace App\Filament\Resources\CooperativePostResource\Pages;

use App\Filament\Resources\CooperativePostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCooperativePost extends EditRecord
{
    protected static string $resource = CooperativePostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
