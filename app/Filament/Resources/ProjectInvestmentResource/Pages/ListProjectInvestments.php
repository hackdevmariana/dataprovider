<?php

namespace App\Filament\Resources\ProjectInvestmentResource\Pages;

use App\Filament\Resources\ProjectInvestmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectInvestments extends ListRecords
{
    protected static string $resource = ProjectInvestmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
