<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppearanceResource\Pages;
use App\Filament\Resources\AppearanceResource\RelationManagers;
use App\Models\Appearance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class AppearanceResource extends Resource
{
    protected static ?string $model = Appearance::class;
    protected static ?string $navigationGroup = 'People & Organizations';

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('person_id')
                ->relationship('person', 'name')
                ->searchable()
                ->required(),
            TextInput::make('height_cm')->numeric()->nullable(),
            TextInput::make('weight_kg')->numeric()->nullable(),
            TextInput::make('body_type')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('person.name')->searchable(),
            Tables\Columns\TextColumn::make('height_cm'),
            Tables\Columns\TextColumn::make('weight_kg'),
            Tables\Columns\TextColumn::make('body_type'),
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
            'index' => Pages\ListAppearances::route('/'),
            'create' => Pages\CreateAppearance::route('/create'),
            'edit' => Pages\EditAppearance::route('/{record}/edit'),
        ];
    }
}
