<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiturgicalCalendarResource\Pages;
use App\Filament\Resources\LiturgicalCalendarResource\RelationManagers;
use App\Models\LiturgicalCalendar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LiturgicalCalendarResource extends Resource
{
    protected static ?string $model = LiturgicalCalendar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('liturgical_season')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('feast_day')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('saint_id')
                    ->relationship('saint', 'name'),
                Forms\Components\TextInput::make('celebration_level')
                    ->required()
                    ->maxLength(255)
                    ->default('memorial'),
                Forms\Components\TextInput::make('readings'),
                Forms\Components\TextInput::make('prayers'),
                Forms\Components\TextInput::make('traditions'),
                Forms\Components\TextInput::make('color')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('special_observances'),
                Forms\Components\Toggle::make('is_holiday')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('liturgical_season')
                    ->searchable(),
                Tables\Columns\TextColumn::make('feast_day')
                    ->searchable(),
                Tables\Columns\TextColumn::make('saint.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('celebration_level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_holiday')
                    ->boolean(),
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
            'index' => Pages\ListLiturgicalCalendars::route('/'),
            'create' => Pages\CreateLiturgicalCalendar::route('/create'),
            'edit' => Pages\EditLiturgicalCalendar::route('/{record}/edit'),
        ];
    }
}
