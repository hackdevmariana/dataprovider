<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FestivalResource\Pages;
use App\Filament\Resources\FestivalResource\RelationManagers;
use App\Models\Festival;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FestivalResource extends Resource
{
    protected static ?string $model = Festival::class;
    protected static ?string $navigationGroup = 'Events & Calendar';


    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')->rows(3)->nullable(),
            Forms\Components\Select::make('location_id')
                ->relationship('location', 'name')
                ->label('Municipality')
                ->searchable()
                ->preload()
                ->nullable(),
            Forms\Components\TextInput::make('month')->numeric()->minValue(1)->maxValue(12),
            Forms\Components\TextInput::make('usual_days')->nullable(),
            Forms\Components\Toggle::make('recurring')->default(false),
            Forms\Components\TextInput::make('logo_url')->label('Logo URL')->nullable(),
            Forms\Components\TextInput::make('color_theme')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('location.name')->label('Municipality'),
            Tables\Columns\IconColumn::make('recurring')->boolean(),
            Tables\Columns\TextColumn::make('month'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\FestivalResource\RelationManagers\EventRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFestivals::route('/'),
            'create' => Pages\CreateFestival::route('/create'),
            'edit' => Pages\EditFestival::route('/{record}/edit'),
        ];
    }
}
