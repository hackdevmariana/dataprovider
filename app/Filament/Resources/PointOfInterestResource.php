<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointOfInterestResource\Pages;
use App\Filament\Resources\PointOfInterestResource\RelationManagers;
use App\Models\PointOfInterest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class PointOfInterestResource extends Resource
{
    protected static ?string $model = PointOfInterest::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $navigationGroup = 'Ubicaciones y Geografía';


    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255),
            TextInput::make('address')->maxLength(255),
            Select::make('type')
                ->options([
                    'hotel' => 'Hotel',
                    'bar' => 'Bar',
                    'monument' => 'Monumento',
                    'museum' => 'Museo',
                    'park' => 'Parque',
                    'other' => 'Otro',
                ])
                ->required()
                ->searchable(),
            TextInput::make('latitude')->numeric()->step(0.000001),
            TextInput::make('longitude')->numeric()->step(0.000001),
            Select::make('municipality_id')
                ->relationship('municipality', 'name')
                ->searchable()
                ->required(),
            Select::make('source')
                ->options([
                    'osm' => 'OpenStreetMap',
                    'google' => 'Google',
                    'yelp' => 'Yelp',
                    'other' => 'Otro',
                ])
                ->required()
                ->searchable(),
            Textarea::make('description')->nullable()->rows(3),
            KeyValue::make('opening_hours')
                ->label('Horario de apertura')
                ->keyLabel('Día')
                ->valueLabel('Horario')
                ->nullable(),

            Toggle::make('is_cultural_center')->label('Centro cultural'),
            Toggle::make('is_energy_installation')->label('Instalación energética'),
            Toggle::make('is_cooperative_office')->label('Sede de cooperativa'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('type')->label('Tipo'),
            TextColumn::make('municipality.name')->label('Municipio'),
            IconColumn::make('is_cultural_center')->label('Cultural')->boolean(),
            IconColumn::make('is_energy_installation')->label('Energética')->boolean(),
            IconColumn::make('is_cooperative_office')->label('Cooperativa')->boolean(),
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
            'index' => Pages\ListPointOfInterests::route('/'),
            'create' => Pages\CreatePointOfInterest::route('/create'),
            'edit' => Pages\EditPointOfInterest::route('/{record}/edit'),
        ];
    }
}
