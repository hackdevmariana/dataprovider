<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RelationshipTypeResource\Pages;
use App\Filament\Resources\RelationshipTypeResource\RelationManagers;
use App\Models\RelationshipType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class RelationshipTypeResource extends Resource
{
    protected static ?string $model = RelationshipType::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationGroup = 'People & Organizations';

    protected static ?int $navigationSort = 10;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(40),
                TextInput::make('slug')->required()->maxLength(40),
                TextInput::make('reciprocal_slug')->maxLength(40),
                Select::make('category')
                    ->options([
                        'family' => 'Family',
                        'legal' => 'Legal',
                        'sentimental' => 'Sentimental',
                        'otro' => 'Otro',
                    ])
                    ->required(),
                TextInput::make('degree')->numeric()->minValue(0)->maxValue(5),
                Forms\Components\Toggle::make('gender_specific'),
                Forms\Components\Toggle::make('is_symmetrical'),
                Textarea::make('description')->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('slug')->sortable(),
                TextColumn::make('reciprocal_slug')->label('Reciprocal')->sortable(),
                TextColumn::make('category')->sortable(),
                TextColumn::make('degree'),
                IconColumn::make('gender_specific')->boolean(),
                IconColumn::make('is_symmetrical')->boolean(),
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
            'index' => Pages\ListRelationshipTypes::route('/'),
            'create' => Pages\CreateRelationshipType::route('/create'),
            'edit' => Pages\EditRelationshipType::route('/{record}/edit'),
        ];
    }
}
