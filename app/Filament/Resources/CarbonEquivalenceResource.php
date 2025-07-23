<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarbonEquivalenceResource\Pages;
use App\Filament\Resources\CarbonEquivalenceResource\RelationManagers;
use App\Models\CarbonEquivalence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarbonEquivalenceResource extends Resource
{
    protected static ?string $model = CarbonEquivalence::class;
    protected static ?string $navigationGroup = 'Economía medioambiental';

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
            Forms\Components\Textarea::make('description'),
            Forms\Components\TextInput::make('co2_kg_equivalent')->required()->numeric(),
            Forms\Components\TextInput::make('category')->maxLength(255),
            Forms\Components\TextInput::make('efficiency_ratio')->numeric()->step(0.01),
            Forms\Components\TextInput::make('loss_factor')->numeric()->step(0.01),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('slug')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('co2_kg_equivalent')->sortable(),
            Tables\Columns\TextColumn::make('category')->sortable()->searchable(),
        ])
            ->filters([
                // Puedes agregar filtros si lo necesitas
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
            'index' => Pages\ListCarbonEquivalences::route('/'),
            'create' => Pages\CreateCarbonEquivalence::route('/create'),
            'edit' => Pages\EditCarbonEquivalence::route('/{record}/edit'),
        ];
    }
}
