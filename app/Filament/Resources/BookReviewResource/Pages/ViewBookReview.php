<?php

namespace App\Filament\Resources\BookReviewResource\Pages;

use App\Filament\Resources\BookReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookReview extends ViewRecord
{
    protected static string $resource = BookReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('fas-edit'),
        ];
    }
}
