<?php

namespace App\Filament\Resources\ListItemResource\Pages;

use App\Filament\Resources\ListItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateListItem extends CreateRecord
{
    protected static string $resource = ListItemResource::class;
}
