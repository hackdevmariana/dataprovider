<?php

namespace App\Filament\Resources\SocialComparisonResource\Pages;

use App\Filament\Resources\SocialComparisonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSocialComparison extends EditRecord
{
    protected static string $resource = SocialComparisonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
