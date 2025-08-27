<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceAlertResource\Pages;
use App\Filament\Resources\PriceAlertResource\RelationManagers;
use App\Models\PriceAlert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceAlertResource extends Resource
{
    protected static ?string $model = PriceAlert::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('energy_type')
                    ->required()
                    ->maxLength(255)
                    ->default('electricity'),
                Forms\Components\TextInput::make('zone')
                    ->required()
                    ->maxLength(255)
                    ->default('peninsula'),
                Forms\Components\TextInput::make('alert_type')
                    ->required()
                    ->maxLength(255)
                    ->default('price_drop'),
                Forms\Components\TextInput::make('threshold_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('condition')
                    ->required()
                    ->maxLength(255)
                    ->default('below'),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\DateTimePicker::make('last_triggered'),
                Forms\Components\TextInput::make('trigger_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('notification_settings'),
                Forms\Components\TextInput::make('frequency')
                    ->required()
                    ->maxLength(255)
                    ->default('once'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('energy_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alert_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('threshold_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_triggered')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trigger_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->searchable(),
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
            'index' => Pages\ListPriceAlerts::route('/'),
            'create' => Pages\CreatePriceAlert::route('/create'),
            'edit' => Pages\EditPriceAlert::route('/{record}/edit'),
        ];
    }
}
