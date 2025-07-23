<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ZoneClimateResource\Pages;
use App\Filament\Resources\ZoneClimateResource\RelationManagers;
use App\Models\ZoneClimate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ZoneClimateResource extends Resource
{
    protected static ?string $model = ZoneClimate::class;
    protected static ?string $navigationGroup = 'Lugares';

    protected static ?string $navigationIcon = 'heroicon-o-sun';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('climate_zone')
                ->required()->maxLength(10),

            Forms\Components\Textarea::make('description'),

            Forms\Components\TextInput::make('average_heating_demand')
                ->numeric()->step(0.01)->label('Demanda Prom. Calefacción'),

            Forms\Components\TextInput::make('average_cooling_demand')
                ->numeric()->step(0.01)->label('Demanda Prom. Refrigeración'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('climate_zone')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('description')->limit(50),
            Tables\Columns\TextColumn::make('average_heating_demand')->sortable(),
            Tables\Columns\TextColumn::make('average_cooling_demand')->sortable(),
        ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListZoneClimates::route('/'),
            'create' => Pages\CreateZoneClimate::route('/create'),
            'edit' => Pages\EditZoneClimate::route('/{record}/edit'),
        ];
    }
}
