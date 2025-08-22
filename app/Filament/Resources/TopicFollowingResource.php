<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopicFollowingResource\Pages;
use App\Models\TopicFollowing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TopicFollowingResource extends Resource
{
    protected static ?string $model = TopicFollowing::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationGroup = 'Social System';

    protected static ?string $modelLabel = 'Seguimiento de Tema';

    protected static ?string $pluralModelLabel = 'Seguimientos de Temas';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                
                Forms\Components\Select::make('topic_id')
                    ->label('Tema')
                    ->relationship('topic', 'name')
                    ->searchable()
                    ->required(),
                
                Forms\Components\Select::make('follow_type')
                    ->label('Tipo de Seguimiento')
                    ->options([
                        'following' => 'Siguiendo',
                        'watching' => 'Vigilando',
                        'ignoring' => 'Ignorando',
                    ])
                    ->default('following')
                    ->required(),
                
                Forms\Components\Toggle::make('notifications_enabled')
                    ->label('Notificaciones Habilitadas')
                    ->default(true),
                
                Forms\Components\KeyValue::make('notification_preferences')
                    ->label('Preferencias de Notificación')
                    ->keyLabel('Evento')
                    ->valueLabel('Habilitado')
                    ->addActionLabel('Añadir preferencia'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('topic.name')
                    ->label('Tema')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('follow_type')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'following',
                        'warning' => 'watching',
                        'danger' => 'ignoring',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'following' => 'Siguiendo',
                        'watching' => 'Vigilando',
                        'ignoring' => 'Ignorando',
                    }),
                
                Tables\Columns\IconColumn::make('notifications_enabled')
                    ->label('Notificaciones')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('visit_count')
                    ->label('Visitas')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('followed_at')
                    ->label('Desde')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('last_visited_at')
                    ->label('Última Visita')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Nunca'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('follow_type')
                    ->options([
                        'following' => 'Siguiendo',
                        'watching' => 'Vigilando',
                        'ignoring' => 'Ignorando',
                    ]),
                
                Tables\Filters\TernaryFilter::make('notifications_enabled')
                    ->label('Notificaciones'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('followed_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopicFollowings::route('/'),
            'create' => Pages\CreateTopicFollowing::route('/create'),
            'edit' => Pages\EditTopicFollowing::route('/{record}/edit'),
        ];
    }
}