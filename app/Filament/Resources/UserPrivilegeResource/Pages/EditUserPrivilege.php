<?php

namespace App\Filament\Resources\UserPrivilegeResource\Pages;

use App\Filament\Resources\UserPrivilegeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserPrivilege extends EditRecord
{
    protected static string $resource = UserPrivilegeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
