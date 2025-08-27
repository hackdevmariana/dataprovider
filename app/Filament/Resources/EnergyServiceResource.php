<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnergyServiceResource\Pages;
use App\Filament\Resources\EnergyServiceResource\RelationManagers;
use App\Models\EnergyService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnergyServiceResource extends Resource
{
    protected static ?string $model = EnergyService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                Forms\Components\TextInput::make('service_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('service_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('energy_source')
                    ->maxLength(255),
                Forms\Components\TextInput::make('features'),
                Forms\Components\TextInput::make('requirements'),
                Forms\Components\TextInput::make('base_price')
                    ->numeric(),
                Forms\Components\TextInput::make('pricing_model')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pricing_details'),
                Forms\Components\TextInput::make('contract_duration')
                    ->maxLength(255),
                Forms\Components\TextInput::make('terms_conditions'),
                Forms\Components\Toggle::make('is_available')
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->required(),
                Forms\Components\TextInput::make('popularity_score')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('energy_source')
                    ->searchable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pricing_model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_duration')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('popularity_score')
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
            'index' => Pages\ListEnergyServices::route('/'),
            'create' => Pages\CreateEnergyService::route('/create'),
            'edit' => Pages\EditEnergyService::route('/{record}/edit'),
        ];
    }
}
