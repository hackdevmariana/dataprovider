<?php

namespace App\Filament\Resources\ProjectInvestmentResource\Pages;

use App\Filament\Resources\ProjectInvestmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectInvestment extends EditRecord
{
    protected static string $resource = ProjectInvestmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
