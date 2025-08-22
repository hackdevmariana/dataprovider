<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScrapingSourceResource\Pages;
use App\Filament\Resources\ScrapingSourceResource\RelationManagers;
use App\Models\ScrapingSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;


class ScrapingSourceResource extends Resource
{
    protected static ?string $model = ScrapingSource::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';
    protected static ?string $navigationGroup = 'General & Stats';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('url')
                ->required()
                ->url()
                ->maxLength(2048),

            Textarea::make('description')
                ->maxLength(1000)
                ->nullable(),

            Toggle::make('active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('url')->limit(50),
            TextColumn::make('description')->limit(50)->wrap(),
            IconColumn::make('active')->boolean(),
            TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => Pages\ListScrapingSources::route('/'),
            'create' => Pages\CreateScrapingSource::route('/create'),
            'edit' => Pages\EditScrapingSource::route('/{record}/edit'),
        ];
    }
}
