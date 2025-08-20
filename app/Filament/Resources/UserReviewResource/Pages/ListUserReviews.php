<?php

namespace App\Filament\Resources\UserReviewResource\Pages;

use App\Filament\Resources\UserReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserReviews extends ListRecords
{
    protected static string $resource = UserReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
