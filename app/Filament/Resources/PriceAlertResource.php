<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceAlertResource\Pages;
use App\Models\PriceAlert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PriceAlertResource extends Resource
{
    protected static ?string $model = PriceAlert::class;

    protected static ?string $navigationIcon = 'fas-bell';

    protected static ?string $navigationGroup = 'Energía y Precios';

    protected static ?string $navigationLabel = 'Alertas de Precios';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Alerta de Precio';

    protected static ?string $pluralModelLabel = 'Alertas de Precios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('alert_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Alerta')
                            ->placeholder('Nombre descriptivo de la alerta...'),
                        
                        Forms\Components\TextInput::make('alert_code')
                            ->maxLength(100)
                            ->label('Código de Alerta')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción de la alerta...'),
                        
                        Forms\Components\Select::make('alert_type')
                            ->options([
                                'price_increase' => '📈 Aumento de Precio',
                                'price_decrease' => '📉 Disminución de Precio',
                                'price_threshold' => '🎯 Umbral de Precio',
                                'price_volatility' => '📊 Volatilidad de Precio',
                                'price_spike' => '🚀 Pico de Precio',
                                'price_drop' => '💥 Caída de Precio',
                                'price_stability' => '📊 Estabilidad de Precio',
                                'price_trend' => '📈 Tendencia de Precio',
                                'price_comparison' => '⚖️ Comparación de Precios',
                                'price_forecast' => '🔮 Pronóstico de Precio',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Alerta'),
                        
                        Forms\Components\Select::make('energy_type')
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
                            ->label('Tipo de Energía'),
                        
                        Forms\Components\Select::make('priority_level')
                            ->options([
                                'low' => '🟢 Baja',
                                'medium' => '🟡 Media',
                                'high' => '🟠 Alta',
                                'critical' => '🔴 Crítica',
                                'urgent' => '🚨 Urgente',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('medium')
                            ->label('Nivel de Prioridad'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '✅ Activa',
                                'inactive' => '❌ Inactiva',
                                'triggered' => '🚨 Activada',
                                'acknowledged' => '👁️ Reconocida',
                                'resolved' => '✅ Resuelta',
                                'expired' => '⏰ Expirada',
                                'cancelled' => '❌ Cancelada',
                                'suspended' => '⏸️ Suspendida',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('active')
                            ->label('Estado'),
                    ])->columns(2),

                Forms\Components\Section::make('Condiciones de Activación')
                    ->schema([
                        Forms\Components\Select::make('trigger_condition')
                            ->options([
                                'above_threshold' => '📈 Por Encima del Umbral',
                                'below_threshold' => '📉 Por Debajo del Umbral',
                                'crosses_threshold' => '🔄 Cruza el Umbral',
                                'percentage_change' => '📊 Cambio Porcentual',
                                'absolute_change' => '📊 Cambio Absoluto',
                                'time_based' => '⏰ Basado en Tiempo',
                                'volume_based' => '📦 Basado en Volumen',
                                'market_based' => '🏪 Basado en Mercado',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Condición de Activación'),
                        
                        Forms\Components\TextInput::make('threshold_value')
                            ->numeric()
                            ->required()
                            ->label('Valor del Umbral')
                            ->placeholder('Valor que activa la alerta...'),
                        
                        Forms\Components\Select::make('threshold_unit')
                            ->options([
                                'EUR' => '€ EUR',
                                'USD' => '$ USD',
                                'GBP' => '£ GBP',
                                'JPY' => '¥ JPY',
                                'CHF' => 'CHF',
                                'CAD' => 'C$ CAD',
                                'AUD' => 'A$ AUD',
                                'percentage' => '% Porcentaje',
                                'other' => 'Otro',
                            ])
                            ->required()
                            ->default('EUR')
                            ->label('Unidad del Umbral'),
                        
                        Forms\Components\TextInput::make('percentage_change')
                            ->numeric()
                            ->label('Cambio Porcentual (%)')
                            ->placeholder('Cambio porcentual requerido...')
                            ->visible(fn (Forms\Get $get): bool => $get('trigger_condition') === 'percentage_change'),
                        
                        Forms\Components\TextInput::make('absolute_change')
                            ->numeric()
                            ->label('Cambio Absoluto')
                            ->placeholder('Cambio absoluto requerido...')
                            ->visible(fn (Forms\Get $get): bool => $get('trigger_condition') === 'absolute_change'),
                        
                        Forms\Components\TextInput::make('time_window_hours')
                            ->numeric()
                            ->label('Ventana de Tiempo (horas)')
                            ->placeholder('Ventana de tiempo para la alerta...'),
                        
                        Forms\Components\Toggle::make('requires_confirmation')
                            ->label('Requiere Confirmación')
                            ->default(false)
                            ->helperText('La alerta requiere confirmación manual'),
                        
                        Forms\Components\Toggle::make('auto_resolve')
                            ->label('Auto-resolución')
                            ->default(false)
                            ->helperText('La alerta se resuelve automáticamente'),
                        
                        Forms\Components\TextInput::make('auto_resolve_delay_hours')
                            ->numeric()
                            ->label('Retraso de Auto-resolución (horas)')
                            ->placeholder('Retraso antes de auto-resolver...')
                            ->visible(fn (Forms\Get $get): bool => $get('auto_resolve')),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración de Notificaciones')
                    ->schema([
                        Forms\Components\Toggle::make('email_notifications')
                            ->label('Notificaciones por Email')
                            ->default(true)
                            ->helperText('Enviar notificaciones por email'),
                        
                        Forms\Components\TextInput::make('email_recipients')
                            ->maxLength(500)
                            ->label('Destinatarios de Email')
                            ->placeholder('emails separados por comas...')
                            ->visible(fn (Forms\Get $get): bool => $get('email_notifications')),
                        
                        Forms\Components\Toggle::make('sms_notifications')
                            ->label('Notificaciones por SMS')
                            ->default(false)
                            ->helperText('Enviar notificaciones por SMS'),
                        
                        Forms\Components\TextInput::make('sms_recipients')
                            ->maxLength(500)
                            ->label('Destinatarios de SMS')
                            ->placeholder('números separados por comas...')
                            ->visible(fn (Forms\Get $get): bool => $get('sms_notifications')),
                        
                        Forms\Components\Toggle::make('push_notifications')
                            ->label('Notificaciones Push')
                            ->default(false)
                            ->helperText('Enviar notificaciones push'),
                        
                        Forms\Components\Toggle::make('webhook_notifications')
                            ->label('Notificaciones Webhook')
                            ->default(false)
                            ->helperText('Enviar notificaciones por webhook'),
                        
                        Forms\Components\TextInput::make('webhook_url')
                            ->url()
                            ->maxLength(500)
                            ->label('URL del Webhook')
                            ->placeholder('https://...')
                            ->visible(fn (Forms\Get $get): bool => $get('webhook_notifications')),
                        
                        Forms\Components\Select::make('notification_frequency')
                            ->options([
                                'immediate' => '⚡ Inmediata',
                                'hourly' => '🕐 Cada Hora',
                                'daily' => '📅 Diaria',
                                'weekly' => '📅 Semanal',
                                'custom' => '⚙️ Personalizada',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('immediate')
                            ->label('Frecuencia de Notificación'),
                        
                        Forms\Components\TextInput::make('custom_frequency_hours')
                            ->numeric()
                            ->label('Frecuencia Personalizada (horas)')
                            ->placeholder('Cada cuántas horas...')
                            ->visible(fn (Forms\Get $get): bool => $get('notification_frequency') === 'custom'),
                        
                        Forms\Components\Toggle::make('quiet_hours_enabled')
                            ->label('Horas Silenciosas')
                            ->default(false)
                            ->helperText('No enviar notificaciones en ciertas horas'),
                        
                        Forms\Components\TimePicker::make('quiet_hours_start')
                            ->label('Inicio de Horas Silenciosas')
                            ->displayFormat('H:i')
                            ->visible(fn (Forms\Get $get): bool => $get('quiet_hours_enabled')),
                        
                        Forms\Components\TimePicker::make('quiet_hours_end')
                            ->label('Fin de Horas Silenciosas')
                            ->displayFormat('H:i')
                            ->visible(fn (Forms\Get $get): bool => $get('quiet_hours_enabled')),
                    ])->columns(2),

                Forms\Components\Section::make('Filtros y Alcance')
                    ->schema([
                        Forms\Components\Select::make('geographic_scope')
                            ->options([
                                'global' => '🌍 Global',
                                'national' => '🏳️ Nacional',
                                'regional' => '🏘️ Regional',
                                'state_province' => '🏛️ Estado/Provincia',
                                'city' => '🏙️ Ciudad',
                                'local' => '🏠 Local',
                                'specific_area' => '📍 Área Específica',
                                'other' => '❓ Otro',
                            ])
                            ->label('Alcance Geográfico'),
                        
                        Forms\Components\TextInput::make('specific_locations')
                            ->maxLength(500)
                            ->label('Ubicaciones Específicas')
                            ->placeholder('Ubicaciones específicas...'),
                        
                        Forms\Components\Select::make('market_segment')
                            ->options([
                                'residential' => '🏠 Residencial',
                                'commercial' => '🏪 Comercial',
                                'industrial' => '🏭 Industrial',
                                'agricultural' => '🌾 Agrícola',
                                'government' => '🏛️ Gubernamental',
                                'wholesale' => '📦 Mayorista',
                                'retail' => '🏪 Minorista',
                                'other' => '❓ Otro',
                            ])
                            ->label('Segmento de Mercado'),
                        
                        Forms\Components\Select::make('provider_type')
                            ->options([
                                'utility' => '⚡ Empresa de Servicios',
                                'independent' => '🏢 Independiente',
                                'municipal' => '🏛️ Municipal',
                                'cooperative' => '🤝 Cooperativa',
                                'government' => '🏛️ Gubernamental',
                                'other' => '❓ Otro',
                            ])
                            ->label('Tipo de Proveedor'),
                        
                        Forms\Components\TextInput::make('specific_providers')
                            ->maxLength(500)
                            ->label('Proveedores Específicos')
                            ->placeholder('Proveedores específicos...'),
                        
                        Forms\Components\Toggle::make('include_derivatives')
                            ->label('Incluir Derivados')
                            ->default(false)
                            ->helperText('Incluir productos derivados de energía'),
                        
                        Forms\Components\Toggle::make('include_futures')
                            ->label('Incluir Futuros')
                            ->default(false)
                            ->helperText('Incluir contratos de futuros'),
                        
                        Forms\Components\Toggle::make('include_options')
                            ->label('Incluir Opciones')
                            ->default(false)
                            ->helperText('Incluir contratos de opciones'),
                    ])->columns(2),

                Forms\Components\Section::make('Historial y Seguimiento')
                    ->schema([
                        Forms\Components\TextInput::make('trigger_count')
                            ->numeric()
                            ->label('Contador de Activaciones')
                            ->placeholder('Número de veces activada...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de veces que se ha activado'),
                        
                        Forms\Components\DateTimePicker::make('last_triggered_at')
                            ->label('Última Activación')
                            ->displayFormat('d/m/Y H:i')
                            ->disabled()
                            ->helperText('Cuándo se activó por última vez'),
                        
                        Forms\Components\TextInput::make('last_triggered_price')
                            ->numeric()
                            ->label('Precio de Última Activación')
                            ->placeholder('Precio cuando se activó...')
                            ->disabled()
                            ->helperText('Precio cuando se activó por última vez'),
                        
                        Forms\Components\TextInput::make('acknowledgement_count')
                            ->numeric()
                            ->label('Contador de Reconocimientos')
                            ->placeholder('Número de reconocimientos...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de veces reconocida'),
                        
                        Forms\Components\DateTimePicker::make('last_acknowledged_at')
                            ->label('Último Reconocimiento')
                            ->displayFormat('d/m/Y H:i')
                            ->disabled()
                            ->helperText('Cuándo fue reconocida por última vez'),
                        
                        Forms\Components\TextInput::make('resolution_count')
                            ->numeric()
                            ->label('Contador de Resoluciones')
                            ->placeholder('Número de resoluciones...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de veces resuelta'),
                        
                        Forms\Components\DateTimePicker::make('last_resolved_at')
                            ->label('Última Resolución')
                            ->displayFormat('d/m/Y H:i')
                            ->disabled()
                            ->helperText('Cuándo fue resuelta por última vez'),
                        
                        Forms\Components\TextInput::make('average_response_time_hours')
                            ->numeric()
                            ->label('Tiempo Promedio de Respuesta (horas)')
                            ->placeholder('Tiempo promedio...')
                            ->disabled()
                            ->helperText('Tiempo promedio para responder'),
                        
                        Forms\Components\TextInput::make('total_notifications_sent')
                            ->numeric()
                            ->label('Total de Notificaciones Enviadas')
                            ->placeholder('Total de notificaciones...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Total de notificaciones enviadas'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Avanzada')
                    ->schema([
                        Forms\Components\Toggle::make('is_template')
                            ->label('Es Plantilla')
                            ->default(false)
                            ->helperText('Esta alerta es una plantilla reutilizable'),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Es Pública')
                            ->default(false)
                            ->helperText('La alerta es visible públicamente'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Alerta importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('La alerta ha sido verificada'),
                        
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
                            ->placeholder('Persona que revisó la alerta...'),
                        
                        Forms\Components\DatePicker::make('review_date')
                            ->label('Fecha de Revisión')
                            ->displayFormat('d/m/Y'),
                        
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
                
                Tables\Columns\TextColumn::make('alert_name')
                    ->label('Alerta')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('alert_type')
                    ->label('Tipo')
                    ->colors([
                        'danger' => 'price_increase',
                        'success' => 'price_decrease',
                        'warning' => 'price_threshold',
                        'info' => 'price_volatility',
                        'primary' => 'price_spike',
                        'secondary' => 'price_drop',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'price_increase' => '📈 Aumento de Precio',
                        'price_decrease' => '📉 Disminución de Precio',
                        'price_threshold' => '🎯 Umbral de Precio',
                        'price_volatility' => '📊 Volatilidad de Precio',
                        'price_spike' => '🚀 Pico de Precio',
                        'price_drop' => '💥 Caída de Precio',
                        'price_stability' => '📊 Estabilidad de Precio',
                        'price_trend' => '📈 Tendencia de Precio',
                        'price_comparison' => '⚖️ Comparación de Precios',
                        'price_forecast' => '🔮 Pronóstico de Precio',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('energy_type')
                    ->label('Energía')
                    ->colors([
                        'primary' => 'electricity',
                        'warning' => 'gas',
                        'danger' => 'oil',
                        'success' => 'solar',
                        'info' => 'wind',
                        'secondary' => 'hydro',
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
                
                Tables\Columns\BadgeColumn::make('priority_level')
                    ->label('Prioridad')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                        'primary' => 'critical',
                        'info' => 'urgent',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => '🟢 Baja',
                        'medium' => '🟡 Media',
                        'high' => '🟠 Alta',
                        'critical' => '🔴 Crítica',
                        'urgent' => '🚨 Urgente',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('threshold_value')
                    ->label('Umbral')
                    ->numeric()
                    ->sortable()
                    ->suffix(fn ($record): string => ' ' . ($record->threshold_unit ?? 'EUR')),
                
                Tables\Columns\TextColumn::make('trigger_count')
                    ->label('Activaciones')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'success',
                        $state <= 5 => 'info',
                        $state <= 20 => 'warning',
                        $state <= 50 => 'danger',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('last_triggered_at')
                    ->label('Última Activación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->last_triggered_at && $record->last_triggered_at->diffInHours(now()) <= 24 ? 'danger' : 
                        ($record->last_triggered_at && $record->last_triggered_at->diffInHours(now()) <= 168 ? 'warning' : 'success')
                    ),
                
                Tables\Columns\IconColumn::make('email_notifications')
                    ->label('Email')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('sms_notifications')
                    ->label('SMS')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('push_notifications')
                    ->label('Push')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('requires_confirmation')
                    ->label('Confirma')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('auto_resolve')
                    ->label('Auto-resuelve')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'triggered',
                        'info' => 'acknowledged',
                        'primary' => 'resolved',
                        'secondary' => 'expired',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'triggered' => '🚨 Activada',
                        'acknowledged' => '👁️ Reconocida',
                        'resolved' => '✅ Resuelta',
                        'expired' => '⏰ Expirada',
                        'cancelled' => '❌ Cancelada',
                        'suspended' => '⏸️ Suspendida',
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
                Tables\Filters\SelectFilter::make('alert_type')
                    ->options([
                        'price_increase' => '📈 Aumento de Precio',
                        'price_decrease' => '📉 Disminución de Precio',
                        'price_threshold' => '🎯 Umbral de Precio',
                        'price_volatility' => '📊 Volatilidad de Precio',
                        'price_spike' => '🚀 Pico de Precio',
                        'price_drop' => '💥 Caída de Precio',
                        'price_stability' => '📊 Estabilidad de Precio',
                        'price_trend' => '📈 Tendencia de Precio',
                        'price_comparison' => '⚖️ Comparación de Precios',
                        'price_forecast' => '🔮 Pronóstico de Precio',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Alerta'),
                
                Tables\Filters\SelectFilter::make('energy_type')
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
                    ->label('Tipo de Energía'),
                
                Tables\Filters\SelectFilter::make('priority_level')
                    ->options([
                        'low' => '🟢 Baja',
                        'medium' => '🟡 Media',
                        'high' => '🟠 Alta',
                        'critical' => '🔴 Crítica',
                        'urgent' => '🚨 Urgente',
                        'other' => '❓ Otro',
                    ])
                    ->label('Nivel de Prioridad'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'triggered' => '🚨 Activada',
                        'acknowledged' => '👁️ Reconocida',
                        'resolved' => '✅ Resuelta',
                        'expired' => '⏰ Expirada',
                        'cancelled' => '❌ Cancelada',
                        'suspended' => '⏸️ Suspendida',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('triggered_alerts')
                    ->label('Solo Activadas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'triggered')),
                
                Tables\Filters\Filter::make('high_priority')
                    ->label('Alta Prioridad')
                    ->query(fn (Builder $query): Builder => $query->whereIn('priority_level', ['high', 'critical', 'urgent'])),
                
                Tables\Filters\Filter::make('recently_triggered')
                    ->label('Activadas Recientemente (24h)')
                    ->query(fn (Builder $query): Builder => $query->where('last_triggered_at', '>=', now()->subHours(24))),
                
                Tables\Filters\Filter::make('frequently_triggered')
                    ->label('Frecuentemente Activadas (10+)')
                    ->query(fn (Builder $query): Builder => $query->where('trigger_count', '>=', 10)),
                
                Tables\Filters\Filter::make('email_enabled')
                    ->label('Solo con Email')
                    ->query(fn (Builder $query): Builder => $query->where('email_notifications', true)),
                
                Tables\Filters\Filter::make('sms_enabled')
                    ->label('Solo con SMS')
                    ->query(fn (Builder $query): Builder => $query->where('sms_notifications', true)),
                
                Tables\Filters\Filter::make('requires_confirmation')
                    ->label('Solo que Requieren Confirmación')
                    ->query(fn (Builder $query): Builder => $query->where('requires_confirmation', true)),
                
                Tables\Filters\Filter::make('auto_resolve')
                    ->label('Solo Auto-resolubles')
                    ->query(fn (Builder $query): Builder => $query->where('auto_resolve', true)),
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
                
                Tables\Actions\Action::make('acknowledge_alert')
                    ->label('Reconocer')
                    ->icon('fas-eye')
                    ->action(function ($record): void {
                        $record->update(['status' => 'acknowledged']);
                    })
                    ->visible(fn ($record): bool => $record->status === 'triggered')
                    ->color('info'),
                
                Tables\Actions\Action::make('resolve_alert')
                    ->label('Resolver')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['status' => 'resolved']);
                    })
                    ->visible(fn ($record): bool => in_array($record->status, ['triggered', 'acknowledged']))
                    ->color('success'),
                
                Tables\Actions\Action::make('activate_alert')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_alert')
                    ->label('Desactivar')
                    ->icon('fas-pause')
                    ->action(function ($record): void {
                        $record->update(['status' => 'inactive']);
                    })
                    ->visible(fn ($record): bool => $record->status === 'active')
                    ->color('warning'),
                
                Tables\Actions\Action::make('test_alert')
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
                    
                    Tables\Actions\BulkAction::make('acknowledge_all')
                        ->label('Reconocer Todas')
                        ->icon('fas-eye')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'acknowledged']);
                        })
                        ->color('info'),
                    
                    Tables\Actions\BulkAction::make('resolve_all')
                        ->label('Resolver Todas')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'resolved']);
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
            'index' => Pages\ListPriceAlerts::route('/'),
            'create' => Pages\CreatePriceAlert::route('/create'),
            'view' => Pages\ViewPriceAlert::route('/{record}'),
            'edit' => Pages\EditPriceAlert::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
