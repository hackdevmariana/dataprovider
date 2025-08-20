<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarbonCalculationResource\Pages;
use App\Filament\Resources\CarbonCalculationResource\RelationManagers;
use App\Models\CarbonCalculation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarbonCalculationResource extends Resource
{
    protected static ?string $model = CarbonCalculation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            'index' => Pages\ListCarbonCalculations::route('/'),
            'create' => Pages\CreateCarbonCalculation::route('/create'),
            'edit' => Pages\EditCarbonCalculation::route('/{record}/edit'),
        ];
    }
}
