<?php

namespace App\Filament\Resources\BillSimulatorResource\Pages;

use App\Filament\Resources\BillSimulatorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBillSimulator extends ViewRecord
{
    protected static string $resource = BillSimulatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),
        ];
    }
}
