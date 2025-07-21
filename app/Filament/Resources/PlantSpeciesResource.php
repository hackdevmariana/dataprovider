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
    protected static ?string $navigationGroup = 'Medio ambiente';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListPlantSpecies::route('/'),
            'create' => Pages\CreatePlantSpecies::route('/create'),
            'edit' => Pages\EditPlantSpecies::route('/{record}/edit'),
        ];
    }
}
