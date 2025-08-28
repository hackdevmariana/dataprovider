<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferComparisonResource\Pages;
use App\Filament\Resources\OfferComparisonResource\RelationManagers;
use App\Models\OfferComparison;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferComparisonResource extends Resource
{
    protected static ?string $model = OfferComparison::class;

    protected static ?string $navigationIcon = 'fas-balance-scale';

    protected static ?string $navigationGroup = 'Energía y Precios';

    protected static ?string $navigationLabel = 'Comparaciones de Ofertas';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Comparación de Ofertas';

    protected static ?string $pluralModelLabel = 'Comparaciones de Ofertas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('comparison_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Comparación')
                            ->placeholder('Nombre descriptivo de la comparación...'),
                        
                        Forms\Components\TextInput::make('comparison_code')
                            ->maxLength(100)
                            ->label('Código de Comparación')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción de la comparación de ofertas...'),
                        
                        Forms\Components\Select::make('comparison_type')
                            ->options([
                                'energy_plans' => '⚡ Planes de Energía',
                                'gas_plans' => '🔥 Planes de Gas',
                                'dual_fuel' => '🔄 Combustible Dual',
                                'renewable_energy' => '🌱 Energía Renovable',
                                'business_plans' => '💼 Planes Empresariales',
                                'residential_plans' => '🏠 Planes Residenciales',
                                'industrial_plans' => '🏭 Planes Industriales',
                                'commercial_plans' => '🏪 Planes Comerciales',
                                'prepaid_plans' => '💳 Planes Prepago',
                                'postpaid_plans' => '📋 Planes Postpago',
                                'fixed_rate' => '📊 Tarifa Fija',
                                'variable_rate' => '📈 Tarifa Variable',
                                'time_of_use' => '⏰ Uso por Tiempo',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Comparación'),
                        
                        Forms\Components\Select::make('energy_source')
                            ->options([
                                'electricity' => '⚡ Electricidad',
                                'gas' => '🔥 Gas Natural',
                                'lpg' => '🛢️ Gas Licuado',
                                'oil' => '🛢️ Petróleo',
                                'coal' => '⛏️ Carbón',
                                'biomass' => '🌱 Biomasa',
                                'solar' => '☀️ Solar',
                                'wind' => '💨 Eólico',
                                'hydro' => '💧 Hidroeléctrico',
                                'nuclear' => '☢️ Nuclear',
                                'geothermal' => '🌋 Geotérmico',
                                'hydrogen' => '⚗️ Hidrógeno',
                                'hybrid' => '🔄 Híbrido',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Fuente de Energía'),
                        
                        Forms\Components\Select::make('customer_type')
                            ->options([
                                'residential' => '🏠 Residencial',
                                'commercial' => '🏪 Comercial',
                                'industrial' => '🏭 Industrial',
                                'agricultural' => '🌾 Agrícola',
                                'government' => '🏛️ Gubernamental',
                                'non_profit' => '🤝 Sin Fines de Lucro',
                                'educational' => '🎓 Educativo',
                                'healthcare' => '🏥 Salud',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Cliente'),
                    ])->columns(2),

                Forms\Components\Section::make('Período y Cobertura')
                    ->schema([
                        Forms\Components\DatePicker::make('comparison_date')
                            ->required()
                            ->label('Fecha de Comparación')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha cuando se realizó la comparación'),
                        
                        Forms\Components\DatePicker::make('valid_from')
                            ->required()
                            ->label('Válido Desde')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha desde cuando es válida la comparación'),
                        
                        Forms\Components\DatePicker::make('valid_until')
                            ->required()
                            ->label('Válido Hasta')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha hasta cuando es válida la comparación'),
                        
                        Forms\Components\TextInput::make('comparison_period')
                            ->maxLength(100)
                            ->label('Período de Comparación')
                            ->placeholder('1 mes, 3 meses, 1 año...'),
                        
                        Forms\Components\Select::make('geographic_coverage')
                            ->options([
                                'national' => '🏳️ Nacional',
                                'regional' => '🏘️ Regional',
                                'state_province' => '🏛️ Estado/Provincia',
                                'city' => '🏙️ Ciudad',
                                'local' => '🏠 Local',
                                'specific_area' => '📍 Área Específica',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Cobertura Geográfica'),
                        
                        Forms\Components\TextInput::make('specific_locations')
                            ->maxLength(500)
                            ->label('Ubicaciones Específicas')
                            ->placeholder('Ciudades, regiones o áreas específicas...'),
                    ])->columns(2),

                Forms\Components\Section::make('Criterios de Comparación')
                    ->schema([
                        Forms\Components\Textarea::make('comparison_criteria')
                            ->required()
                            ->maxLength(1000)
                            ->label('Criterios de Comparación')
                            ->rows(3)
                            ->placeholder('Criterios utilizados para comparar las ofertas...'),
                        
                        Forms\Components\KeyValue::make('key_metrics')
                            ->label('Métricas Clave')
                            ->keyLabel('Métrica')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Métrica'),
                        
                        Forms\Components\Textarea::make('weighting_system')
                            ->maxLength(500)
                            ->label('Sistema de Ponderación')
                            ->rows(2)
                            ->placeholder('Cómo se ponderan los diferentes criterios...'),
                        
                        Forms\Components\Toggle::make('includes_price')
                            ->label('Incluye Precio')
                            ->default(true)
                            ->helperText('La comparación incluye análisis de precios'),
                        
                        Forms\Components\Toggle::make('includes_features')
                            ->label('Incluye Características')
                            ->default(true)
                            ->helperText('La comparación incluye características de las ofertas'),
                        
                        Forms\Components\Toggle::make('includes_quality')
                            ->label('Incluye Calidad')
                            ->default(true)
                            ->helperText('La comparación incluye calidad del servicio'),
                        
                        Forms\Components\Toggle::make('includes_customer_service')
                            ->label('Incluye Servicio al Cliente')
                            ->default(false)
                            ->helperText('La comparación incluye servicio al cliente'),
                        
                        Forms\Components\Toggle::make('includes_contract_terms')
                            ->label('Incluye Términos del Contrato')
                            ->default(false)
                            ->helperText('La comparación incluye términos contractuales'),
                    ])->columns(2),

                Forms\Components\Section::make('Ofertas Comparadas')
                    ->schema([
                        Forms\Components\TextInput::make('number_of_offers')
                            ->numeric()
                            ->label('Número de Ofertas')
                            ->helperText('Número total de ofertas comparadas'),
                        
                        Forms\Components\TextInput::make('providers_included')
                            ->maxLength(500)
                            ->label('Proveedores Incluidos')
                            ->placeholder('Proveedores incluidos en la comparación...'),
                        
                        Forms\Components\KeyValue::make('offer_details')
                            ->label('Detalles de las Ofertas')
                            ->keyLabel('Oferta')
                            ->valueLabel('Características')
                            ->addActionLabel('Agregar Oferta'),
                        
                        Forms\Components\Textarea::make('exclusion_criteria')
                            ->maxLength(500)
                            ->label('Criterios de Exclusión')
                            ->rows(2)
                            ->placeholder('Ofertas excluidas y por qué...'),
                        
                        Forms\Components\Toggle::make('includes_new_providers')
                            ->label('Incluye Nuevos Proveedores')
                            ->default(false)
                            ->helperText('La comparación incluye proveedores nuevos'),
                        
                        Forms\Components\Toggle::make('includes_established_providers')
                            ->label('Incluye Proveedores Establecidos')
                            ->default(true)
                            ->helperText('La comparación incluye proveedores establecidos'),
                    ])->columns(2),

                Forms\Components\Section::make('Análisis y Resultados')
                    ->schema([
                        Forms\Components\Textarea::make('analysis_summary')
                            ->maxLength(1000)
                            ->label('Resumen del Análisis')
                            ->rows(3)
                            ->placeholder('Resumen de los hallazgos principales...'),
                        
                        Forms\Components\Textarea::make('key_findings')
                            ->maxLength(1000)
                            ->label('Hallazgos Clave')
                            ->rows(3)
                            ->placeholder('Hallazgos más importantes de la comparación...'),
                        
                        Forms\Components\Textarea::make('price_analysis')
                            ->maxLength(500)
                            ->label('Análisis de Precios')
                            ->rows(2)
                            ->placeholder('Análisis detallado de precios...'),
                        
                        Forms\Components\Textarea::make('feature_analysis')
                            ->maxLength(500)
                            ->label('Análisis de Características')
                            ->rows(2)
                            ->placeholder('Análisis de características de las ofertas...'),
                        
                        Forms\Components\Textarea::make('quality_analysis')
                            ->maxLength(500)
                            ->label('Análisis de Calidad')
                            ->rows(2)
                            ->placeholder('Análisis de calidad del servicio...'),
                        
                        Forms\Components\KeyValue::make('recommendations')
                            ->label('Recomendaciones')
                            ->keyLabel('Categoría')
                            ->valueLabel('Recomendación')
                            ->addActionLabel('Agregar Recomendación'),
                    ])->columns(1),

                Forms\Components\Section::make('Visualización y Presentación')
                    ->schema([
                        Forms\Components\Select::make('presentation_format')
                            ->options([
                                'table' => '📊 Tabla',
                                'chart' => '📈 Gráfico',
                                'infographic' => '🖼️ Infografía',
                                'report' => '📋 Reporte',
                                'dashboard' => '🎛️ Dashboard',
                                'interactive' => '🖱️ Interactivo',
                                'pdf' => '📄 PDF',
                                'web' => '🌐 Web',
                                'mobile' => '📱 Móvil',
                                'other' => '❓ Otro',
                            ])
                            ->label('Formato de Presentación'),
                        
                        Forms\Components\Toggle::make('has_charts')
                            ->label('Incluye Gráficos')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_tables')
                            ->label('Incluye Tablas')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('has_rankings')
                            ->label('Incluye Rankings')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_ratings')
                            ->label('Incluye Calificaciones')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_interactive')
                            ->label('Es Interactivo')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_responsive')
                            ->label('Es Responsivo')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('color_scheme')
                            ->maxLength(100)
                            ->label('Esquema de Colores')
                            ->placeholder('Colores utilizados en la presentación...'),
                    ])->columns(2),

                Forms\Components\Section::make('Uso y Aplicación')
                    ->schema([
                        Forms\Components\Select::make('primary_audience')
                            ->options([
                                'consumers' => '👥 Consumidores',
                                'businesses' => '💼 Empresas',
                                'energy_consultants' => '🔍 Consultores Energéticos',
                                'regulators' => '🏛️ Reguladores',
                                'researchers' => '🔬 Investigadores',
                                'policy_makers' => '📋 Políticos',
                                'energy_providers' => '⚡ Proveedores de Energía',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Audiencia Principal'),
                        
                        Forms\Components\TextInput::make('use_cases')
                            ->maxLength(500)
                            ->label('Casos de Uso')
                            ->placeholder('Cómo se utiliza la comparación...'),
                        
                        Forms\Components\Toggle::make('is_educational')
                            ->label('Es Educativo')
                            ->default(false)
                            ->helperText('La comparación tiene valor educativo'),
                        
                        Forms\Components\Toggle::make('is_decision_support')
                            ->label('Apoya Decisiones')
                            ->default(true)
                            ->helperText('Ayuda a tomar decisiones informadas'),
                        
                        Forms\Components\Toggle::make('is_public_awareness')
                            ->label('Conciencia Pública')
                            ->default(false)
                            ->helperText('Aumenta la conciencia pública'),
                        
                        Forms\Components\TextInput::make('update_frequency')
                            ->maxLength(100)
                            ->label('Frecuencia de Actualización')
                            ->placeholder('Mensual, trimestral, anual...'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Calidad')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => '📝 Borrador',
                                'active' => '✅ Activa',
                                'expired' => '❌ Expirada',
                                'under_review' => '👀 En Revisión',
                                'archived' => '📦 Archivada',
                                'deprecated' => '⚠️ Deprecada',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('draft')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Comparación importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('La comparación ha sido verificada'),
                        
                        Forms\Components\Toggle::make('is_peer_reviewed')
                            ->label('Revisada por Pares')
                            ->default(false)
                            ->helperText('Revisada por expertos del sector'),
                        
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
                        
                        Forms\Components\TextInput::make('reviewer')
                            ->maxLength(255)
                            ->label('Revisor')
                            ->placeholder('Persona que revisó la comparación...'),
                        
                        Forms\Components\DatePicker::make('review_date')
                            ->label('Fecha de Revisión')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas adicionales o comentarios...'),
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
                
                Tables\Columns\TextColumn::make('comparison_name')
                    ->label('Comparación')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('comparison_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'energy_plans',
                        'success' => 'gas_plans',
                        'warning' => 'dual_fuel',
                        'info' => 'renewable_energy',
                        'danger' => 'business_plans',
                        'secondary' => 'residential_plans',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'energy_plans' => '⚡ Planes de Energía',
                        'gas_plans' => '🔥 Planes de Gas',
                        'dual_fuel' => '🔄 Combustible Dual',
                        'renewable_energy' => '🌱 Energía Renovable',
                        'business_plans' => '💼 Planes Empresariales',
                        'residential_plans' => '🏠 Planes Residenciales',
                        'industrial_plans' => '🏭 Planes Industriales',
                        'commercial_plans' => '🏪 Planes Comerciales',
                        'prepaid_plans' => '💳 Planes Prepago',
                        'postpaid_plans' => '📋 Planes Postpago',
                        'fixed_rate' => '📊 Tarifa Fija',
                        'variable_rate' => '📈 Tarifa Variable',
                        'time_of_use' => '⏰ Uso por Tiempo',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('energy_source')
                    ->label('Fuente')
                    ->colors([
                        'warning' => 'electricity',
                        'info' => 'gas',
                        'success' => 'solar',
                        'primary' => 'wind',
                        'danger' => 'nuclear',
                        'secondary' => 'biomass',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'electricity' => '⚡ Electricidad',
                        'gas' => '🔥 Gas Natural',
                        'lpg' => '🛢️ Gas Licuado',
                        'oil' => '🛢️ Petróleo',
                        'coal' => '⛏️ Carbón',
                        'biomass' => '🌱 Biomasa',
                        'solar' => '☀️ Solar',
                        'wind' => '💨 Eólico',
                        'hydro' => '💧 Hidroeléctrico',
                        'nuclear' => '☢️ Nuclear',
                        'geothermal' => '🌋 Geotérmico',
                        'hydrogen' => '⚗️ Hidrógeno',
                        'hybrid' => '🔄 Híbrido',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('customer_type')
                    ->label('Cliente')
                    ->colors([
                        'success' => 'residential',
                        'warning' => 'commercial',
                        'danger' => 'industrial',
                        'info' => 'agricultural',
                        'primary' => 'government',
                        'secondary' => 'non_profit',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'residential' => '🏠 Residencial',
                        'commercial' => '🏪 Comercial',
                        'industrial' => '🏭 Industrial',
                        'agricultural' => '🌾 Agrícola',
                        'government' => '🏛️ Gubernamental',
                        'non_profit' => '🤝 Sin Fines de Lucro',
                        'educational' => '🎓 Educativo',
                        'healthcare' => '🏥 Salud',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('comparison_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('valid_from')
                    ->label('Válido Desde')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Válido Hasta')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->valid_until && $record->valid_until->isPast() ? 'danger' : 
                        ($record->valid_until && $record->valid_until->diffInDays(now()) <= 30 ? 'warning' : 'success')
                    ),
                
                Tables\Columns\TextColumn::make('number_of_offers')
                    ->label('Ofertas')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 10 => 'success',
                        $state >= 5 => 'info',
                        $state >= 3 => 'warning',
                        $state >= 1 => 'danger',
                        default => 'secondary',
                    }),
                
                Tables\Columns\BadgeColumn::make('geographic_coverage')
                    ->label('Cobertura')
                    ->colors([
                        'danger' => 'national',
                        'warning' => 'regional',
                        'info' => 'state_province',
                        'success' => 'city',
                        'primary' => 'local',
                        'secondary' => 'specific_area',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'national' => '🏳️ Nacional',
                        'regional' => '🏘️ Regional',
                        'state_province' => '🏛️ Estado/Provincia',
                        'city' => '🏙️ Ciudad',
                        'local' => '🏠 Local',
                        'specific_area' => '📍 Área Específica',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'danger' => 'expired',
                        'info' => 'under_review',
                        'dark' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activa',
                        'expired' => '❌ Expirada',
                        'under_review' => '👀 En Revisión',
                        'archived' => '📦 Archivada',
                        'deprecated' => '⚠️ Deprecada',
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
                
                Tables\Columns\IconColumn::make('is_peer_reviewed')
                    ->label('Revisada por Pares')
                    ->boolean()
                    ->trueColor('primary')
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
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('comparison_type')
                    ->options([
                        'energy_plans' => '⚡ Planes de Energía',
                        'gas_plans' => '🔥 Planes de Gas',
                        'dual_fuel' => '🔄 Combustible Dual',
                        'renewable_energy' => '🌱 Energía Renovable',
                        'business_plans' => '💼 Planes Empresariales',
                        'residential_plans' => '🏠 Planes Residenciales',
                        'industrial_plans' => '🏭 Planes Industriales',
                        'commercial_plans' => '🏪 Planes Comerciales',
                        'prepaid_plans' => '💳 Planes Prepago',
                        'postpaid_plans' => '📋 Planes Postpago',
                        'fixed_rate' => '📊 Tarifa Fija',
                        'variable_rate' => '📈 Tarifa Variable',
                        'time_of_use' => '⏰ Uso por Tiempo',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Comparación'),
                
                Tables\Filters\SelectFilter::make('energy_source')
                    ->options([
                        'electricity' => '⚡ Electricidad',
                        'gas' => '🔥 Gas Natural',
                        'lpg' => '🛢️ Gas Licuado',
                        'oil' => '🛢️ Petróleo',
                        'coal' => '⛏️ Carbón',
                        'biomass' => '🌱 Biomasa',
                        'solar' => '☀️ Solar',
                        'wind' => '💨 Eólico',
                        'hydro' => '💧 Hidroeléctrico',
                        'nuclear' => '☢️ Nuclear',
                        'geothermal' => '🌋 Geotérmico',
                        'hydrogen' => '⚗️ Hidrógeno',
                        'hybrid' => '🔄 Híbrido',
                        'other' => '❓ Otro',
                    ])
                    ->label('Fuente de Energía'),
                
                Tables\Filters\SelectFilter::make('customer_type')
                    ->options([
                        'residential' => '🏠 Residencial',
                        'commercial' => '🏪 Comercial',
                        'industrial' => '🏭 Industrial',
                        'agricultural' => '🌾 Agrícola',
                        'government' => '🏛️ Gubernamental',
                        'non_profit' => '🤝 Sin Fines de Lucro',
                        'educational' => '🎓 Educativo',
                        'healthcare' => '🏥 Salud',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Cliente'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activa',
                        'expired' => '❌ Expirada',
                        'under_review' => '👀 En Revisión',
                        'archived' => '📦 Archivada',
                        'deprecated' => '⚠️ Deprecada',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('peer_reviewed_only')
                    ->label('Solo Revisadas por Pares')
                    ->query(fn (Builder $query): Builder => $query->where('is_peer_reviewed', true)),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('expired_only')
                    ->label('Solo Expiradas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'expired')),
                
                Tables\Filters\Filter::make('many_offers')
                    ->label('Muchas Ofertas (5+)')
                    ->query(fn (Builder $query): Builder => $query->where('number_of_offers', '>=', 5)),
                
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiran Pronto (30 días)')
                    ->query(fn (Builder $query): Builder => $query->where('valid_until', '<=', now()->addDays(30))->where('status', 'active')),
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
                
                Tables\Actions\Action::make('mark_peer_reviewed')
                    ->label('Marcar como Revisada por Pares')
                    ->icon('fas-users')
                    ->action(function ($record): void {
                        $record->update(['is_peer_reviewed' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_peer_reviewed)
                    ->color('primary'),
                
                Tables\Actions\Action::make('activate_comparison')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('archive_comparison')
                    ->label('Archivar')
                    ->icon('fas-archive')
                    ->action(function ($record): void {
                        $record->update(['status' => 'archived']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'archived')
                    ->color('secondary'),
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
                    
                    Tables\Actions\BulkAction::make('mark_peer_reviewed')
                        ->label('Marcar como Revisadas por Pares')
                        ->icon('fas-users')
                        ->action(function ($records): void {
                            $records->each->update(['is_peer_reviewed' => true]);
                        })
                        ->color('primary'),
                    
                    Tables\Actions\BulkAction::make('activate_all')
                        ->label('Activar Todas')
                        ->icon('fas-play')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'active']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('archive_all')
                        ->label('Archivar Todas')
                        ->icon('fas-archive')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'archived']);
                        })
                        ->color('secondary'),
                ]),
            ])
            ->defaultSort('comparison_date', 'desc')
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
            'index' => Pages\ListOfferComparisons::route('/'),
            'create' => Pages\CreateOfferComparison::route('/create'),
            'view' => Pages\ViewOfferComparison::route('/{record}'),
            'edit' => Pages\EditOfferComparison::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
