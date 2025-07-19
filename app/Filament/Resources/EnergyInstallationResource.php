<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnergyInstallationResource\Pages;
use App\Filament\Resources\EnergyInstallationResource\RelationManagers;
use App\Models\EnergyInstallation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class EnergyInstallationResource extends Resource
{
    protected static ?string $model = EnergyInstallation::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Mercado energético';
    protected static ?string $label = 'Instalación Energética';
    protected static ?string $pluralLabel = 'Instalaciones Energéticas';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('type')->required(),
            Forms\Components\TextInput::make('capacity_kw')->numeric()->required(),
            Forms\Components\TextInput::make('location')->required(),
            Forms\Components\Select::make('owner_id')
                ->relationship('owner', 'name')
                ->required(),
            Forms\Components\DatePicker::make('commissioned_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('type'),
            TextColumn::make('capacity_kw')->label('kW')->sortable(),
            TextColumn::make('location')->searchable(),
            TextColumn::make('owner.name')->label('Propietario'),
            TextColumn::make('commissioned_at')->label('Puesta en marcha')->date(),
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
            'index' => Pages\ListEnergyInstallations::route('/'),
            'create' => Pages\CreateEnergyInstallation::route('/create'),
            'edit' => Pages\EditEnergyInstallation::route('/{record}/edit'),
        ];
    }
}
