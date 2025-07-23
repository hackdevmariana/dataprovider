<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Filament\Resources\RegionResource\RelationManagers;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;


    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Lugares';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255),
            TextInput::make('latitude')->numeric()->step(0.000001),
            TextInput::make('longitude')->numeric()->step(0.000001),
            TextInput::make('area_km2')->numeric()->step(0.01),
            TextInput::make('altitude_m')->numeric()->step(1),
            TextInput::make('timezone')->maxLength(100),

            Select::make('province_id')
                ->relationship('province', 'name')
                ->searchable()
                ->required(),

            Select::make('autonomous_community_id')
                ->relationship('autonomousCommunity', 'name')
                ->searchable()
                ->required(),

            Select::make('country_id')
                ->relationship('country', 'name')
                ->searchable()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('province.name')->label('Provincia'),
            TextColumn::make('autonomousCommunity.name')->label('Comunidad'),
            TextColumn::make('country.name')->label('País'),
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
