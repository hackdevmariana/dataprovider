<?php

namespace App\Filament\Resources\CalendarHolidayResource\RelationManagers;

use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Models\Municipality;
use App\Models\Province;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;


class CalendarHolidayLocationRelationManager extends RelationManager
{
    protected static string $relationship = 'locations';

    protected static ?string $title = 'Holiday Locations';

    public function form(Form $form): Form

    {
        return $form->schema([
            Forms\Components\Select::make('municipality_id')
                ->label('Municipality')
                ->relationship('municipality', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            Forms\Components\Select::make('province_id')
                ->label('Province')
                ->relationship('province', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            Forms\Components\Select::make('autonomous_community_id')
                ->label('Autonomous Community')
                ->relationship('autonomousCommunity', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            Forms\Components\Select::make('country_id')
                ->label('Country')
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
        ]);
    }



    public function table(Table $table): Table


    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('municipality.name')->label('Municipality'),
                Tables\Columns\TextColumn::make('province.name')->label('Province'),
                Tables\Columns\TextColumn::make('autonomousCommunity.name')->label('Autonomous Community'),
                Tables\Columns\TextColumn::make('country.name')->label('Country'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getRecordTitle(?object $record): string
    {
        return $record->country->name
            ?? $record->autonomousCommunity->name
            ?? $record->province->name
            ?? $record->municipality->name
            ?? 'Location';
    }
}
