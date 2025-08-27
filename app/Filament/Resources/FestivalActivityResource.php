<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FestivalActivityResource\Pages;
use App\Filament\Resources\FestivalActivityResource\RelationManagers;
use App\Models\FestivalActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FestivalActivityResource extends Resource
{
    protected static ?string $model = FestivalActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('festival_id')
                    ->relationship('festival', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255)
                    ->default('workshop'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('start_time')
                    ->required(),
                Forms\Components\TextInput::make('duration_minutes')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('organizer')
                    ->maxLength(255),
                Forms\Components\TextInput::make('max_participants')
                    ->numeric(),
                Forms\Components\TextInput::make('age_restriction')
                    ->maxLength(255),
                Forms\Components\TextInput::make('requirements'),
                Forms\Components\TextInput::make('materials_provided'),
                Forms\Components\Toggle::make('requires_registration')
                    ->required(),
                Forms\Components\TextInput::make('participation_fee')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('festival.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organizer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_participants')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age_restriction')
                    ->searchable(),
                Tables\Columns\IconColumn::make('requires_registration')
                    ->boolean(),
                Tables\Columns\TextColumn::make('participation_fee')
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
            'index' => Pages\ListFestivalActivities::route('/'),
            'create' => Pages\CreateFestivalActivity::route('/create'),
            'edit' => Pages\EditFestivalActivity::route('/{record}/edit'),
        ];
    }
}
