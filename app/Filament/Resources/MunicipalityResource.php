<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MunicipalityResource\Pages;
use App\Filament\Resources\MunicipalityResource\RelationManagers;
use App\Models\Municipality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;


class MunicipalityResource extends Resource
{
    protected static ?string $model = Municipality::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Locations';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255),
            TextInput::make('ine_code')->maxLength(10),
            TextInput::make('postal_code')->maxLength(10),
            TextInput::make('population')->numeric(),
            TextInput::make('mayor_name')->maxLength(255),
            TextInput::make('mayor_salary')->numeric()->nullable(),
            TextInput::make('latitude')->numeric()->step(0.000001),
            TextInput::make('longitude')->numeric()->step(0.000001),
            TextInput::make('area_km2')->numeric()->step(0.01),
            TextInput::make('altitude_m')->numeric()->step(1),
            TextInput::make('timezone')->maxLength(100),
            Textarea::make('tourism_info')->rows(3)->nullable(),
            Toggle::make('is_capital'),

            Select::make('region_id')
                ->relationship('region', 'name')
                ->searchable()
                ->nullable(),

            Select::make('province_id')
                ->relationship('province', 'name')
                ->searchable()
                ->required(),

            Select::make('autonomous_community_id')
                ->relationship('autonomousCommunity', 'name')
                ->searchable()
                ->required(),

            Select::make('country_id')
                ->relationship('country', 'name')
                ->searchable()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('province.name')->label('Provincia')->sortable(),
            TextColumn::make('autonomousCommunity.name')->label('C. Autónoma')->sortable(),
            TextColumn::make('population')->numeric()->sortable(),
            ToggleColumn::make('is_capital')->label('Capital'),
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
            'index' => Pages\ListMunicipalities::route('/'),
            'create' => Pages\CreateMunicipality::route('/create'),
            'edit' => Pages\EditMunicipality::route('/{record}/edit'),
        ];
    }
}
