<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueResource\Pages;
use App\Filament\Resources\VenueResource\RelationManagers;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Calendario y eventos';


    protected static ?string $label = 'Venue';
    protected static ?string $pluralLabel = 'Venues';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('slug')->required()->maxLength(255),
                Forms\Components\TextInput::make('address')->required()->maxLength(255),

                Forms\Components\Select::make('municipality_id')
                    ->relationship('municipality', 'name')
                    ->searchable()
                    ->required()
                    ->label('Municipality'),

                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('capacity')
                    ->numeric()
                    ->nullable(),

                Forms\Components\Textarea::make('description')->nullable(),

                Forms\Components\Select::make('venue_type_id')
                    ->label('Venue Type')
                    ->relationship('venueType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),


                Forms\Components\Select::make('venue_status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'closed' => 'Closed',
                        'under_construction' => 'Under Construction',
                    ]),

                Forms\Components\Toggle::make('is_verified')
                    ->label('Verified')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('municipality.name')->label('Municipality')->sortable(),
                Tables\Columns\TextColumn::make('venue_type')->label('Type')->sortable(),
                Tables\Columns\TextColumn::make('venue_status')->label('Status')->sortable(),
                Tables\Columns\IconColumn::make('is_verified')->boolean(),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
