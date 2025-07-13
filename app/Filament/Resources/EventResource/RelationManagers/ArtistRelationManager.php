<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class ArtistRelationManager extends RelationManager
{
    protected static string $relationship = 'artists';

    protected static ?string $title = 'Participating Artists';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('artist_id')
                ->relationship('artists', 'name') // importante si usas pivot editing
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('role')->label('Role (main act, guest...)')->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Artist')->searchable(),
                Tables\Columns\TextColumn::make('pivot.role')->label('Role'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
