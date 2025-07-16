<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceUnitResource\Pages;
use App\Filament\Resources\PriceUnitResource\RelationManagers;
use App\Models\PriceUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceUnitResource extends Resource
{
    protected static ?string $model = PriceUnit::class;
    protected static ?string $navigationGroup = 'Mercado energético';

    protected static ?string $navigationIcon = 'heroicon-o-currency-euro';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('short_name')->required()->maxLength(50),
            Forms\Components\TextInput::make('unit_code')->nullable()->maxLength(50),
            Forms\Components\TextInput::make('conversion_factor_to_kwh')
                ->numeric()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('short_name')->sortable(),
            Tables\Columns\TextColumn::make('unit_code')->sortable(),
            Tables\Columns\TextColumn::make('conversion_factor_to_kwh')->sortable(),
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
            'index' => Pages\ListPriceUnits::route('/'),
            'create' => Pages\CreatePriceUnit::route('/create'),
            'edit' => Pages\EditPriceUnit::route('/{record}/edit'),
        ];
    }
}
