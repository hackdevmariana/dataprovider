<?php

namespace App\Filament\Resources\SponsoredContentResource\Pages;

use App\Filament\Resources\SponsoredContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSponsoredContent extends EditRecord
{
    protected static string $resource = SponsoredContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
