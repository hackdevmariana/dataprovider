<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtistResource\Pages;
use App\Filament\Resources\ArtistResource\RelationManagers;
use App\Models\Artist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ArtistResource\RelationManagers\ArtistGroupRelationManager;


class ArtistResource extends Resource
{
    protected static ?string $model = Artist::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Eventos culturales';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\Textarea::make('description')->nullable(),
            Forms\Components\DatePicker::make('birth_date')->nullable(),
            Forms\Components\TextInput::make('genre')->nullable(),
            Forms\Components\Select::make('language_id')
                ->relationship('language', 'language')
                ->searchable()
                ->preload()
                ->nullable(),
            Forms\Components\TextInput::make('stage_name')->nullable(),
            Forms\Components\TextInput::make('group_name')->nullable(),
            Forms\Components\TextInput::make('active_years_start')->numeric()->nullable(),
            Forms\Components\TextInput::make('active_years_end')->numeric()->nullable(),
            Forms\Components\Textarea::make('bio')->nullable(),
            Forms\Components\TextInput::make('photo')->nullable(),
            Forms\Components\Textarea::make('social_links')->json()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('stage_name'),
            Tables\Columns\TextColumn::make('genre'),
            Tables\Columns\TextColumn::make('language.language')->label('Language'),
        ])->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            ArtistGroupRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArtists::route('/'),
            'create' => Pages\CreateArtist::route('/create'),
            'edit' => Pages\EditArtist::route('/{record}/edit'),
        ];
    }
}
