<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmissionFactorResource\Pages;
use App\Filament\Resources\EmissionFactorResource\RelationManagers;
use App\Models\EmissionFactor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmissionFactorResource extends Resource
{
    protected static ?string $model = EmissionFactor::class;
    protected static ?string $navigationGroup = 'Economía medioambiental';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('region_id')
                ->relationship('region', 'name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('year')
                ->numeric()->minValue(1900)->maxValue(date('Y')),

            Forms\Components\TextInput::make('source')->maxLength(255)->required(),

            Forms\Components\TextInput::make('co2_kg_per_kwh')
                ->numeric()->step(0.0001)->required(),

            Forms\Components\Textarea::make('emission_context'),

            Forms\Components\TextInput::make('temperature_adjustment_factor')
                ->numeric()->step(0.01),

            Forms\Components\TextInput::make('source_url')
                ->url()->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('region.name')->label('Región'),
            Tables\Columns\TextColumn::make('year')->sortable(),
            Tables\Columns\TextColumn::make('source')->searchable(),
            Tables\Columns\TextColumn::make('co2_kg_per_kwh')->sortable()->numeric(),
        ])
            ->filters([
                // Ejemplo de filtros por año o región
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
            'index' => Pages\ListEmissionFactors::route('/'),
            'create' => Pages\CreateEmissionFactor::route('/create'),
            'edit' => Pages\EditEmissionFactor::route('/{record}/edit'),
        ];
    }
}
