<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserFollowResource\Pages;
use App\Filament\Resources\UserFollowResource\RelationManagers;
use App\Models\UserFollow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\KeyValue;

class UserFollowResource extends Resource
{
    protected static ?string $navigationGroup = 'Usuarios y Social';
    protected static ?string $model = UserFollow::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Seguimientos de Usuarios';
    protected static ?string $modelLabel = 'Seguimiento';
    protected static ?string $pluralModelLabel = 'Seguimientos';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Select::make('follower_id')
                            ->label('Usuario que Sigue')
                            ->relationship('follower', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Select::make('following_id')
                            ->label('Usuario Seguido')
                            ->relationship('following', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Select::make('follow_type')
                            ->label('Tipo de Seguimiento')
                            ->options([
                                'personal' => 'Personal',
                                'professional' => 'Profesional',
                                'project' => 'Proyecto',
                                'interest' => 'Interés',
                                'mentor' => 'Mentor',
                                'collaborator' => 'Colaborador',
                            ])
                            ->default('personal')
                            ->required(),
                        
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'paused' => 'Pausado',
                                'muted' => 'Silenciado',
                                'blocked' => 'Bloqueado',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Configuración de Notificaciones')
                    ->schema([
                        Select::make('notification_frequency')
                            ->label('Frecuencia de Notificaciones')
                            ->options([
                                'instant' => 'Instantáneas',
                                'daily_digest' => 'Resumen Diario',
                                'weekly_digest' => 'Resumen Semanal',
                                'never' => 'Nunca',
                            ])
                            ->default('daily_digest'),
                        
                        Toggle::make('notify_new_activity')
                            ->label('Notificar Nueva Actividad')
                            ->default(true),
                        
                        Toggle::make('notify_achievements')
                            ->label('Notificar Logros')
                            ->default(true),
                        
                        Toggle::make('notify_projects')
                            ->label('Notificar Proyectos')
                            ->default(true),
                        
                        Toggle::make('notify_investments')
                            ->label('Notificar Inversiones')
                            ->default(false),
                        
                        Toggle::make('notify_milestones')
                            ->label('Notificar Hitos')
                            ->default(true),
                        
                        Toggle::make('notify_content')
                            ->label('Notificar Contenido')
                            ->default(true),
                    ])
                    ->columns(3),

                Section::make('Configuración del Feed')
                    ->schema([
                        Toggle::make('show_in_main_feed')
                            ->label('Mostrar en Feed Principal')
                            ->default(true),
                        
                        Toggle::make('prioritize_in_feed')
                            ->label('Priorizar en Feed')
                            ->default(false),
                        
                        TextInput::make('feed_weight')
                            ->label('Peso en Feed')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(200)
                            ->default(50),
                        
                        TextInput::make('minimum_relevance_score')
                            ->label('Score Mínimo de Relevancia')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(30),
                    ])
                    ->columns(2),

                Section::make('Contexto y Motivación')
                    ->schema([
                        Textarea::make('follow_reason')
                            ->label('Razón del Seguimiento')
                            ->rows(2)
                            ->maxLength(500),
                        
                        TagsInput::make('interests')
                            ->label('Intereses')
                            ->placeholder('Añadir interés'),
                        
                        TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->placeholder('Añadir etiqueta'),
                    ])
                    ->columns(1),

                Section::make('Configuración de Privacidad')
                    ->schema([
                        Toggle::make('is_public')
                            ->label('Público')
                            ->default(true),
                        
                        Toggle::make('show_to_followed')
                            ->label('Mostrar al Seguido')
                            ->default(true),
                        
                        Toggle::make('allow_followed_to_see_activity')
                            ->label('Permitir Ver Actividad')
                            ->default(true),
                    ])
                    ->columns(3),

                Section::make('Métricas y Engagement')
                    ->schema([
                        TextInput::make('engagement_score')
                            ->label('Score de Engagement')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0),
                        
                        TextInput::make('interactions_count')
                            ->label('Contador de Interacciones')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        
                        TextInput::make('content_views')
                            ->label('Visualizaciones de Contenido')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        
                        TextInput::make('days_following')
                            ->label('Días Siguiendo')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(2),

                Section::make('Fechas Importantes')
                    ->schema([
                        DateTimePicker::make('followed_at')
                            ->label('Fecha de Seguimiento')
                            ->default(now())
                            ->required(),
                        
                        DateTimePicker::make('last_interaction_at')
                            ->label('Última Interacción')
                            ->nullable(),
                        
                        DateTimePicker::make('last_seen_activity_at')
                            ->label('Última Actividad Vista')
                            ->nullable(),
                        
                        DateTimePicker::make('mutual_since')
                            ->label('Mutuo Desde')
                            ->nullable(),
                        
                        DateTimePicker::make('status_changed_at')
                            ->label('Estado Cambiado')
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Filtros Avanzados')
                    ->schema([
                        KeyValue::make('content_filters')
                            ->label('Filtros de Contenido')
                            ->keyLabel('Tipo')
                            ->valueLabel('Acción')
                            ->addActionLabel('Añadir Filtro'),
                        
                        KeyValue::make('activity_filters')
                            ->label('Filtros de Actividad')
                            ->keyLabel('Tipo')
                            ->valueLabel('Acción')
                            ->addActionLabel('Añadir Filtro'),
                        
                        KeyValue::make('algorithm_preferences')
                            ->label('Preferencias del Algoritmo')
                            ->keyLabel('Preferencia')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir Preferencia'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Estado y Cambios')
                    ->schema([
                        Textarea::make('status_reason')
                            ->label('Razón del Cambio de Estado')
                            ->rows(2)
                            ->maxLength(500),
                        
                        TextInput::make('relevance_decay_rate')
                            ->label('Tasa de Decaimiento de Relevancia')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(1)
                            ->default(0.1),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('follower.name')
                    ->label('Usuario que Sigue')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('following.name')
                    ->label('Usuario Seguido')
                    ->searchable()
                    ->sortable(),
                
                BadgeColumn::make('follow_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'personal',
                        'success' => 'professional',
                        'warning' => 'project',
                        'info' => 'interest',
                        'secondary' => 'mentor',
                        'danger' => 'collaborator',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'personal' => 'Personal',
                        'professional' => 'Profesional',
                        'project' => 'Proyecto',
                        'interest' => 'Interés',
                        'mentor' => 'Mentor',
                        'collaborator' => 'Colaborador',
                        default => $state,
                    }),
                
                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'paused',
                        'info' => 'muted',
                        'danger' => 'blocked',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'paused' => 'Pausado',
                        'muted' => 'Silenciado',
                        'blocked' => 'Bloqueado',
                        default => $state,
                    }),
                
                TextColumn::make('engagement_score')
                    ->label('Engagement')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state >= 40 => 'info',
                        default => 'danger',
                    }),
                
                TextColumn::make('interactions_count')
                    ->label('Interacciones')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('days_following')
                    ->label('Días Siguiendo')
                    ->numeric()
                    ->sortable(),
                
                IconColumn::make('is_mutual')
                    ->label('Mutuo')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-arrow-right')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                IconColumn::make('show_in_main_feed')
                    ->label('En Feed')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye')
                    ->trueColor('info')
                    ->falseColor('gray'),
                
                IconColumn::make('prioritize_in_feed')
                    ->label('Prioritario')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                TextColumn::make('notification_frequency')
                    ->label('Notificaciones')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'instant' => 'success',
                        'daily_digest' => 'warning',
                        'weekly_digest' => 'info',
                        'never' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'instant' => 'Instantáneas',
                        'daily_digest' => 'Diario',
                        'weekly_digest' => 'Semanal',
                        'never' => 'Nunca',
                        default => $state,
                    }),
                
                TextColumn::make('followed_at')
                    ->label('Fecha Seguimiento')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('last_interaction_at')
                    ->label('Última Interacción')
                    ->dateTime()
                    ->placeholder('Sin interacciones')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('mutual_since')
                    ->label('Mutuo Desde')
                    ->dateTime()
                    ->placeholder('No mutuo')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'paused' => 'Pausado',
                        'muted' => 'Silenciado',
                        'blocked' => 'Bloqueado',
                    ]),
                
                Tables\Filters\SelectFilter::make('follow_type')
                    ->label('Tipo de Seguimiento')
                    ->options([
                        'personal' => 'Personal',
                        'professional' => 'Profesional',
                        'project' => 'Proyecto',
                        'interest' => 'Interés',
                        'mentor' => 'Mentor',
                        'collaborator' => 'Colaborador',
                    ]),
                
                Tables\Filters\SelectFilter::make('notification_frequency')
                    ->label('Frecuencia de Notificaciones')
                    ->options([
                        'instant' => 'Instantáneas',
                        'daily_digest' => 'Resumen Diario',
                        'weekly_digest' => 'Resumen Semanal',
                        'never' => 'Nunca',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_mutual')
                    ->label('Mutuo'),
                
                Tables\Filters\TernaryFilter::make('show_in_main_feed')
                    ->label('En Feed Principal'),
                
                Tables\Filters\TernaryFilter::make('prioritize_in_feed')
                    ->label('Prioritario en Feed'),
                
                Tables\Filters\Filter::make('engagement_range')
                    ->form([
                        TextInput::make('min_engagement')
                            ->label('Engagement Mínimo')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        TextInput::make('max_engagement')
                            ->label('Engagement Máximo')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_engagement'],
                                fn (Builder $query, $min): Builder => $query->where('engagement_score', '>=', $min),
                            )
                            ->when(
                                $data['max_engagement'],
                                fn (Builder $query, $max): Builder => $query->where('engagement_score', '<=', $max),
                            );
                    }),
                
                Tables\Filters\Filter::make('recent_follows')
                    ->label('Seguimientos Recientes')
                    ->query(fn (Builder $query): Builder => $query->where('followed_at', '>=', now()->subDays(30))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('followed_at', 'desc');
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
            'index' => Pages\ListUserFollows::route('/'),
            'create' => Pages\CreateUserFollow::route('/create'),
            'edit' => Pages\EditUserFollow::route('/{record}/edit'),
        ];
    }
}
