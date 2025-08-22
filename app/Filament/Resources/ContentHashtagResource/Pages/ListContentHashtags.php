<?php

namespace App\Filament\Resources\ContentHashtagResource\Pages;

use App\Filament\Resources\ContentHashtagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContentHashtags extends ListRecords
{
    protected static string $resource = ContentHashtagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Se pueden agregar widgets de estadísticas aquí en el futuro
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Se pueden agregar widgets de resumen aquí en el futuro
        ];
    }
}
