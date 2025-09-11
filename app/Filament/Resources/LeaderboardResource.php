<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderboardResource\Pages;
use App\Models\Leaderboard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaderboardResource extends Resource
{
    protected static ?string $model = Leaderboard::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Social System';

    protected static ?string $modelLabel = 'Tabla de Clasificación';

    protected static ?string $pluralModelLabel = 'Tablas de Clasificación';

    protected static ?int $navigationSort = 7;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'energy_savings' => 'Ahorro de Energía',
                                'reputation' => 'Reputación',
                                'contributions' => 'Contribuciones',
                                'projects' => 'Proyectos',
                                'community_engagement' => 'Participación Comunitaria',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('period')
                            ->label('Período')
                            ->options([
                                'daily' => 'Diario',
                                'weekly' => 'Semanal',
                                'monthly' => 'Mensual',
                                'yearly' => 'Anual',
                                'all_time' => 'Histórico',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('scope')
                            ->label('Ámbito')
                            ->options([
                                'global' => 'Global',
                                'cooperative' => 'Cooperativa',
                                'regional' => 'Regional',
                                'topic' => 'Tema',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('scope_id')
                            ->label('ID del Ámbito')
                            ->numeric()
                            ->visible(fn ($get) => $get('scope') !== 'global')
                            ->helperText('ID específico del ámbito seleccionado'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\TextInput::make('max_positions')
                            ->label('Máximo Posiciones')
                            ->numeric()
                            ->default(100)
                            ->minValue(10)
                            ->maxValue(1000)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Público')
                            ->default(true),
                        
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio')
                            ->required(),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Fin')
                            ->after('start_date'),
                    ])->columns(3),

                Forms\Components\Section::make('Criterios y Reglas')
                    ->schema([
                        Forms\Components\KeyValue::make('criteria')
                            ->label('Criterios de Clasificación')
                            ->keyLabel('Criterio')
                            ->valueLabel('Peso/Valor')
                            ->addActionLabel('Añadir criterio')
                            ->required(),
                        
                        Forms\Components\KeyValue::make('rules')
                            ->label('Reglas de Cálculo')
                            ->keyLabel('Regla')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Añadir regla'),
                        
                        Forms\Components\Textarea::make('metadata')
                            ->label('Metadatos Adicionales')
                            ->rows(3)
                            ->helperText('Información adicional en formato JSON'),
                    ]),

                Forms\Components\Section::make('Rankings Actuales')
                    ->schema([
                        Forms\Components\DateTimePicker::make('last_calculated_at')
                            ->label('Última Actualización')
                            ->displayFormat('d/m/Y H:i'),
                        
                        Forms\Components\Textarea::make('current_rankings')
                            ->label('Rankings Actuales (JSON)')
                            ->rows(5)
                            ->disabled()
                            ->helperText('Rankings calculados automáticamente'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'energy_savings',
                        'primary' => 'reputation',
                        'warning' => 'contributions',
                        'info' => 'projects',
                        'gray' => 'community_engagement',
                        'purple' => 'carbon_reduction',
                        'orange' => 'knowledge_sharing',
                        'blue' => 'innovation',
                        'yellow' => 'sustainability_score',
                        'pink' => 'peer_mentoring',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'energy_savings' => 'Ahorro Energía',
                        'reputation' => 'Reputación',
                        'contributions' => 'Contribuciones',
                        'projects' => 'Proyectos',
                        'community_engagement' => 'Participación Comunitaria',
                        'carbon_reduction' => 'Reducción de Carbono',
                        'knowledge_sharing' => 'Compartir Conocimiento',
                        'innovation' => 'Innovación',
                        'sustainability_score' => 'Puntuación de Sostenibilidad',
                        'peer_mentoring' => 'Mentoría entre Pares',
                        default => ucwords(str_replace('_', ' ', $state)),
                    }),
                
                Tables\Columns\BadgeColumn::make('period')
                    ->label('Período')
                    ->colors([
                        'gray' => 'daily',
                        'blue' => 'weekly',
                        'green' => 'monthly',
                        'orange' => 'yearly',
                        'red' => 'all_time',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'daily' => 'Diario',
                        'weekly' => 'Semanal',
                        'monthly' => 'Mensual',
                        'yearly' => 'Anual',
                        'all_time' => 'Histórico',
                    }),
                
                Tables\Columns\BadgeColumn::make('scope')
                    ->label('Ámbito')
                    ->colors([
                        'primary' => 'global',
                        'success' => 'cooperative',
                        'warning' => 'regional',
                        'info' => 'topic',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'global' => 'Global',
                        'cooperative' => 'Cooperativa',
                        'regional' => 'Regional',
                        'topic' => 'Tema',
                    }),
                
                Tables\Columns\TextColumn::make('max_positions')
                    ->label('Máx. Pos.')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Participantes')
                    ->getStateUsing(fn ($record) => count($record->getRankings()))
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_currently_active')
                    ->label('En Curso')
                    ->getStateUsing(fn ($record) => $record->isCurrentlyActive())
                    ->boolean()
                    ->trueIcon('heroicon-o-play')
                    ->falseIcon('heroicon-o-pause'),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date()
                    ->sortable()
                    ->placeholder('Sin fin'),
                
                Tables\Columns\TextColumn::make('last_calculated_at')
                    ->label('Última Actualización')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Nunca'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'energy_savings' => 'Ahorro de Energía',
                        'reputation' => 'Reputación',
                        'contributions' => 'Contribuciones',
                        'projects' => 'Proyectos',
                        'community_engagement' => 'Participación Comunitaria',
                        'carbon_reduction' => 'Reducción de Carbono',
                        'knowledge_sharing' => 'Compartir Conocimiento',
                        'innovation' => 'Innovación',
                        'sustainability_score' => 'Puntuación de Sostenibilidad',
                        'peer_mentoring' => 'Mentoría entre Pares',
                    ]),
                
                Tables\Filters\SelectFilter::make('period')
                    ->options([
                        'daily' => 'Diario',
                        'weekly' => 'Semanal',
                        'monthly' => 'Mensual',
                        'yearly' => 'Anual',
                        'all_time' => 'Histórico',
                    ]),
                
                Tables\Filters\SelectFilter::make('scope')
                    ->options([
                        'global' => 'Global',
                        'cooperative' => 'Cooperativa',
                        'regional' => 'Regional',
                        'topic' => 'Tema',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Público'),
                
                Tables\Filters\Filter::make('current')
                    ->label('En Curso')
                    ->query(fn ($query) => $query->current()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('recalculate')
                    ->label('Recalcular')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Leaderboard $record) {
                        // Aquí iría la lógica de recálculo
                        $record->update(['last_calculated_at' => now()]);
                    })
                    ->visible(fn (Leaderboard $record) => $record->is_active),
                
                Tables\Actions\Action::make('activate')
                    ->label('Activar')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(fn (Leaderboard $record) => $record->update(['is_active' => true]))
                    ->visible(fn (Leaderboard $record) => !$record->is_active),
                
                Tables\Actions\Action::make('deactivate')
                    ->label('Desactivar')
                    ->icon('heroicon-o-pause')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Leaderboard $record) => $record->update(['is_active' => false]))
                    ->visible(fn (Leaderboard $record) => $record->is_active),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaderboards::route('/'),
            'create' => Pages\CreateLeaderboard::route('/create'),
            'edit' => Pages\EditLeaderboard::route('/{record}/edit'),
        ];
    }
}