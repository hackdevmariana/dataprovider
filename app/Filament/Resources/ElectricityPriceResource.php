<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ElectricityPriceResource\Pages;
use App\Filament\Resources\ElectricityPriceResource\RelationManagers;
use App\Models\ElectricityPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ElectricityPriceResource extends Resource
{
    protected static ?string $model = ElectricityPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = 'Mercado energético';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')->required(),
                Forms\Components\TextInput::make('hour')->nullable()->label('Hour (0-23)'),
                Forms\Components\Select::make('type')
                    ->options([
                        'pvpc' => 'PVPC',
                        'spot' => 'Spot',
                    ])->required(),
                Forms\Components\TextInput::make('price_eur_mwh')->numeric()->required()->label('Price (€/MWh)'),
                Forms\Components\TextInput::make('price_min')->numeric()->nullable(),
                Forms\Components\TextInput::make('price_max')->numeric()->nullable(),
                Forms\Components\TextInput::make('price_avg')->numeric()->nullable(),
                Forms\Components\Toggle::make('forecast_for_tomorrow')->default(false),
                Forms\Components\Select::make('price_unit_id')
                    ->relationship('priceUnit', 'short_name')
                    ->nullable(),
                Forms\Components\TextInput::make('source')->nullable(),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->sortable(),
                Tables\Columns\TextColumn::make('hour')->label('Hour')->sortable(),
                Tables\Columns\TextColumn::make('type')->sortable(),
                Tables\Columns\TextColumn::make('price_eur_mwh')->label('€/MWh')->sortable(),
                Tables\Columns\BooleanColumn::make('forecast_for_tomorrow')->label('Forecast?'),
                Tables\Columns\TextColumn::make('priceUnit.short_name')->label('Unit'),
                Tables\Columns\TextColumn::make('source')->limit(30),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListElectricityPrices::route('/'),
            'create' => Pages\CreateElectricityPrice::route('/create'),
            'edit' => Pages\EditElectricityPrice::route('/{record}/edit'),
        ];
    }
}
