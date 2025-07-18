<?php

namespace App\Filament\Resources\ScrapingSourceResource\Pages;

use App\Filament\Resources\ScrapingSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScrapingSources extends ListRecords
{
    protected static string $resource = ScrapingSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
