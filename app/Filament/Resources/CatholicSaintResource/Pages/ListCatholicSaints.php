<?php

namespace App\Filament\Resources\CatholicSaintResource\Pages;

use App\Filament\Resources\CatholicSaintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCatholicSaints extends ListRecords
{
    protected static string $resource = CatholicSaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
