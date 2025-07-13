<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkResource\Pages;
use App\Filament\Resources\WorkResource\RelationManagers;
use App\Models\Work;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkResource extends Resource
{
    protected static ?string $model = Work::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'General';
    protected static ?string $label = 'Work';
    protected static ?string $pluralLabel = 'Works';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('type')
                    ->options([
                        'book' => 'Book',
                        'movie' => 'Movie',
                        'tv_show' => 'TV Show',
                        'theatre_play' => 'Theatre Play',
                        'article' => 'Article',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->nullable(),

                Forms\Components\TextInput::make('release_year')
                    ->label('Release Year')
                    ->numeric()
                    ->nullable(),

                Forms\Components\Select::make('person_id')
                    ->relationship('language', 'language')
                    ->searchable()
                    ->preload()
                    ->label('Person')
                    ->nullable(),

                Forms\Components\TextInput::make('genre')
                    ->nullable()
                    ->maxLength(255),

                Forms\Components\Select::make('language_id')
                    ->relationship('language', 'language')
                    ->searchable()
                    ->preload()
                    ->label('Language')
                    ->nullable(),

                Forms\Components\Select::make('link_id')
                    ->relationship('link', 'url')
                    ->searchable()
                    ->preload()
                    ->label('Link')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->sortable(),
                Tables\Columns\TextColumn::make('release_year')->sortable(),
                Tables\Columns\TextColumn::make('person.name')->label('Person')->searchable(),
                Tables\Columns\TextColumn::make('language.language')->label('Language'),
                Tables\Columns\TextColumn::make('link.url')
                    ->label('Link')
                    ->limit(50)
                    ->url(fn ($record) => $record->link?->url),
            ])
            ->filters([
                //
            ])
            ->defaultSort('title');
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
            'index' => Pages\ListWorks::route('/'),
            'create' => Pages\CreateWork::route('/create'),
            'edit' => Pages\EditWork::route('/{record}/edit'),
        ];
    }
}
