<?php

namespace App\Filament\Resources\QuoteCollectionResource\Pages;

use App\Filament\Resources\QuoteCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuoteCollection extends CreateRecord
{
    protected static string $resource = QuoteCollectionResource::class;
}
