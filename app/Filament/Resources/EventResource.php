<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Events & Calendar';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')->nullable(),
            Forms\Components\DateTimePicker::make('start_datetime')->required(),
            Forms\Components\DateTimePicker::make('end_datetime')->nullable(),

            Forms\Components\Select::make('venue_id')->relationship('venue', 'name')->nullable()->searchable()->preload(),
            Forms\Components\Select::make('event_type_id')->relationship('eventType', 'name')->nullable()->searchable()->preload(),
            Forms\Components\Select::make('festival_id')->relationship('festival', 'name')->nullable()->searchable()->preload(),
            Forms\Components\Select::make('language_id')->relationship('language', 'language')->nullable()->searchable()->preload(),
            Forms\Components\Select::make('timezone_id')->relationship('timezone', 'name')->nullable()->searchable()->preload(),
            Forms\Components\Select::make('municipality_id')->relationship('municipality', 'name')->nullable()->searchable()->preload(),
            Forms\Components\Select::make('point_of_interest_id')->relationship('pointOfInterest', 'name')->nullable()->searchable()->preload(),
            Forms\Components\Select::make('work_id')->relationship('work', 'title')->nullable()->searchable()->preload(),

            Forms\Components\TextInput::make('price')->numeric()->nullable(),
            Forms\Components\Toggle::make('is_free')->default(false),
            Forms\Components\TextInput::make('audience_size_estimate')->numeric()->nullable(),
            Forms\Components\TextInput::make('source_url')->url()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('start_datetime')->dateTime(),
            Tables\Columns\TextColumn::make('venue.name')->label('Venue'),
            Tables\Columns\IconColumn::make('is_free')->boolean(),
            Tables\Columns\TextColumn::make('price'),
        ])->defaultSort('start_datetime');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\EventResource\RelationManagers\ArtistRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
