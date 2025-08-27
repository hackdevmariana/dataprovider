<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillSimulatorResource\Pages;
use App\Filament\Resources\BillSimulatorResource\RelationManagers;
use App\Models\BillSimulator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BillSimulatorResource extends Resource
{
    protected static ?string $model = BillSimulator::class;

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
                Forms\Components\TextInput::make('monthly_consumption')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('consumption_unit')
                    ->required()
                    ->maxLength(255)
                    ->default('kWh'),
                Forms\Components\TextInput::make('contract_type')
                    ->required()
                    ->maxLength(255)
                    ->default('fixed'),
                Forms\Components\TextInput::make('power_contracted')
                    ->numeric(),
                Forms\Components\TextInput::make('tariff_details'),
                Forms\Components\TextInput::make('estimated_monthly_bill')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('estimated_annual_bill')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('breakdown'),
                Forms\Components\DateTimePicker::make('simulation_date')
                    ->required(),
                Forms\Components\TextInput::make('assumptions'),
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
                Tables\Columns\TextColumn::make('monthly_consumption')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('consumption_unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('power_contracted')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_monthly_bill')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_annual_bill')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('simulation_date')
                    ->dateTime()
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
            'index' => Pages\ListBillSimulators::route('/'),
            'create' => Pages\CreateBillSimulator::route('/create'),
            'edit' => Pages\EditBillSimulator::route('/{record}/edit'),
        ];
    }
}
