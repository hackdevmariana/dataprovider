<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers;
use App\Models\Country;
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

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Ubicaciones';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('slug')->required(),
            TextInput::make('iso_alpha2')->maxLength(2),
            TextInput::make('iso_alpha3')->maxLength(3),
            TextInput::make('iso_numeric')->numeric(),
            TextInput::make('demonym'),
            TextInput::make('official_language'),
            TextInput::make('currency_code'),
            TextInput::make('phone_code'),
            TextInput::make('latitude'),
            TextInput::make('longitude'),
            TextInput::make('flag_url'),
            TextInput::make('area_km2'),
            TextInput::make('altitude_m'),
            TextInput::make('timezone'),
            TextInput::make('region_group'),
            TextInput::make('gdp_usd'),
            TextInput::make('population'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('iso_alpha2'),
            TextColumn::make('currency_code'),
            TextColumn::make('population')->numeric(),
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
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
