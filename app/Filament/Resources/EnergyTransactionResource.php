<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnergyTransactionResource\Pages;
use App\Filament\Resources\EnergyTransactionResource\RelationManagers;
use App\Models\EnergyTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;


class EnergyTransactionResource extends Resource
{
    protected static ?string $model = EnergyTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Energía y Sostenibilidad';
    protected static ?string $label = 'Transacción Energética';
    protected static ?string $pluralLabel = 'Transacciones Energéticas';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('producer_id')
                ->relationship('producer', 'name')
                ->required(),
            Forms\Components\Select::make('consumer_id')
                ->relationship('consumer', 'name')
                ->required(),
            Forms\Components\Select::make('installation_id')
                ->relationship('installation', 'name')
                ->required(),
            Forms\Components\TextInput::make('amount_kwh')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('price_per_kwh')
                ->numeric()
                ->required(),
            Forms\Components\DateTimePicker::make('transaction_datetime')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('producer.name')->label('Productor')->sortable()->searchable(),
            TextColumn::make('consumer.name')->label('Consumidor')->sortable()->searchable(),
            TextColumn::make('installation.name')->label('Instalación'),
            TextColumn::make('amount_kwh')->label('kWh')->sortable(),
            TextColumn::make('price_per_kwh')->label('€/kWh')->sortable(),
            TextColumn::make('transaction_datetime')->label('Fecha')->since(),
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
            'index' => Pages\ListEnergyTransactions::route('/'),
            'create' => Pages\CreateEnergyTransaction::route('/create'),
            'edit' => Pages\EditEnergyTransaction::route('/{record}/edit'),
        ];
    }
}
