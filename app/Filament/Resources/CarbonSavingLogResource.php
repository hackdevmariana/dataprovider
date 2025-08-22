<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarbonSavingLogResource\Pages;
use App\Filament\Resources\CarbonSavingLogResource\RelationManagers;
use App\Models\CarbonSavingLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarbonSavingLogResource extends Resource
{
    protected static ?string $model = CarbonSavingLog::class;
    protected static ?string $navigationGroup = 'Energy & Environment';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')->searchable()->required(),

            Forms\Components\Select::make('cooperative_id')
                ->relationship('cooperative', 'name')->searchable(),

            Forms\Components\TextInput::make('kw_installed')
                ->numeric()->step(0.01)->required(),

            Forms\Components\TextInput::make('production_kwh')
                ->numeric()->step(0.01),

            Forms\Components\TextInput::make('co2_saved_kg')
                ->numeric()->step(0.01),

            Forms\Components\DatePicker::make('date_range_start')->required(),
            Forms\Components\DatePicker::make('date_range_end'),

            Forms\Components\TextInput::make('estimation_source')->maxLength(255),
            Forms\Components\TextInput::make('carbon_saving_method')->maxLength(255),

            Forms\Components\Toggle::make('created_by_system'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('Usuario')->searchable(),
            Tables\Columns\TextColumn::make('cooperative.name')->label('Cooperativa'),
            Tables\Columns\TextColumn::make('kw_installed')->sortable(),
            Tables\Columns\TextColumn::make('co2_saved_kg')->sortable(),
            Tables\Columns\TextColumn::make('date_range_start')->date(),
            Tables\Columns\TextColumn::make('date_range_end')->date(),
            Tables\Columns\IconColumn::make('created_by_system')->boolean(),
        ])
            ->filters([
                // Puedes agregar filtros por fecha, usuario, cooperativa, etc.
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCarbonSavingLogs::route('/'),
            'create' => Pages\CreateCarbonSavingLog::route('/create'),
            'edit' => Pages\EditCarbonSavingLog::route('/{record}/edit'),
        ];
    }
}
