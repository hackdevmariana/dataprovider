<?php

namespace App\Filament\Resources\ReputationTransactionResource\Pages;

use App\Filament\Resources\ReputationTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReputationTransaction extends EditRecord
{
    protected static string $resource = ReputationTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
