<?php

namespace App\Filament\Resources\BillSimulatorResource\Pages;

use App\Filament\Resources\BillSimulatorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillSimulators extends ListRecords
{
    protected static string $resource = BillSimulatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
