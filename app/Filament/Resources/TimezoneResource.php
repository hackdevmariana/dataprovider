<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimezoneResource\Pages;
use App\Filament\Resources\TimezoneResource\RelationManagers;
use App\Models\Timezone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;


class TimezoneResource extends Resource
{
    protected static ?string $model = Timezone::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Lugares';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('offset')->required()->maxLength(10),
            TextInput::make('dst_offset')->nullable()->maxLength(10),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('offset')->label('UTC Offset'),
            TextColumn::make('dst_offset')->label('DST Offset')->default('-'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimezones::route('/'),
            'create' => Pages\CreateTimezone::route('/create'),
            'edit' => Pages\EditTimezone::route('/{record}/edit'),
        ];
    }
}
