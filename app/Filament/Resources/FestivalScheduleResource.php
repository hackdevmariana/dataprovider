<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FestivalScheduleResource\Pages;
use App\Filament\Resources\FestivalScheduleResource\RelationManagers;
use App\Models\FestivalSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FestivalScheduleResource extends Resource
{
    protected static ?string $model = FestivalSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('festival_id')
                    ->relationship('festival', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('opening_time')
                    ->required(),
                Forms\Components\TextInput::make('closing_time')
                    ->required(),
                Forms\Components\TextInput::make('main_events')
                    ->required(),
                Forms\Components\TextInput::make('side_activities'),
                Forms\Components\Textarea::make('special_notes')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('weather_forecast')
                    ->maxLength(255),
                Forms\Components\TextInput::make('expected_attendance')
                    ->numeric(),
                Forms\Components\TextInput::make('transportation_info'),
                Forms\Components\TextInput::make('parking_info'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('festival.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('opening_time'),
                Tables\Columns\TextColumn::make('closing_time'),
                Tables\Columns\TextColumn::make('weather_forecast')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expected_attendance')
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
            'index' => Pages\ListFestivalSchedules::route('/'),
            'create' => Pages\CreateFestivalSchedule::route('/create'),
            'edit' => Pages\EditFestivalSchedule::route('/{record}/edit'),
        ];
    }
}
