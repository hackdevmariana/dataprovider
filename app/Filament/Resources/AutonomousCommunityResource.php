<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutonomousCommunityResource\Pages;
use App\Filament\Resources\AutonomousCommunityResource\RelationManagers;
use App\Models\AutonomousCommunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;


class AutonomousCommunityResource extends Resource
{
    protected static ?string $model = AutonomousCommunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Lugares';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255),
            TextInput::make('code')->maxLength(50),
            TextInput::make('latitude')->numeric()->step(0.000001),
            TextInput::make('longitude')->numeric()->step(0.000001),
            TextInput::make('area_km2')->numeric()->step(0.01),
            TextInput::make('altitude_m')->numeric()->step(1),
            TextInput::make('timezone')->maxLength(100),

            Select::make('country_id')
                ->relationship('country', 'name')
                ->required()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('country.name')->label('País')->sortable(),
            TextColumn::make('code')->label('Código'),
        ])->defaultSort('name');
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
            'index' => Pages\ListAutonomousCommunities::route('/'),
            'create' => Pages\CreateAutonomousCommunity::route('/create'),
            'edit' => Pages\EditAutonomousCommunity::route('/{record}/edit'),
        ];
    }
}
