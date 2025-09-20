<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AchievementResource\Pages;
use App\Filament\Resources\AchievementResource\RelationManagers;
use App\Models\Achievement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Sistema y Administración';
    protected static ?int $navigationSort = 8;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'single' => 'Único',
                        'progressive' => 'Progresivo',
                        'recurring' => 'Recurrente'
                    ])
                    ->default('single'),
                Forms\Components\Select::make('difficulty')
                    ->required()
                    ->options([
                        'bronze' => 'Bronce',
                        'silver' => 'Plata',
                        'gold' => 'Oro',
                        'legendary' => 'Legendario'
                    ])
                    ->default('bronze'),
                Forms\Components\TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(255),
                Forms\Components\TextInput::make('banner_color')
                    ->maxLength(255),
                Forms\Components\Textarea::make('conditions')
                    ->label('Condiciones (JSON)')
                    ->helperText('Formato JSON para las condiciones del logro'),
                Forms\Components\Toggle::make('is_secret')
                    ->label('Es secreto')
                    ->default(false),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'single',
                        'warning' => 'progressive', 
                        'success' => 'recurring'
                    ]),
                Tables\Columns\BadgeColumn::make('difficulty')
                    ->label('Dificultad')
                    ->colors([
                        'secondary' => 'bronze',
                        'warning' => 'silver',
                        'success' => 'gold',
                        'danger' => 'legendary'
                    ]),
                Tables\Columns\TextColumn::make('points')
                    ->label('Puntos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_secret')
                    ->label('Secreto')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
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
            'index' => Pages\ListAchievements::route('/'),
            'create' => Pages\CreateAchievement::route('/create'),
            'edit' => Pages\EditAchievement::route('/{record}/edit'),
        ];
    }
}
