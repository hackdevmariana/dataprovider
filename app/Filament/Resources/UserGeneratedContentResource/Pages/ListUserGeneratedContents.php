<?php

namespace App\Filament\Resources\UserGeneratedContentResource\Pages;

use App\Filament\Resources\UserGeneratedContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserGeneratedContents extends ListRecords
{
    protected static string $resource = UserGeneratedContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
