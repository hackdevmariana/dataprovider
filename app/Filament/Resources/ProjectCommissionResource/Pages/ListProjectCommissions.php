<?php

namespace App\Filament\Resources\ProjectCommissionResource\Pages;

use App\Filament\Resources\ProjectCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectCommissions extends ListRecords
{
    protected static string $resource = ProjectCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
