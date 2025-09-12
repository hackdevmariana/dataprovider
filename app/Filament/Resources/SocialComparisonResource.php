<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialComparisonResource\Pages;
use App\Models\SocialComparison;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SocialComparisonResource extends Resource
{
    protected static ?string $model = SocialComparison::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Social System';

    protected static ?string $modelLabel = 'Comparación Social';

    protected static ?string $pluralModelLabel = 'Comparaciones Sociales';

    protected static ?int $navigationSort = 6;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('comparison_type')
                            ->label('Tipo de Comparación')
                            ->options([
                                'energy_savings' => 'Ahorro de Energía',
                                'carbon_reduction' => 'Reducción de Carbono',
                                'community_participation' => 'Participación Comunitaria',
                                'project_contributions' => 'Contribuciones a Proyectos',
                                'knowledge_sharing' => 'Compartir Conocimiento',
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
                                'personal' => 'Personal',
                                'cooperative' => 'Cooperativa',
                                'regional' => 'Regional',
                                'national' => 'Nacional',
                                'global' => 'Global',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Valores y Métricas')
                    ->schema([
                        Forms\Components\TextInput::make('user_value')
                            ->label('Valor del Usuario')
                            ->numeric()
                            ->step(0.0001)
                            ->required(),
                        
                        Forms\Components\TextInput::make('unit')
                            ->label('Unidad')
                            ->placeholder('kWh, kg_co2, points, etc.')
                            ->required(),
                        
                        Forms\Components\TextInput::make('average_value')
                            ->label('Valor Promedio')
                            ->numeric()
                            ->step(0.0001),
                        
                        Forms\Components\TextInput::make('median_value')
                            ->label('Valor Mediano')
                            ->numeric()
                            ->step(0.0001),
                        
                        Forms\Components\TextInput::make('best_value')
                            ->label('Mejor Valor')
                            ->numeric()
                            ->step(0.0001),
                        
                        Forms\Components\TextInput::make('user_rank')
                            ->label('Ranking del Usuario')
                            ->numeric()
                            ->minValue(1),
                        
                        Forms\Components\TextInput::make('total_participants')
                            ->label('Total Participantes')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        
                        Forms\Components\TextInput::make('percentile')
                            ->label('Percentil')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01),
                    ])->columns(3),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\DatePicker::make('comparison_date')
                            ->label('Fecha de Comparación')
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Comparación Pública')
                            ->default(true),
                        
                        Forms\Components\KeyValue::make('breakdown')
                            ->label('Desglose Detallado')
                            ->keyLabel('Métrica')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir métrica'),
                    ]),
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
                
                Tables\Columns\BadgeColumn::make('comparison_type')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'energy_savings',
                        'info' => 'carbon_reduction',
                        'warning' => 'community_participation',
                        'primary' => 'project_contribution',
                        'gray' => 'knowledge_sharing',
                        'purple' => 'renewable_energy_usage',
                        'orange' => 'energy_efficiency',
                        'blue' => 'sustainability_score',
                        'yellow' => 'peer_engagement',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'energy_savings' => 'Ahorro Energía',
                        'carbon_reduction' => 'Reducción CO2',
                        'community_participation' => 'Participación Comunitaria',
                        'project_contribution' => 'Contribuciones a Proyectos',
                        'knowledge_sharing' => 'Compartir Conocimiento',
                        'renewable_energy_usage' => 'Uso Energía Renovable',
                        'energy_efficiency' => 'Eficiencia Energética',
                        'sustainability_score' => 'Puntuación Sostenibilidad',
                        'peer_engagement' => 'Compromiso entre Pares',
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
                
                Tables\Columns\TextColumn::make('user_value')
                    ->label('Valor Usuario')
                    ->formatStateUsing(fn ($state, $record) => number_format($state, 2) . ' ' . $record->unit)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user_rank')
                    ->label('Ranking')
                    ->formatStateUsing(fn ($state, $record) => $state ? "#$state de {$record->total_participants}" : 'N/A')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('percentile')
                    ->label('Percentil')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '%' : 'N/A')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('performance_category')
                    ->label('Rendimiento')
                    ->getStateUsing(fn ($record) => $record->getPerformanceCategory())
                    ->colors([
                        'success' => 'excellent',
                        'info' => 'good',
                        'warning' => 'average',
                        'gray' => 'below_average',
                        'danger' => 'needs_improvement',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'excellent' => 'Excelente',
                        'good' => 'Bueno',
                        'average' => 'Promedio',
                        'below_average' => 'Bajo Promedio',
                        'needs_improvement' => 'Necesita Mejorar',
                        default => 'N/A',
                    }),
                
                Tables\Columns\TextColumn::make('comparison_date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('comparison_type')
                    ->options([
                        'energy_savings' => 'Ahorro de Energía',
                        'carbon_reduction' => 'Reducción de Carbono',
                        'community_participation' => 'Participación Comunitaria',
                        'project_contribution' => 'Contribuciones a Proyectos',
                        'knowledge_sharing' => 'Compartir Conocimiento',
                        'renewable_energy_usage' => 'Uso de Energía Renovable',
                        'energy_efficiency' => 'Eficiencia Energética',
                        'sustainability_score' => 'Puntuación de Sostenibilidad',
                        'peer_engagement' => 'Compromiso entre Pares',
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
                        'personal' => 'Personal',
                        'cooperative' => 'Cooperativa',
                        'regional' => 'Regional',
                        'national' => 'Nacional',
                        'global' => 'Global',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Público'),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Reciente (30 días)')
                    ->query(fn ($query) => $query->where('comparison_date', '>=', now()->subDays(30))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('comparison_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialComparisons::route('/'),
            'create' => Pages\CreateSocialComparison::route('/create'),
            'edit' => Pages\EditSocialComparison::route('/{record}/edit'),
        ];
    }
}