<?php

namespace App\Filament\Resources\FestivalResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Table;

class EventRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $title = 'Festival Events';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\DateTimePicker::make('start_datetime')->required(),
            Forms\Components\DateTimePicker::make('end_datetime')->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('start_datetime')->dateTime(),
                Tables\Columns\TextColumn::make('venue.name')->label('Venue'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
