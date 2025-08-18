<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAchievementResource\Pages;
use App\Filament\Resources\UserAchievementResource\RelationManagers;
use App\Models\UserAchievement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserAchievementResource extends Resource
{
    protected static ?string $model = UserAchievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Gamificación';
    protected static ?int $navigationSort = 30;
    protected static ?string $label = 'Logro de Usuario';
    protected static ?string $pluralLabel = 'Logros de Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('achievement_id')
                    ->relationship('achievement', 'name')
                    ->required(),
                Forms\Components\TextInput::make('progress')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('level')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Toggle::make('is_completed')
                    ->required(),
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\TextInput::make('metadata'),
                Forms\Components\TextInput::make('value_achieved')
                    ->numeric(),
                Forms\Components\TextInput::make('points_earned')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_notified')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('achievement.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value_achieved')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_earned')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_notified')
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
            'index' => Pages\ListUserAchievements::route('/'),
            'create' => Pages\CreateUserAchievement::route('/create'),
            'edit' => Pages\EditUserAchievement::route('/{record}/edit'),
        ];
    }
}
