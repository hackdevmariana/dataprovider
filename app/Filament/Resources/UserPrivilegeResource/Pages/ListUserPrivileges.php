<?php

namespace App\Filament\Resources\UserPrivilegeResource\Pages;

use App\Filament\Resources\UserPrivilegeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserPrivileges extends ListRecords
{
    protected static string $resource = UserPrivilegeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
