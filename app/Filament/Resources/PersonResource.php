<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationGroup = 'People & Organizations';
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('birth_name')->maxLength(255),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\DatePicker::make('birth_date'),
                Forms\Components\DatePicker::make('death_date'),
                Forms\Components\TextInput::make('birth_place')->maxLength(255),
                Forms\Components\TextInput::make('death_place')->maxLength(255),
                Forms\Components\Select::make('nationality_id')->relationship('nationality', 'name')->searchable(),
                Forms\Components\Select::make('language_id')->relationship('language', 'language')->searchable(),
                Forms\Components\Select::make('image_id')->relationship('image', 'id'),
                Forms\Components\Select::make('gender')->options([
                    'male' => 'Male',
                    'female' => 'Female',
                    'other' => 'Other',
                ]),
                Forms\Components\TextInput::make('official_website')->url(),
                Forms\Components\TextInput::make('wikidata_id'),
                Forms\Components\TextInput::make('wikipedia_url')->url(),
                Forms\Components\TextInput::make('notable_for'),
                Forms\Components\TextInput::make('occupation_summary'),
                Forms\Components\Textarea::make('short_bio'),
                Forms\Components\Textarea::make('long_bio'),
                Forms\Components\TextInput::make('source_url')->url(),
                Forms\Components\Toggle::make('is_influencer'),
                Forms\Components\TextInput::make('search_boost')->numeric()->default(0),
                Forms\Components\KeyValue::make('social_handles'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('birth_date')->date(),
                Tables\Columns\TextColumn::make('death_date')->date(),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\IconColumn::make('is_influencer')->boolean(),
            ])
            ->filters([])
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
