<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsAggregationResource\Pages;
use App\Models\NewsAggregation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsAggregationResource extends Resource
{
    protected static ?string $model = NewsAggregation::class;

    protected static ?string $navigationIcon = 'fas-newspaper';

    protected static ?string $navigationGroup = 'Noticias y Tendencias';

    protected static ?string $navigationLabel = 'Agregaciones de Noticias';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Agregación de Noticias';

    protected static ?string $pluralModelLabel = 'Agregaciones de Noticias';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('aggregation_title')
                            ->required()
                            ->maxLength(255)
                            ->label('Título de la Agregación')
                            ->placeholder('Título de la agregación de noticias...'),
                        
                        Forms\Components\TextInput::make('aggregation_code')
                            ->maxLength(100)
                            ->label('Código de Agregación')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción de la agregación...'),
                        
                        Forms\Components\Select::make('aggregation_type')
                            ->options([
                                'topic_based' => '📋 Basada en Tema',
                                'source_based' => '📰 Basada en Fuente',
                                'time_based' => '⏰ Basada en Tiempo',
                                'geographic_based' => '🌍 Basada en Geografía',
                                'category_based' => '🏷️ Basada en Categoría',
                                'sentiment_based' => '😊 Basada en Sentimiento',
                                'trending_based' => '🔥 Basada en Trending',
                                'custom_based' => '⚙️ Basada en Criterios Personalizados',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Agregación'),
                        
                        Forms\Components\Select::make('content_category')
                            ->options([
                                'politics' => '🏛️ Política',
                                'technology' => '💻 Tecnología',
                                'entertainment' => '🎬 Entretenimiento',
                                'sports' => '⚽ Deportes',
                                'business' => '💼 Negocios',
                                'health' => '🏥 Salud',
                                'science' => '🔬 Ciencia',
                                'environment' => '🌍 Medio Ambiente',
                                'education' => '🎓 Educación',
                                'culture' => '🎨 Cultura',
                                'social_issues' => '🤝 Temas Sociales',
                                'crime' => '🚨 Crimen',
                                'weather' => '🌤️ Clima',
                                'travel' => '✈️ Viajes',
                                'food' => '🍕 Comida',
                                'fashion' => '👗 Moda',
                                'automotive' => '🚗 Automóviles',
                                'finance' => '💰 Finanzas',
                                'real_estate' => '🏠 Bienes Raíces',
                                'energy' => '⚡ Energía',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría de Contenido'),
                        
                        Forms\Components\Select::make('aggregation_method')
                            ->options([
                                'automatic' => '🤖 Automática',
                                'manual' => '👤 Manual',
                                'hybrid' => '🔄 Híbrida',
                                'ai_powered' => '🧠 IA',
                                'algorithmic' => '📊 Algorítmica',
                                'curated' => '✍️ Curada',
                                'crowdsourced' => '👥 Crowdsourcing',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Método de Agregación'),
                        
                        Forms\Components\Select::make('update_frequency')
                            ->options([
                                'real_time' => '⚡ Tiempo Real',
                                'hourly' => '🕐 Cada Hora',
                                'daily' => '📅 Diario',
                                'weekly' => '📅 Semanal',
                                'monthly' => '📅 Mensual',
                                'on_demand' => '🎯 Bajo Demanda',
                                'event_triggered' => '🚨 Por Evento',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Frecuencia de Actualización'),
                    ])->columns(2),

                Forms\Components\Section::make('Criterios de Agregación')
                    ->schema([
                        Forms\Components\Textarea::make('aggregation_criteria')
                            ->required()
                            ->maxLength(1000)
                            ->label('Criterios de Agregación')
                            ->rows(3)
                            ->placeholder('Criterios utilizados para la agregación...'),
                        
                        Forms\Components\KeyValue::make('filter_parameters')
                            ->label('Parámetros de Filtro')
                            ->keyLabel('Parámetro')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Parámetro'),
                        
                        Forms\Components\Textarea::make('exclusion_criteria')
                            ->maxLength(500)
                            ->label('Criterios de Exclusión')
                            ->rows(2)
                            ->placeholder('Criterios para excluir contenido...'),
                        
                        Forms\Components\TextInput::make('min_articles_count')
                            ->numeric()
                            ->label('Mínimo de Artículos')
                            ->placeholder('Número mínimo de artículos...'),
                        
                        Forms\Components\TextInput::make('max_articles_count')
                            ->numeric()
                            ->label('Máximo de Artículos')
                            ->placeholder('Número máximo de artículos...'),
                        
                        Forms\Components\Toggle::make('include_duplicates')
                            ->label('Incluir Duplicados')
                            ->default(false)
                            ->helperText('Incluir artículos duplicados'),
                        
                        Forms\Components\Toggle::make('include_archived')
                            ->label('Incluir Archivados')
                            ->default(false)
                            ->helperText('Incluir artículos archivados'),
                        
                        Forms\Components\Toggle::make('include_paywall')
                            ->label('Incluir Paywall')
                            ->default(false)
                            ->helperText('Incluir artículos con paywall'),
                    ])->columns(2),

                Forms\Components\Section::make('Fuentes y Cobertura')
                    ->schema([
                        Forms\Components\Textarea::make('sources_included')
                            ->maxLength(1000)
                            ->label('Fuentes Incluidas')
                            ->rows(3)
                            ->placeholder('Fuentes de noticias incluidas...'),
                        
                        Forms\Components\Textarea::make('sources_excluded')
                            ->maxLength(500)
                            ->label('Fuentes Excluidas')
                            ->rows(2)
                            ->placeholder('Fuentes de noticias excluidas...'),
                        
                        Forms\Components\Select::make('geographic_coverage')
                            ->options([
                                'global' => '🌍 Global',
                                'national' => '🏳️ Nacional',
                                'regional' => '🏘️ Regional',
                                'local' => '🏠 Local',
                                'continental' => '🌎 Continental',
                                'specific_countries' => '📍 Países Específicos',
                                'other' => '❓ Otro',
                            ])
                            ->label('Cobertura Geográfica'),
                        
                        Forms\Components\TextInput::make('specific_locations')
                            ->maxLength(500)
                            ->label('Ubicaciones Específicas')
                            ->placeholder('Países, regiones o ciudades específicas...'),
                        
                        Forms\Components\TextInput::make('language_coverage')
                            ->maxLength(255)
                            ->label('Cobertura de Idiomas')
                            ->placeholder('Idiomas cubiertos...'),
                        
                        Forms\Components\Toggle::make('multilingual_support')
                            ->label('Soporte Multilingüe')
                            ->default(false)
                            ->helperText('Soporte para múltiples idiomas'),
                        
                        Forms\Components\TextInput::make('translation_languages')
                            ->maxLength(255)
                            ->label('Idiomas de Traducción')
                            ->placeholder('Idiomas para traducción...')
                            ->visible(fn (Forms\Get $get): bool => $get('multilingual_support')),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Técnica')
                    ->schema([
                        Forms\Components\TextInput::make('api_endpoint')
                            ->maxLength(500)
                            ->label('Endpoint de API')
                            ->placeholder('URL del endpoint de API...'),
                        
                        Forms\Components\TextInput::make('api_key')
                            ->maxLength(255)
                            ->label('Clave de API')
                            ->placeholder('Clave de API...'),
                        
                        Forms\Components\TextInput::make('rate_limit')
                            ->numeric()
                            ->label('Límite de Tasa')
                            ->placeholder('Límite de solicitudes por minuto...'),
                        
                        Forms\Components\TextInput::make('timeout_seconds')
                            ->numeric()
                            ->label('Timeout (segundos)')
                            ->placeholder('Tiempo de espera en segundos...'),
                        
                        Forms\Components\Toggle::make('use_cache')
                            ->label('Usar Caché')
                            ->default(true)
                            ->helperText('Utilizar sistema de caché'),
                        
                        Forms\Components\TextInput::make('cache_duration_minutes')
                            ->numeric()
                            ->label('Duración del Caché (minutos)')
                            ->placeholder('Duración del caché...')
                            ->visible(fn (Forms\Get $get): bool => $get('use_cache')),
                        
                        Forms\Components\Toggle::make('use_proxy')
                            ->label('Usar Proxy')
                            ->default(false)
                            ->helperText('Utilizar servidor proxy'),
                        
                        Forms\Components\TextInput::make('proxy_url')
                            ->maxLength(500)
                            ->label('URL del Proxy')
                            ->placeholder('URL del servidor proxy...')
                            ->visible(fn (Forms\Get $get): bool => $get('use_proxy')),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Calidad')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '✅ Activa',
                                'inactive' => '❌ Inactiva',
                                'testing' => '🧪 En Pruebas',
                                'maintenance' => '🔧 En Mantenimiento',
                                'error' => '🚨 Con Error',
                                'paused' => '⏸️ Pausada',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('active')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Agregación importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('La agregación ha sido verificada'),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Pública')
                            ->default(true)
                            ->helperText('La agregación es pública'),
                        
                        Forms\Components\Select::make('quality_rating')
                            ->options([
                                'excellent' => '🟢 Excelente (5/5)',
                                'very_good' => '🟢 Muy Buena (4/5)',
                                'good' => '🟡 Buena (3/5)',
                                'fair' => '🟠 Regular (2/5)',
                                'poor' => '🔴 Pobre (1/5)',
                                'not_rated' => '⚫ No Evaluada',
                            ])
                            ->label('Calificación de Calidad'),
                        
                        Forms\Components\TextInput::make('last_successful_update')
                            ->label('Última Actualización Exitosa')
                            ->disabled()
                            ->helperText('Fecha de la última actualización exitosa'),
                        
                        Forms\Components\TextInput::make('error_count')
                            ->numeric()
                            ->label('Contador de Errores')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de errores acumulados'),
                        
                        Forms\Components\Textarea::make('last_error_message')
                            ->maxLength(500)
                            ->label('Último Mensaje de Error')
                            ->rows(2)
                            ->disabled()
                            ->helperText('Mensaje del último error'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas adicionales...'),
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
                
                Tables\Columns\TextColumn::make('aggregation_title')
                    ->label('Agregación')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('aggregation_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'topic_based',
                        'success' => 'source_based',
                        'warning' => 'time_based',
                        'info' => 'geographic_based',
                        'danger' => 'category_based',
                        'secondary' => 'sentiment_based',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'topic_based' => '📋 Basada en Tema',
                        'source_based' => '📰 Basada en Fuente',
                        'time_based' => '⏰ Basada en Tiempo',
                        'geographic_based' => '🌍 Basada en Geografía',
                        'category_based' => '🏷️ Basada en Categoría',
                        'sentiment_based' => '😊 Basada en Sentimiento',
                        'trending_based' => '🔥 Basada en Trending',
                        'custom_based' => '⚙️ Basada en Criterios Personalizados',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('content_category')
                    ->label('Categoría')
                    ->colors([
                        'danger' => 'politics',
                        'success' => 'technology',
                        'warning' => 'entertainment',
                        'info' => 'sports',
                        'primary' => 'business',
                        'secondary' => 'health',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'politics' => '🏛️ Política',
                        'technology' => '💻 Tecnología',
                        'entertainment' => '🎬 Entretenimiento',
                        'sports' => '⚽ Deportes',
                        'business' => '💼 Negocios',
                        'health' => '🏥 Salud',
                        'science' => '🔬 Ciencia',
                        'environment' => '🌍 Medio Ambiente',
                        'education' => '🎓 Educación',
                        'culture' => '🎨 Cultura',
                        'social_issues' => '🤝 Temas Sociales',
                        'crime' => '🚨 Crimen',
                        'weather' => '🌤️ Clima',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'fashion' => '👗 Moda',
                        'automotive' => '🚗 Automóviles',
                        'finance' => '💰 Finanzas',
                        'real_estate' => '🏠 Bienes Raíces',
                        'energy' => '⚡ Energía',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('aggregation_method')
                    ->label('Método')
                    ->colors([
                        'success' => 'automatic',
                        'warning' => 'manual',
                        'info' => 'hybrid',
                        'primary' => 'ai_powered',
                        'danger' => 'algorithmic',
                        'secondary' => 'curated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'automatic' => '🤖 Automática',
                        'manual' => '👤 Manual',
                        'hybrid' => '🔄 Híbrida',
                        'ai_powered' => '🧠 IA',
                        'algorithmic' => '📊 Algorítmica',
                        'curated' => '✍️ Curada',
                        'crowdsourced' => '👥 Crowdsourcing',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('update_frequency')
                    ->label('Frecuencia')
                    ->colors([
                        'danger' => 'real_time',
                        'warning' => 'hourly',
                        'success' => 'daily',
                        'info' => 'weekly',
                        'primary' => 'monthly',
                        'secondary' => 'on_demand',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'real_time' => '⚡ Tiempo Real',
                        'hourly' => '🕐 Cada Hora',
                        'daily' => '📅 Diario',
                        'weekly' => '📅 Semanal',
                        'monthly' => '📅 Mensual',
                        'on_demand' => '🎯 Bajo Demanda',
                        'event_triggered' => '🚨 Por Evento',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('min_articles_count')
                    ->label('Mín Artículos')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('max_articles_count')
                    ->label('Máx Artículos')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('multilingual_support')
                    ->label('Multilingüe')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('use_cache')
                    ->label('Caché')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'info' => 'testing',
                        'warning' => 'maintenance',
                        'primary' => 'error',
                        'secondary' => 'paused',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'testing' => '🧪 En Pruebas',
                        'maintenance' => '🔧 En Mantenimiento',
                        'error' => '🚨 Con Error',
                        'paused' => '⏸️ Pausada',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificada')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Pública')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('quality_rating')
                    ->label('Calidad')
                    ->colors([
                        'success' => 'excellent',
                        'info' => 'very_good',
                        'warning' => 'good',
                        'danger' => 'fair',
                        'secondary' => 'poor',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'excellent' => '🟢 Excelente',
                        'very_good' => '🟢 Muy Buena',
                        'good' => '🟡 Buena',
                        'fair' => '🟠 Regular',
                        'poor' => '🔴 Pobre',
                        'not_rated' => '⚫ No Evaluada',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('error_count')
                    ->label('Errores')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'success',
                        $state <= 5 => 'warning',
                        $state <= 20 => 'danger',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('aggregation_type')
                    ->options([
                        'topic_based' => '📋 Basada en Tema',
                        'source_based' => '📰 Basada en Fuente',
                        'time_based' => '⏰ Basada en Tiempo',
                        'geographic_based' => '🌍 Basada en Geografía',
                        'category_based' => '🏷️ Basada en Categoría',
                        'sentiment_based' => '😊 Basada en Sentimiento',
                        'trending_based' => '🔥 Basada en Trending',
                        'custom_based' => '⚙️ Basada en Criterios Personalizados',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Agregación'),
                
                Tables\Filters\SelectFilter::make('content_category')
                    ->options([
                        'politics' => '🏛️ Política',
                        'technology' => '💻 Tecnología',
                        'entertainment' => '🎬 Entretenimiento',
                        'sports' => '⚽ Deportes',
                        'business' => '💼 Negocios',
                        'health' => '🏥 Salud',
                        'science' => '🔬 Ciencia',
                        'environment' => '🌍 Medio Ambiente',
                        'education' => '🎓 Educación',
                        'culture' => '🎨 Cultura',
                        'social_issues' => '🤝 Temas Sociales',
                        'crime' => '🚨 Crimen',
                        'weather' => '🌤️ Clima',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'fashion' => '👗 Moda',
                        'automotive' => '🚗 Automóviles',
                        'finance' => '💰 Finanzas',
                        'real_estate' => '🏠 Bienes Raíces',
                        'energy' => '⚡ Energía',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría de Contenido'),
                
                Tables\Filters\SelectFilter::make('aggregation_method')
                    ->options([
                        'automatic' => '🤖 Automática',
                        'manual' => '👤 Manual',
                        'hybrid' => '🔄 Híbrida',
                        'ai_powered' => '🧠 IA',
                        'algorithmic' => '📊 Algorítmica',
                        'curated' => '✍️ Curada',
                        'crowdsourced' => '👥 Crowdsourcing',
                        'other' => '❓ Otro',
                    ])
                    ->label('Método de Agregación'),
                
                Tables\Filters\SelectFilter::make('update_frequency')
                    ->options([
                        'real_time' => '⚡ Tiempo Real',
                        'hourly' => '🕐 Cada Hora',
                        'daily' => '📅 Diario',
                        'weekly' => '📅 Semanal',
                        'monthly' => '📅 Mensual',
                        'on_demand' => '🎯 Bajo Demanda',
                        'event_triggered' => '🚨 Por Evento',
                        'other' => '❓ Otro',
                    ])
                    ->label('Frecuencia de Actualización'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'testing' => '🧪 En Pruebas',
                        'maintenance' => '🔧 En Mantenimiento',
                        'error' => '🚨 Con Error',
                        'paused' => '⏸️ Pausada',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('public_only')
                    ->label('Solo Públicas')
                    ->query(fn (Builder $query): Builder => $query->where('is_public', true)),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('error_free')
                    ->label('Sin Errores')
                    ->query(fn (Builder $query): Builder => $query->where('error_count', 0)),
                
                Tables\Filters\Filter::make('real_time_updates')
                    ->label('Actualizaciones en Tiempo Real')
                    ->query(fn (Builder $query): Builder => $query->where('update_frequency', 'real_time')),
                
                Tables\Filters\Filter::make('ai_powered')
                    ->label('Con IA')
                    ->query(fn (Builder $query): Builder => $query->where('aggregation_method', 'ai_powered')),
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
                    ->label(fn ($record): string => $record->is_featured ? 'Quitar Destacada' : 'Destacar')
                    ->icon(fn ($record): string => $record->is_featured ? 'fas-star' : 'far-star')
                    ->action(function ($record): void {
                        $record->update(['is_featured' => !$record->is_featured]);
                    })
                    ->color(fn ($record): string => $record->is_featured ? 'warning' : 'success'),
                
                Tables\Actions\Action::make('mark_verified')
                    ->label('Marcar como Verificada')
                    ->icon('fas-check-circle')
                    ->action(function ($record): void {
                        $record->update(['is_verified' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('toggle_public')
                    ->label(fn ($record): string => $record->is_public ? 'Hacer Privada' : 'Hacer Pública')
                    ->icon(fn ($record): string => $record->is_public ? 'fas-eye-slash' : 'fas-eye')
                    ->action(function ($record): void {
                        $record->update(['is_public' => !$record->is_public]);
                    })
                    ->color(fn ($record): string => $record->is_public ? 'warning' : 'success'),
                
                Tables\Actions\Action::make('activate_aggregation')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_aggregation')
                    ->label('Desactivar')
                    ->icon('fas-pause')
                    ->action(function ($record): void {
                        $record->update(['status' => 'inactive']);
                    })
                    ->visible(fn ($record): bool => $record->status === 'active')
                    ->color('warning'),
                
                Tables\Actions\Action::make('test_aggregation')
                    ->label('Probar')
                    ->icon('fas-vial')
                    ->action(function ($record): void {
                        // Aquí se implementaría la lógica de prueba
                    })
                    ->color('info'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Marcar como Destacadas')
                        ->icon('fas-star')
                        ->action(function ($records): void {
                            $records->each->update(['is_featured' => true]);
                        })
                        ->color('warning'),
                    
                    Tables\Actions\BulkAction::make('mark_verified')
                        ->label('Marcar como Verificadas')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_verified' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('activate_all')
                        ->label('Activar Todas')
                        ->icon('fas-play')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'active']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('deactivate_all')
                        ->label('Desactivar Todas')
                        ->icon('fas-pause')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'inactive']);
                        })
                        ->color('warning'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListNewsAggregations::route('/'),
            'create' => Pages\CreateNewsAggregation::route('/create'),
            'view' => Pages\ViewNewsAggregation::route('/{record}'),
            'edit' => Pages\EditNewsAggregation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
