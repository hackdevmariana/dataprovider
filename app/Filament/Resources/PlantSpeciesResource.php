<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlantSpeciesResource\Pages;
use App\Filament\Resources\PlantSpeciesResource\RelationManagers;
use App\Models\PlantSpecies;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlantSpeciesResource extends Resource
{
    protected static ?string $model = PlantSpecies::class;

    protected static ?string $navigationIcon = 'phosphor-plant-bold';
    protected static ?string $navigationGroup = 'Economía medioambiental';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('common_name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('scientific_name')
                    ->maxLength(255),

                Forms\Components\TextInput::make('co2_absorption_kg_per_year')
                    ->label('CO₂ Absorción kg/año')
                    ->numeric()
                    ->suffix('kg')
                    ->step(0.01)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('common_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('scientific_name')->sortable(),
                Tables\Columns\TextColumn::make('co2_absorption_kg_per_year')
                    ->label('CO₂ (kg/año)')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' kg'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')->label('Creado'),
            ])
            ->defaultSort('common_name');
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
            'index' => Pages\ListPlantSpecies::route('/'),
            'create' => Pages\CreatePlantSpecies::route('/create'),
            'edit' => Pages\EditPlantSpecies::route('/{record}/edit'),
        ];
    }
}
