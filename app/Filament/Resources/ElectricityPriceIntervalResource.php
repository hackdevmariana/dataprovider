<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ElectricityPriceIntervalResource\Pages;
use App\Filament\Resources\ElectricityPriceIntervalResource\RelationManagers;
use App\Models\ElectricityPriceInterval;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TimeColumn;

class ElectricityPriceIntervalResource extends Resource
{
    protected static ?string $model = ElectricityPriceInterval::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Energy & Environment';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('electricity_price_id')
                ->relationship('electricityPrice', 'date') // asumiendo que ElectricityPrice tiene un campo 'date'
                ->required(),

            TextInput::make('interval_index')
                ->numeric()
                ->minValue(0)
                ->required(),

            TimePicker::make('start_time')->required(),
            TimePicker::make('end_time')->required(),

            TextInput::make('price_eur_mwh')
                ->numeric()
                ->step(0.0001)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('electricityPrice.date')->label('Date')->sortable()->searchable(),
                TextColumn::make('interval_index')->sortable(),
                TextColumn::make('start_time')->time('H:i'),
                TextColumn::make('end_time')->time('H:i'),
                TextColumn::make('price_eur_mwh')->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListElectricityPriceIntervals::route('/'),
            'create' => Pages\CreateElectricityPriceInterval::route('/create'),
            'edit' => Pages\EditElectricityPriceInterval::route('/{record}/edit'),
        ];
    }
}
