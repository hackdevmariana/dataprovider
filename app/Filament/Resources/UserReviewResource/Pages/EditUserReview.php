<?php

namespace App\Filament\Resources\UserReviewResource\Pages;

use App\Filament\Resources\UserReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserReview extends EditRecord
{
    protected static string $resource = UserReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
