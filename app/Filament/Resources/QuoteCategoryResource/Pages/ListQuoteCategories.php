<?php

namespace App\Filament\Resources\QuoteCategoryResource\Pages;

use App\Filament\Resources\QuoteCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuoteCategories extends ListRecords
{
    protected static string $resource = QuoteCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
