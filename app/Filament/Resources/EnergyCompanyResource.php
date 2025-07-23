<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnergyCompanyResource\Pages;
use App\Filament\Resources\EnergyCompanyResource\RelationManagers;
use App\Models\EnergyCompany;
use App\Models\Image;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnergyCompanyResource extends Resource
{
    protected static ?string $model = EnergyCompany::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Sociedades';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('slug')->required()->maxLength(255),
                Forms\Components\Select::make('company_type')
                    ->options([
                        'comercializadora' => 'Comercializadora',
                        'distribuidora' => 'Distribuidora',
                        'mixta' => 'Mixta',
                    ])->required(),
                Forms\Components\TextInput::make('website')->url()->nullable(),
                Forms\Components\TextInput::make('phone_customer')->tel()->nullable(),
                Forms\Components\TextInput::make('phone_commercial')->tel()->nullable(),
                Forms\Components\TextInput::make('email_customer')->email()->nullable(),
                Forms\Components\TextInput::make('email_commercial')->email()->nullable(),
                Forms\Components\TextInput::make('highlighted_offer')->nullable(),
                Forms\Components\TextInput::make('cnmc_id')->nullable(),
                Forms\Components\TextInput::make('logo_url')->url()->nullable(),
                Forms\Components\TextInput::make('address')->nullable(),
                Forms\Components\TextInput::make('latitude')->numeric()->step(0.000001)->nullable(),
                Forms\Components\TextInput::make('longitude')->numeric()->step(0.000001)->nullable(),
                Forms\Components\Select::make('coverage_scope')
                    ->options([
                        'local' => 'Local',
                        'regional' => 'Regional',
                        'nacional' => 'Nacional',
                    ])->nullable(),
                Forms\Components\Select::make('municipality_id')
                    ->relationship('municipality', 'name')
                    ->nullable(),

                
                Forms\Components\Select::make('image_id')
                    ->label('Image')
                    ->options(Image::all()->pluck('url', 'id'))
                    ->searchable()
                    ->nullable(),
                
/*
                Forms\Components\Select::make('data_source_id')
                    ->relationship('dataSource', 'name')
                    ->nullable(),

                    */
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('company_type')->sortable(),
                Tables\Columns\TextColumn::make('website')
                    ->url(fn($record) => $record->website, true)
                    ->limit(30),

                Tables\Columns\TextColumn::make('coverage_scope')->label('Scope')->sortable(),

                Tables\Columns\TextColumn::make('phone_customer')->label('Phone')->limit(20),
                Tables\Columns\TextColumn::make('email_customer')->label('Email')->limit(30),
                Tables\Columns\TextColumn::make('municipality.name')->label('Municipality'),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListEnergyCompanies::route('/'),
            'create' => Pages\CreateEnergyCompany::route('/create'),
            'edit' => Pages\EditEnergyCompany::route('/{record}/edit'),
        ];
    }
}
