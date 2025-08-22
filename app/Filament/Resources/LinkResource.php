<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Filament\Resources\LinkResource\RelationManagers;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;


class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationGroup = 'General & Stats';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('url')
                ->label('URL')
                ->url()
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('label')
                ->label('Label')
                ->maxLength(255)
                ->nullable(),

            Forms\Components\Select::make('type')
                ->label('Type')
                ->options([
                    'wikipedia' => 'Wikipedia',
                    'imdb' => 'IMDb',
                    'official' => 'Official',
                    'twitter' => 'Twitter',
                    'instagram' => 'Instagram',
                    'other' => 'Other',
                ])
                ->required(),

            Forms\Components\Toggle::make('is_primary')
                ->label('Is Primary')
                ->default(false),

            Forms\Components\Toggle::make('opens_in_new_tab')
                ->label('Opens in New Tab')
                ->default(false),

            // Morph relation fields for related model
            Forms\Components\Select::make('related_type')
                ->label('Related Model')
                ->options([
                    'App\Models\Person' => 'Person',
                    'App\Models\Work' => 'Work',
                    'App\Models\CalendarHoliday' => 'Calendar Holiday',
                    // añade aquí otros modelos relacionados si los tienes
                ])
                ->required(),

            Forms\Components\TextInput::make('related_id')
                ->label('Related Model ID')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')->label('URL')->limit(50)->url(fn ($record) => $record->url),
                Tables\Columns\TextColumn::make('label')->label('Label')->limit(30),
                Tables\Columns\TextColumn::make('type')->label('Type')->sortable(),

                IconColumn::make('is_primary')
                    ->label('Primary')
                    ->boolean(),

                IconColumn::make('opens_in_new_tab')
                    ->label('New Tab')
                    ->boolean(),

                Tables\Columns\TextColumn::make('related_type')->label('Related Model'),
                Tables\Columns\TextColumn::make('related_id')->label('Related ID'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'edit' => Pages\EditLink::route('/{record}/edit'),
        ];
    }
}
