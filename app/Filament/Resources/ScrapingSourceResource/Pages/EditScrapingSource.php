<?php

namespace App\Filament\Resources\ScrapingSourceResource\Pages;

use App\Filament\Resources\ScrapingSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScrapingSource extends EditRecord
{
    protected static string $resource = ScrapingSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
