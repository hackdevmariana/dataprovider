<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProvinceResource\Pages;
use App\Filament\Resources\ProvinceResource\RelationManagers;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Lugares';


    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255),
            TextInput::make('ine_code')->maxLength(10),
            TextInput::make('latitude')->numeric()->step(0.000001),
            TextInput::make('longitude')->numeric()->step(0.000001),
            TextInput::make('area_km2')->numeric()->step(0.01),
            TextInput::make('altitude_m')->numeric()->step(1),
            TextInput::make('timezone')->maxLength(100),

            Select::make('autonomous_community_id')
                ->relationship('autonomousCommunity', 'name')
                ->required()
                ->searchable(),

            Select::make('country_id')
                ->relationship('country', 'name')
                ->required()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('autonomousCommunity.name')->label('C. Autónoma')->sortable(),
            TextColumn::make('country.name')->label('País')->sortable(),
            TextColumn::make('ine_code')->label('Código INE'),
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
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),
        ];
    }
}
