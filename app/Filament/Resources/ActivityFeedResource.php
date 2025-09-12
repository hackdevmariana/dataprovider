<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityFeedResource\Pages;
use App\Filament\Resources\ActivityFeedResource\RelationManagers;
use App\Models\ActivityFeed;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityFeedResource extends Resource
{
    protected static ?string $navigationGroup = 'Social System';
    protected static ?string $model = ActivityFeed::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Actividad';

    protected static ?string $pluralModelLabel = 'Actividades';

    protected static ?int $navigationSort = 9;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Actividad')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('activity_type')
                            ->label('Tipo de Actividad')
                            ->options([
                                'energy_saved' => 'Ahorro Energético',
                                'solar_generated' => 'Energía Solar Generada',
                                'achievement_unlocked' => 'Logro Desbloqueado',
                                'project_funded' => 'Proyecto Financiado',
                                'installation_completed' => 'Instalación Completada',
                                'cooperative_joined' => 'Se Unió a Cooperativa',
                                'roof_published' => 'Publicó Techo en Marketplace',
                                'investment_made' => 'Realizó Inversión',
                                'production_right_sold' => 'Vendió Derecho de Producción',
                                'challenge_completed' => 'Completó Desafío',
                                'milestone_reached' => 'Alcanzó Hito',
                                'content_published' => 'Publicó Contenido',
                                'expert_verified' => 'Verificado como Experto',
                                'review_published' => 'Publicó Review',
                                'topic_created' => 'Creó Tema de Discusión',
                                'community_contribution' => 'Contribución Comunitaria',
                                'carbon_milestone' => 'Hito de Reducción CO2',
                                'efficiency_improvement' => 'Mejora de Eficiencia',
                                'grid_contribution' => 'Contribución a la Red',
                                'sustainability_goal' => 'Meta de Sostenibilidad',
                                'other' => 'Otra Actividad',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('related_type')
                            ->label('Tipo de Objeto Relacionado')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('related_id')
                            ->label('ID del Objeto Relacionado')
                            ->required()
                            ->numeric(),
                    ])->columns(2),

                Forms\Components\Section::make('Contenido y Descripción')
                    ->schema([
                        Forms\Components\KeyValue::make('activity_data')
                            ->label('Datos de la Actividad')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir dato'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->maxLength(1000),
                        
                        Forms\Components\Textarea::make('summary')
                            ->label('Resumen')
                            ->rows(2)
                            ->maxLength(500),
                    ])->columns(2),

                Forms\Components\Section::make('Métricas')
                    ->schema([
                        Forms\Components\TextInput::make('energy_amount_kwh')
                            ->label('Energía (kWh)')
                            ->numeric()
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('cost_savings_eur')
                            ->label('Ahorro (€)')
                            ->numeric()
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('co2_savings_kg')
                            ->label('Ahorro CO2 (kg)')
                            ->numeric()
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('investment_amount_eur')
                            ->label('Inversión (€)')
                            ->numeric()
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('community_impact_score')
                            ->label('Puntuación Impacto Comunitario')
                            ->numeric()
                            ->step(1)
                            ->minValue(0)
                            ->maxValue(100),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('visibility')
                            ->label('Visibilidad')
                            ->options([
                                'public' => 'Público',
                                'cooperative' => 'Cooperativa',
                                'followers' => 'Seguidores',
                                'private' => 'Privado',
                            ])
                            ->default('public')
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado'),
                        
                        Forms\Components\Toggle::make('is_milestone')
                            ->label('Es Hito'),
                        
                        Forms\Components\Toggle::make('notify_followers')
                            ->label('Notificar Seguidores'),
                        
                        Forms\Components\Toggle::make('show_in_feed')
                            ->label('Mostrar en Feed'),
                        
                        Forms\Components\Toggle::make('allow_interactions')
                            ->label('Permitir Interacciones'),
                    ])->columns(2),

                Forms\Components\Section::make('Métricas de Engagement')
                    ->schema([
                        Forms\Components\TextInput::make('engagement_score')
                            ->label('Puntuación de Engagement')
                            ->numeric()
                            ->step(1),
                        
                        Forms\Components\TextInput::make('likes_count')
                            ->label('Likes')
                            ->numeric()
                            ->step(1),
                        
                        Forms\Components\TextInput::make('loves_count')
                            ->label('Loves')
                            ->numeric()
                            ->step(1),
                        
                        Forms\Components\TextInput::make('wow_count')
                            ->label('Wow')
                            ->numeric()
                            ->step(1),
                        
                        Forms\Components\TextInput::make('comments_count')
                            ->label('Comentarios')
                            ->numeric()
                            ->step(1),
                        
                        Forms\Components\TextInput::make('shares_count')
                            ->label('Compartidos')
                            ->numeric()
                            ->step(1),
                        
                        Forms\Components\TextInput::make('bookmarks_count')
                            ->label('Bookmarks')
                            ->numeric()
                            ->step(1),
                        
                        Forms\Components\TextInput::make('views_count')
                            ->label('Visualizaciones')
                            ->numeric()
                            ->step(1),
                    ])->columns(4),

                Forms\Components\Section::make('Geolocalización')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitud')
                            ->numeric()
                            ->step(0.00000001),
                        
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitud')
                            ->numeric()
                            ->step(0.00000001),
                        
                        Forms\Components\TextInput::make('location_name')
                            ->label('Nombre del Lugar')
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Información Temporal y Algoritmo')
                    ->schema([
                        Forms\Components\DateTimePicker::make('activity_occurred_at')
                            ->label('Fecha de Ocurrencia'),
                        
                        Forms\Components\Toggle::make('is_real_time')
                            ->label('Tiempo Real'),
                        
                        Forms\Components\TextInput::make('activity_group')
                            ->label('Grupo de Actividad')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('relevance_score')
                            ->label('Puntuación de Relevancia')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\DateTimePicker::make('boost_until')
                            ->label('Impulsar Hasta'),
                        
                        Forms\Components\KeyValue::make('algorithm_data')
                            ->label('Datos del Algoritmo')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir dato'),
                    ])->columns(2),
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
                
                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'energy_saved' => 'success',
                        'solar_generated' => 'warning',
                        'achievement_unlocked' => 'info',
                        'challenge_completed' => 'primary',
                        'installation_completed' => 'success',
                        'cooperative_joined' => 'info',
                        'content_published' => 'warning',
                        'investment_made' => 'success',
                        'milestone_reached' => 'danger',
                        'community_contribution' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'energy_saved' => 'Ahorro Energético',
                        'solar_generated' => 'Energía Solar',
                        'achievement_unlocked' => 'Logro',
                        'project_funded' => 'Proyecto',
                        'installation_completed' => 'Instalación',
                        'cooperative_joined' => 'Cooperativa',
                        'roof_published' => 'Techo',
                        'investment_made' => 'Inversión',
                        'production_right_sold' => 'Derecho',
                        'challenge_completed' => 'Desafío',
                        'milestone_reached' => 'Hito',
                        'content_published' => 'Contenido',
                        'expert_verified' => 'Experto',
                        'review_published' => 'Review',
                        'topic_created' => 'Tema',
                        'community_contribution' => 'Comunidad',
                        'carbon_milestone' => 'CO2',
                        'efficiency_improvement' => 'Eficiencia',
                        'grid_contribution' => 'Red',
                        'sustainability_goal' => 'Sostenibilidad',
                        'other' => 'Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('energy_amount_kwh')
                    ->label('Energía (kWh)')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('N/A'),
                
                Tables\Columns\TextColumn::make('cost_savings_eur')
                    ->label('Ahorro (€)')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('N/A'),
                
                Tables\Columns\TextColumn::make('co2_savings_kg')
                    ->label('CO2 (kg)')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('N/A'),
                
                Tables\Columns\TextColumn::make('community_impact_score')
                    ->label('Impacto')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state >= 40 => 'info',
                        default => 'gray',
                    })
                    ->toggleable()
                    ->placeholder('N/A'),
                
                Tables\Columns\BadgeColumn::make('visibility')
                    ->label('Visibilidad')
                    ->colors([
                        'success' => 'public',
                        'warning' => 'cooperative',
                        'info' => 'followers',
                        'gray' => 'private',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => 'Público',
                        'cooperative' => 'Cooperativa',
                        'followers' => 'Seguidores',
                        'private' => 'Privado',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star'),
                
                Tables\Columns\IconColumn::make('is_milestone')
                    ->label('Hito')
                    ->boolean()
                    ->trueIcon('heroicon-o-flag')
                    ->falseIcon('heroicon-o-flag'),
                
                Tables\Columns\TextColumn::make('engagement_score')
                    ->label('Engagement')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comentarios')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Ubicación')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('N/A'),
                
                Tables\Columns\TextColumn::make('activity_occurred_at')
                    ->label('Ocurrió')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('activity_type')
                    ->label('Tipo de Actividad')
                    ->options([
                        'energy_saved' => 'Ahorro Energético',
                        'solar_generated' => 'Energía Solar Generada',
                        'achievement_unlocked' => 'Logro Desbloqueado',
                        'challenge_completed' => 'Desafío Completado',
                        'installation_completed' => 'Instalación Completada',
                        'cooperative_joined' => 'Se Unió a Cooperativa',
                        'content_published' => 'Contenido Publicado',
                        'investment_made' => 'Inversión Realizada',
                        'milestone_reached' => 'Hito Alcanzado',
                        'community_contribution' => 'Contribución Comunitaria',
                    ]),
                
                Tables\Filters\SelectFilter::make('visibility')
                    ->label('Visibilidad')
                    ->options([
                        'public' => 'Público',
                        'cooperative' => 'Cooperativa',
                        'followers' => 'Seguidores',
                        'private' => 'Privado',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacados'),
                
                Tables\Filters\TernaryFilter::make('is_milestone')
                    ->label('Hitos'),
                
                Tables\Filters\Filter::make('has_energy_data')
                    ->label('Con Datos de Energía')
                    ->query(fn ($query) => $query->whereNotNull('energy_amount_kwh')),
                
                Tables\Filters\Filter::make('has_cost_savings')
                    ->label('Con Ahorros')
                    ->query(fn ($query) => $query->whereNotNull('cost_savings_eur')),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Recientes (7 días)')
                    ->query(fn ($query) => $query->where('created_at', '>=', now()->subWeek())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn (ActivityFeed $record) => $record->is_featured ? 'Quitar Destacado' : 'Destacar')
                    ->icon(fn (ActivityFeed $record) => $record->is_featured ? 'heroicon-o-star' : 'heroicon-o-star')
                    ->color(fn (ActivityFeed $record) => $record->is_featured ? 'gray' : 'warning')
                    ->action(fn (ActivityFeed $record) => $record->update(['is_featured' => !$record->is_featured])),
                
                Tables\Actions\Action::make('toggle_milestone')
                    ->label(fn (ActivityFeed $record) => $record->is_milestone ? 'Quitar Hito' : 'Marcar Hito')
                    ->icon(fn (ActivityFeed $record) => $record->is_milestone ? 'heroicon-o-flag' : 'heroicon-o-flag')
                    ->color(fn (ActivityFeed $record) => $record->is_milestone ? 'gray' : 'danger')
                    ->action(fn (ActivityFeed $record) => $record->update(['is_milestone' => !$record->is_milestone])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Marcar como Destacado')
                        ->icon('heroicon-o-star')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_featured' => true]);
                            }
                        }),
                    Tables\Actions\BulkAction::make('mark_milestone')
                        ->label('Marcar como Hito')
                        ->icon('heroicon-o-flag')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_milestone' => true]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListActivityFeeds::route('/'),
            'create' => Pages\CreateActivityFeed::route('/create'),
            'edit' => Pages\EditActivityFeed::route('/{record}/edit'),
        ];
    }
}
