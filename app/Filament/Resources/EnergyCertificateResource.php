<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnergyCertificateResource\Pages;
use App\Filament\Resources\EnergyCertificateResource\RelationManagers;
use App\Models\EnergyCertificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnergyCertificateResource extends Resource
{
    protected static ?string $model = EnergyCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    
    protected static ?string $navigationGroup = 'Energy & Environment';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $modelLabel = 'Certificado Energético';
    
    protected static ?string $pluralModelLabel = 'Certificados Energéticos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('building_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('energy_rating')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('annual_energy_consumption_kwh')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('annual_emissions_kg_co2e')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('zone_climate_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('building_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('energy_rating')
                    ->searchable(),
                Tables\Columns\TextColumn::make('annual_energy_consumption_kwh')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('annual_emissions_kg_co2e')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('zone_climate_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListEnergyCertificates::route('/'),
            'create' => Pages\CreateEnergyCertificate::route('/create'),
            'edit' => Pages\EditEnergyCertificate::route('/{record}/edit'),
        ];
    }
}
