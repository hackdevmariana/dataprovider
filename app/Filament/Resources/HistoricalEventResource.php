<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoricalEventResource\Pages;
use App\Filament\Resources\HistoricalEventResource\RelationManagers;
use App\Models\HistoricalEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistoricalEventResource extends Resource
{
    protected static ?string $model = HistoricalEvent::class;

    protected static ?string $navigationIcon = 'fas-landmark';

    protected static ?string $navigationGroup = 'Historia y Cultura';

    protected static ?string $navigationLabel = 'Eventos Históricos';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Evento Histórico';

    protected static ?string $pluralModelLabel = 'Eventos Históricos';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(500)
                            ->label('Título del Evento')
                            ->placeholder('Nombre del evento histórico...'),
                        
                        Forms\Components\TextInput::make('alternative_names')
                            ->maxLength(500)
                            ->label('Nombres Alternativos')
                            ->placeholder('Otros nombres conocidos del evento...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(2000)
                            ->label('Descripción')
                            ->rows(4)
                            ->placeholder('Descripción detallada del evento histórico...'),
                        
                        Forms\Components\Textarea::make('summary')
                            ->maxLength(1000)
                            ->label('Resumen')
                            ->rows(3)
                            ->placeholder('Resumen ejecutivo del evento...'),
                    ])->columns(1),

                Forms\Components\Section::make('Fechas y Período')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha aproximada del inicio del evento'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Fin')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha aproximada del fin del evento'),
                        
                        Forms\Components\Select::make('date_precision')
                            ->options([
                                'exact' => '📅 Fecha Exacta',
                                'month' => '📅 Mes y Año',
                                'year' => '📅 Solo Año',
                                'decade' => '📅 Década',
                                'century' => '📅 Siglo',
                                'millennium' => '📅 Milenio',
                                'approximate' => '📅 Aproximado',
                                'unknown' => '❓ Desconocido',
                            ])
                            ->default('year')
                            ->label('Precisión de la Fecha'),
                        
                        Forms\Components\TextInput::make('era')
                            ->maxLength(100)
                            ->label('Era Histórica')
                            ->placeholder('Antigüedad, Edad Media, Renacimiento...'),
                        
                        Forms\Components\TextInput::make('period')
                            ->maxLength(100)
                            ->label('Período Específico')
                            ->placeholder('Dinastía, reinado, período específico...'),
                    ])->columns(2),

                Forms\Components\Section::make('Clasificación')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->options([
                                'politics' => '🏛️ Política',
                                'war' => '⚔️ Guerra',
                                'religion' => '⛪ Religión',
                                'culture' => '🎨 Cultura',
                                'science' => '🔬 Ciencia',
                                'technology' => '💻 Tecnología',
                                'economy' => '💰 Economía',
                                'social' => '👥 Social',
                                'environmental' => '🌍 Ambiental',
                                'disaster' => '🌋 Desastre Natural',
                                'discovery' => '🔍 Descubrimiento',
                                'invention' => '💡 Invención',
                                'revolution' => '🔥 Revolución',
                                'treaty' => '📜 Tratado',
                                'coronation' => '👑 Coronación',
                                'assassination' => '🗡️ Asesinato',
                                'battle' => '⚔️ Batalla',
                                'exploration' => '🗺️ Exploración',
                                'migration' => '🚶 Migración',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría Principal'),
                        
                        Forms\Components\Select::make('importance_level')
                            ->options([
                                'world' => '🌍 Mundial',
                                'continental' => '🌎 Continental',
                                'national' => '🏳️ Nacional',
                                'regional' => '🏘️ Regional',
                                'local' => '🏠 Local',
                                'personal' => '👤 Personal',
                            ])
                            ->required()
                            ->label('Nivel de Importancia'),
                        
                        Forms\Components\Select::make('impact_scale')
                            ->options([
                                'minimal' => '🟢 Mínimo',
                                'minor' => '🟡 Menor',
                                'moderate' => '🟠 Moderado',
                                'major' => '🔴 Mayor',
                                'catastrophic' => '⚫ Catastrófico',
                                'transformative' => '🟣 Transformador',
                            ])
                            ->label('Escala de Impacto'),
                        
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->separator(',')
                            ->placeholder('Agregar etiquetas...'),
                    ])->columns(2),

                Forms\Components\Section::make('Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255)
                            ->label('Ubicación')
                            ->placeholder('Ciudad, región o lugar específico...'),
                        
                        Forms\Components\TextInput::make('country')
                            ->maxLength(100)
                            ->label('País')
                            ->placeholder('País donde ocurrió el evento...'),
                        
                        Forms\Components\TextInput::make('region')
                            ->maxLength(100)
                            ->label('Región')
                            ->placeholder('Región, provincia o estado...'),
                        
                        Forms\Components\TextInput::make('coordinates')
                            ->maxLength(100)
                            ->label('Coordenadas')
                            ->placeholder('Latitud, Longitud...'),
                        
                        Forms\Components\Toggle::make('location_uncertain')
                            ->label('Ubicación Incierta')
                            ->default(false)
                            ->helperText('Indica si la ubicación exacta es incierta'),
                    ])->columns(2),

                Forms\Components\Section::make('Personajes y Entidades')
                    ->schema([
                        Forms\Components\TextInput::make('key_figures')
                            ->maxLength(500)
                            ->label('Figuras Clave')
                            ->placeholder('Personas importantes involucradas...'),
                        
                        Forms\Components\TextInput::make('organizations')
                            ->maxLength(500)
                            ->label('Organizaciones')
                            ->placeholder('Instituciones, grupos o entidades...'),
                        
                        Forms\Components\TextInput::make('dynasties')
                            ->maxLength(255)
                            ->label('Dinastías')
                            ->placeholder('Familias gobernantes...'),
                        
                        Forms\Components\TextInput::make('rulers')
                            ->maxLength(255)
                            ->label('Gobernantes')
                            ->placeholder('Reyes, emperadores, líderes...'),
                    ])->columns(2),

                Forms\Components\Section::make('Causas y Consecuencias')
                    ->schema([
                        Forms\Components\Textarea::make('causes')
                            ->maxLength(1000)
                            ->label('Causas')
                            ->rows(3)
                            ->placeholder('Factores que llevaron al evento...'),
                        
                        Forms\Components\Textarea::make('consequences')
                            ->maxLength(1000)
                            ->label('Consecuencias')
                            ->rows(3)
                            ->placeholder('Impacto y resultados del evento...'),
                        
                        Forms\Components\Textarea::make('long_term_effects')
                            ->maxLength(1000)
                            ->label('Efectos a Largo Plazo')
                            ->rows(3)
                            ->placeholder('Influencia en la historia posterior...'),
                    ])->columns(1),

                Forms\Components\Section::make('Fuentes y Evidencia')
                    ->schema([
                        Forms\Components\Textarea::make('sources')
                            ->maxLength(1000)
                            ->label('Fuentes Históricas')
                            ->rows(3)
                            ->placeholder('Documentos, crónicas, testimonios...'),
                        
                        Forms\Components\Select::make('evidence_quality')
                            ->options([
                                'excellent' => '🟢 Excelente',
                                'good' => '🟡 Buena',
                                'fair' => '🟠 Regular',
                                'poor' => '🔴 Pobre',
                                'minimal' => '⚫ Mínima',
                                'contested' => '🟣 Contestada',
                            ])
                            ->label('Calidad de la Evidencia'),
                        
                        Forms\Components\Toggle::make('is_contested')
                            ->label('Evento Contestado')
                            ->default(false)
                            ->helperText('Indica si hay controversia sobre el evento'),
                        
                        Forms\Components\Textarea::make('controversies')
                            ->maxLength(1000)
                            ->label('Controversias')
                            ->rows(3)
                            ->placeholder('Debates o controversias sobre el evento...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_contested')),
                    ])->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\KeyValue::make('additional_data')
                            ->label('Datos Adicionales')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Campo'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas adicionales o comentarios...'),
                    ])->columns(1),

                Forms\Components\Section::make('Estado y Verificación')
                    ->schema([
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado')
                            ->default(false)
                            ->helperText('Indica si el evento ha sido verificado por historiadores'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Evento importante para destacar'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'confirmed' => '✅ Confirmado',
                                'probable' => '🟡 Probable',
                                'possible' => '🟠 Posible',
                                'doubtful' => '🔴 Dudoso',
                                'legendary' => '🟣 Legendario',
                                'mythical' => '🟤 Mítico',
                            ])
                            ->default('confirmed')
                            ->label('Estado de Verificación'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Evento')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'politics',
                        'danger' => 'war',
                        'info' => 'religion',
                        'warning' => 'culture',
                        'success' => 'science',
                        'secondary' => 'technology',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'politics' => '🏛️ Política',
                        'war' => '⚔️ Guerra',
                        'religion' => '⛪ Religión',
                        'culture' => '🎨 Cultura',
                        'science' => '🔬 Ciencia',
                        'technology' => '💻 Tecnología',
                        'economy' => '💰 Economía',
                        'social' => '👥 Social',
                        'environmental' => '🌍 Ambiental',
                        'disaster' => '🌋 Desastre',
                        'discovery' => '🔍 Descubrimiento',
                        'invention' => '💡 Invención',
                        'revolution' => '🔥 Revolución',
                        'treaty' => '📜 Tratado',
                        'coronation' => '👑 Coronación',
                        'assassination' => '🗡️ Asesinato',
                        'battle' => '⚔️ Batalla',
                        'exploration' => '🗺️ Exploración',
                        'migration' => '🚶 Migración',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('date_precision')
                    ->label('Precisión')
                    ->colors([
                        'success' => 'exact',
                        'info' => 'month',
                        'primary' => 'year',
                        'warning' => 'decade',
                        'secondary' => 'century',
                        'danger' => 'millennium',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'exact' => '📅 Exacta',
                        'month' => '📅 Mes/Año',
                        'year' => '📅 Año',
                        'decade' => '📅 Década',
                        'century' => '📅 Siglo',
                        'millennium' => '📅 Milenio',
                        'approximate' => '📅 Aprox.',
                        'unknown' => '❓ Desconocido',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('importance_level')
                    ->label('Importancia')
                    ->colors([
                        'danger' => 'world',
                        'warning' => 'continental',
                        'primary' => 'national',
                        'info' => 'regional',
                        'success' => 'local',
                        'secondary' => 'personal',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'world' => '🌍 Mundial',
                        'continental' => '🌎 Continental',
                        'national' => '🏳️ Nacional',
                        'regional' => '🏘️ Regional',
                        'local' => '🏠 Local',
                        'personal' => '👤 Personal',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('impact_scale')
                    ->label('Impacto')
                    ->colors([
                        'success' => 'minimal',
                        'warning' => 'minor',
                        'info' => 'moderate',
                        'danger' => 'major',
                        'dark' => 'catastrophic',
                        'primary' => 'transformative',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'minimal' => '🟢 Mínimo',
                        'minor' => '🟡 Menor',
                        'moderate' => '🟠 Moderado',
                        'major' => '🔴 Mayor',
                        'catastrophic' => '⚫ Catastrófico',
                        'transformative' => '🟣 Transformador',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('location')
                    ->label('Ubicación')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('key_figures')
                    ->label('Figuras Clave')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_contested')
                    ->label('Contestado')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'confirmed',
                        'warning' => 'probable',
                        'info' => 'possible',
                        'danger' => 'doubtful',
                        'secondary' => 'legendary',
                        'dark' => 'mythical',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'confirmed' => '✅ Confirmado',
                        'probable' => '🟡 Probable',
                        'possible' => '🟠 Posible',
                        'doubtful' => '🔴 Dudoso',
                        'legendary' => '🟣 Legendario',
                        'mythical' => '🟤 Mítico',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'politics' => '🏛️ Política',
                        'war' => '⚔️ Guerra',
                        'religion' => '⛪ Religión',
                        'culture' => '🎨 Cultura',
                        'science' => '🔬 Ciencia',
                        'technology' => '💻 Tecnología',
                        'economy' => '💰 Economía',
                        'social' => '👥 Social',
                        'environmental' => '🌍 Ambiental',
                        'disaster' => '🌋 Desastre',
                        'discovery' => '🔍 Descubrimiento',
                        'invention' => '💡 Invención',
                        'revolution' => '🔥 Revolución',
                        'treaty' => '📜 Tratado',
                        'coronation' => '👑 Coronación',
                        'assassination' => '🗡️ Asesinato',
                        'battle' => '⚔️ Batalla',
                        'exploration' => '🗺️ Exploración',
                        'migration' => '🚶 Migración',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría'),
                
                Tables\Filters\SelectFilter::make('importance_level')
                    ->options([
                        'world' => '🌍 Mundial',
                        'continental' => '🌎 Continental',
                        'national' => '🏳️ Nacional',
                        'regional' => '🏘️ Regional',
                        'local' => '🏠 Local',
                        'personal' => '👤 Personal',
                    ])
                    ->label('Nivel de Importancia'),
                
                Tables\Filters\SelectFilter::make('impact_scale')
                    ->options([
                        'minimal' => '🟢 Mínimo',
                        'minor' => '🟡 Menor',
                        'moderate' => '🟠 Moderado',
                        'major' => '🔴 Mayor',
                        'catastrophic' => '⚫ Catastrófico',
                        'transformative' => '🟣 Transformador',
                    ])
                    ->label('Escala de Impacto'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'confirmed' => '✅ Confirmado',
                        'probable' => '🟡 Probable',
                        'possible' => '🟠 Posible',
                        'doubtful' => '🔴 Dudoso',
                        'legendary' => '🟣 Legendario',
                        'mythical' => '🟤 Mítico',
                    ])
                    ->label('Estado de Verificación'),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificados')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('contested_events')
                    ->label('Eventos Contestados')
                    ->query(fn (Builder $query): Builder => $query->where('is_contested', true)),
                
                Tables\Filters\Filter::make('ancient_times')
                    ->label('Antigüedad')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '<=', '0500-01-01')),
                
                Tables\Filters\Filter::make('middle_ages')
                    ->label('Edad Media')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('start_date', ['0500-01-01', '1500-01-01'])),
                
                Tables\Filters\Filter::make('modern_era')
                    ->label('Era Moderna')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '>=', '1500-01-01')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('fas-eye')
                    ->color('info'),
                
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('fas-edit')
                    ->color('warning'),
                
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn ($record): string => $record->is_featured ? 'Quitar Destacado' : 'Destacar')
                    ->icon(fn ($record): string => $record->is_featured ? 'fas-star' : 'far-star')
                    ->action(function ($record): void {
                        $record->update(['is_featured' => !$record->is_featured]);
                    })
                    ->color(fn ($record): string => $record->is_featured ? 'warning' : 'success'),
                
                Tables\Actions\Action::make('mark_verified')
                    ->label('Verificar')
                    ->icon('fas-check-circle')
                    ->action(function ($record): void {
                        $record->update(['is_verified' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('search_wikipedia')
                    ->label('Buscar en Wikipedia')
                    ->icon('fab-wikipedia-w')
                    ->url(fn ($record): string => "https://es.wikipedia.org/wiki/" . urlencode($record->title))
                    ->openUrlInNewTab()
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('mark_verified')
                        ->label('Marcar como Verificados')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_verified' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Marcar como Destacados')
                        ->icon('fas-star')
                        ->action(function ($records): void {
                            $records->each->update(['is_featured' => true]);
                        })
                        ->color('warning'),
                    
                    Tables\Actions\BulkAction::make('mark_contested')
                        ->label('Marcar como Contestados')
                        ->icon('fas-question-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_contested' => true]);
                        })
                        ->color('danger'),
                ]),
            ])
            ->defaultSort('start_date', 'desc')
            ->paginated([25, 50, 100]);
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
            'index' => Pages\ListHistoricalEvents::route('/'),
            'create' => Pages\CreateHistoricalEvent::route('/create'),
            'view' => Pages\ViewHistoricalEvent::route('/{record}'),
            'edit' => Pages\EditHistoricalEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
