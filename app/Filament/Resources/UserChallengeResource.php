<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserChallengeResource\Pages;
use App\Filament\Resources\UserChallengeResource\RelationManagers;
use App\Models\UserChallenge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserChallengeResource extends Resource
{
    protected static ?string $model = UserChallenge::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Social System';
    protected static ?int $navigationSort = 40;
    protected static ?string $label = 'Participación en Reto';
    protected static ?string $pluralLabel = 'Participaciones en Retos';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('challenge_id')
                    ->relationship('challenge', 'name')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\DateTimePicker::make('joined_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\TextInput::make('progress'),
                Forms\Components\TextInput::make('current_value')
                    ->required()
                    ->numeric()
                    ->default(0.0000),
                Forms\Components\TextInput::make('ranking_position')
                    ->numeric(),
                Forms\Components\TextInput::make('points_earned')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('reward_earned')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('achievements_unlocked'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_team_leader')
                    ->required(),
                Forms\Components\TextInput::make('team_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('challenge.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('joined_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ranking_position')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_earned')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reward_earned')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_team_leader')
                    ->boolean(),
                Tables\Columns\TextColumn::make('team_id')
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
            'index' => Pages\ListUserChallenges::route('/'),
            'create' => Pages\CreateUserChallenge::route('/create'),
            'edit' => Pages\EditUserChallenge::route('/{record}/edit'),
        ];
    }
}
