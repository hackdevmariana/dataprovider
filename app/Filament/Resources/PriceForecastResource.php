<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceForecastResource\Pages;
use App\Filament\Resources\PriceForecastResource\RelationManagers;
use App\Models\PriceForecast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceForecastResource extends Resource
{
    protected static ?string $model = PriceForecast::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('energy_type')
                    ->required()
                    ->maxLength(255)
                    ->default('electricity'),
                Forms\Components\TextInput::make('zone')
                    ->required()
                    ->maxLength(255)
                    ->default('peninsula'),
                Forms\Components\DateTimePicker::make('forecast_time')
                    ->required(),
                Forms\Components\DateTimePicker::make('target_time')
                    ->required(),
                Forms\Components\TextInput::make('predicted_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('confidence_level')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('forecast_model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('factors'),
                Forms\Components\TextInput::make('min_price')
                    ->numeric(),
                Forms\Components\TextInput::make('max_price')
                    ->numeric(),
                Forms\Components\TextInput::make('accuracy_score')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('energy_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('forecast_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('predicted_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('confidence_level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('forecast_model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('min_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accuracy_score')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPriceForecasts::route('/'),
            'create' => Pages\CreatePriceForecast::route('/create'),
            'edit' => Pages\EditPriceForecast::route('/{record}/edit'),
        ];
    }
}
