<?php

namespace App\Filament\Resources\ArtistResource\RelationManagers;

use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class ArtistGroupRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $title = 'Groups';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('group_id')
                ->relationship('groups', 'name')
                ->label('Group')
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
                    ->label('Group Name')
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
