<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaOutletResource\Pages;
use App\Filament\Resources\MediaOutletResource\RelationManagers;
use App\Models\MediaOutlet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MediaOutletResource extends Resource
{
    protected static ?string $model = MediaOutlet::class;

    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $label = 'Medio de comunicación';
    protected static ?string $pluralLabel = 'Medios de comunicación';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'newspaper' => 'Newspaper',
                    'tv' => 'TV',
                    'radio' => 'Radio',
                    'blog' => 'Blog',
                    'magazine' => 'Magazine',
                ])
                ->required(),
            Forms\Components\TextInput::make('website')->url()->nullable(),
            Forms\Components\TextInput::make('headquarters_location')->nullable(),
            Forms\Components\Select::make('municipality_id')
                ->relationship('municipality', 'name')
                ->searchable()
                ->nullable(),
            Forms\Components\TextInput::make('language')->nullable(),
            Forms\Components\TextInput::make('circulation')->numeric()->nullable(),
            Forms\Components\TextInput::make('founding_year')->numeric()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('website')
                    ->limit(30)
                    ->url(fn($record) => $record->website)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('language')
                    ->sortable(),
                Tables\Columns\TextColumn::make('founding_year')
                    ->sortable(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            MediaOutletResource\RelationManagers\MediaContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaOutlets::route('/'),
            'create' => Pages\CreateMediaOutlet::route('/create'),
            'edit' => Pages\EditMediaOutlet::route('/{record}/edit'),
        ];
    }
}
