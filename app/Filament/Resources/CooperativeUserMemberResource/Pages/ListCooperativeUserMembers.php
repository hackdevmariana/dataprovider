<?php

namespace App\Filament\Resources\CooperativeUserMemberResource\Pages;

use App\Filament\Resources\CooperativeUserMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCooperativeUserMembers extends ListRecords
{
    protected static string $resource = CooperativeUserMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
