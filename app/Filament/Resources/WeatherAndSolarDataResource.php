<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeatherAndSolarDataResource\Pages;
use App\Filament\Resources\WeatherAndSolarDataResource\RelationManagers;
use App\Models\WeatherAndSolarData;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class WeatherAndSolarDataResource extends Resource
{
    protected static ?string $model = WeatherAndSolarData::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';
    protected static ?string $navigationGroup = 'Energy & Environment';
    protected static ?string $label = 'Dato Meteorológico y Solar';
    protected static ?string $pluralLabel = 'Datos Meteorológicos y Solares';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DateTimePicker::make('datetime')->required(),
            Forms\Components\TextInput::make('location')->required(),
            Forms\Components\TextInput::make('temperature')->numeric(),
            Forms\Components\TextInput::make('humidity')->numeric(),
            Forms\Components\TextInput::make('cloud_coverage')->numeric(),
            Forms\Components\TextInput::make('solar_irradiance')->numeric(),
            Forms\Components\TextInput::make('wind_speed')->numeric(),
            Forms\Components\TextInput::make('precipitation')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('datetime')->sortable(),
            TextColumn::make('location')->sortable()->searchable(),
            TextColumn::make('temperature')->label('Temp (°C)'),
            TextColumn::make('solar_irradiance')->label('Irradiancia (W/m²)'),
            TextColumn::make('humidity')->label('Humedad (%)'),
            TextColumn::make('cloud_coverage')->label('Nubosidad (%)'),
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
            'index' => Pages\ListWeatherAndSolarData::route('/'),
            'create' => Pages\CreateWeatherAndSolarData::route('/create'),
            'edit' => Pages\EditWeatherAndSolarData::route('/{record}/edit'),
        ];
    }
}
