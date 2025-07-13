<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class GroupArtistRelationManager extends RelationManager
{
    protected static string $relationship = 'artists';

    protected static ?string $title = 'Artists';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('artist_id')
                ->relationship('artists', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\DatePicker::make('joined_at')
                ->label('Joined At')
                ->nullable(),

            Forms\Components\DatePicker::make('left_at')
                ->label('Left At')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Artist Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('pivot.joined_at')
                    ->label('Joined At')
                    ->date(),

                Tables\Columns\TextColumn::make('pivot.left_at')
                    ->label('Left At')
                    ->date(),
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
}
