<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionRightResource\Pages;
use App\Filament\Resources\ProductionRightResource\RelationManagers;
use App\Models\ProductionRight;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ProductionRightResource extends Resource
{
    protected static ?string $model = ProductionRight::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Energía y Sostenibilidad';
    protected static ?string $modelLabel = 'Derecho de Producción';
    protected static ?string $pluralModelLabel = 'Derechos de Producción';
    protected static ?int $navigationSort = 3;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Básica')
                ->description('Datos principales del derecho de producción')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Título')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Ej: Derechos de Producción Solar - Parque Andalucía'),

                            Forms\Components\TextInput::make('right_identifier')
                                ->label('Identificador del Derecho')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Identificador único del derecho'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('URL amigable'),

                            Forms\Components\Select::make('right_type')
                                ->label('Tipo de Derecho')
                                ->options([
                                    'energy_production' => 'Producción Energética',
                                    'excess_energy' => 'Excedentes de Energía',
                                    'carbon_credits' => 'Créditos de Carbono',
                                    'renewable_certificates' => 'Certificados Renovables',
                                    'grid_injection' => 'Inyección a Red',
                                    'virtual_battery' => 'Batería Virtual',
                                    'demand_response' => 'Respuesta a la Demanda',
                                    'capacity_rights' => 'Derechos de Capacidad',
                                    'green_certificates' => 'Certificados Verdes',
                                    'other' => 'Otro Tipo',
                                ])
                                ->required()
                                ->searchable(),
                        ]),

                    Forms\Components\Textarea::make('description')
                        ->label('Descripción')
                        ->rows(3)
                        ->placeholder('Descripción detallada del derecho de producción...'),
                ])
                ->collapsible(false),

            Forms\Components\Section::make('Capacidad y Producción')
                ->description('Características técnicas del derecho')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('total_capacity_kw')
                                ->label('Capacidad Total (kW)')
                                ->required()
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->suffix('kW'),

                            Forms\Components\TextInput::make('available_capacity_kw')
                                ->label('Capacidad Disponible (kW)')
                                ->required()
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->suffix('kW'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('reserved_capacity_kw')
                                ->label('Capacidad Reservada (kW)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->default(0)
                                ->suffix('kW'),

                            Forms\Components\TextInput::make('sold_capacity_kw')
                                ->label('Capacidad Vendida (kW)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->default(0)
                                ->suffix('kW'),
                        ]),

                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('estimated_annual_production_kwh')
                                ->label('Producción Anual Estimada (kWh)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->suffix('kWh'),

                            Forms\Components\TextInput::make('guaranteed_annual_production_kwh')
                                ->label('Producción Anual Garantizada (kWh)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->suffix('kWh'),

                            Forms\Components\TextInput::make('actual_annual_production_kwh')
                                ->label('Producción Anual Real (kWh)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->default(0)
                                ->suffix('kWh'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Período de Validez')
                ->description('Duración y renovación del derecho')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('valid_from')
                                ->label('Válido Desde')
                                ->required()
                                ->displayFormat('d/m/Y'),

                            Forms\Components\DatePicker::make('valid_until')
                                ->label('Válido Hasta')
                                ->required()
                                ->displayFormat('d/m/Y'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('duration_years')
                                ->label('Duración (años)')
                                ->numeric()
                                ->step(1)
                                ->minValue(1)
                                ->suffix('años'),

                            Forms\Components\Toggle::make('renewable_right')
                                ->label('Derecho Renovable')
                                ->default(false),
                        ]),

                    Forms\Components\TextInput::make('renewal_period_years')
                                ->label('Período de Renovación (años)')
                                ->numeric()
                                ->step(1)
                                ->minValue(1)
                                ->suffix('años')
                                ->visible(fn (Forms\Get $get) => $get('renewable_right')),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Modelo de Precios')
                ->description('Estructura económica del derecho')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('pricing_model')
                                ->label('Modelo de Precios')
                                ->options([
                                    'fixed_price_kwh' => 'Precio Fijo por kWh',
                                    'market_price' => 'Precio de Mercado',
                                    'premium_over_market' => 'Prima sobre Mercado',
                                    'auction_based' => 'Basado en Subasta',
                                    'performance_based' => 'Basado en Rendimiento',
                                    'subscription_model' => 'Modelo de Suscripción',
                                    'revenue_sharing' => 'Participación en Ingresos',
                                    'hybrid' => 'Modelo Híbrido',
                                ])
                                ->required()
                                ->searchable(),

                            Forms\Components\TextInput::make('price_per_kwh')
                                ->label('Precio por kWh (€)')
                                ->numeric()
                                ->step(0.0001)
                                ->minValue(0)
                                ->prefix('€'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('market_premium_percentage')
                                ->label('Prima sobre Mercado (%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%'),

                            Forms\Components\TextInput::make('minimum_guaranteed_price')
                                ->label('Precio Mínimo Garantizado (€)')
                                ->numeric()
                                ->step(0.0001)
                                ->minValue(0)
                                ->prefix('€'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('maximum_price_cap')
                                ->label('Precio Máximo (€)')
                                ->numeric()
                                ->step(0.0001)
                                ->minValue(0)
                                ->prefix('€'),

                            Forms\Components\KeyValue::make('price_escalation_terms')
                                ->label('Términos de Escalación de Precios')
                                ->keyLabel('Término')
                                ->valueLabel('Valor')
                                ->addActionLabel('Añadir Término'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Términos de Pago')
                ->description('Condiciones financieras del contrato')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('upfront_payment')
                                ->label('Pago Inicial (€)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->prefix('€'),

                            Forms\Components\TextInput::make('periodic_payment')
                                ->label('Pago Periódico (€)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->prefix('€'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('payment_frequency')
                                ->label('Frecuencia de Pago')
                                ->options([
                                    'monthly' => 'Mensual',
                                    'quarterly' => 'Trimestral',
                                    'biannual' => 'Semestral',
                                    'annual' => 'Anual',
                                    'on_production' => 'Por Producción',
                                ])
                                ->searchable(),

                            Forms\Components\TextInput::make('security_deposit')
                                ->label('Depósito de Garantía (€)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->prefix('€'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\KeyValue::make('payment_terms')
                                ->label('Términos de Pago')
                                ->keyLabel('Término')
                                ->valueLabel('Valor')
                                ->addActionLabel('Añadir Término'),

                            Forms\Components\KeyValue::make('penalty_clauses')
                                ->label('Cláusulas de Penalización')
                                ->keyLabel('Cláusula')
                                ->valueLabel('Penalización')
                                ->addActionLabel('Añadir Cláusula'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Garantías y Seguros')
                ->description('Protecciones y coberturas del derecho')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('production_guaranteed')
                                ->label('Producción Garantizada')
                                ->default(false),

                            Forms\Components\TextInput::make('production_guarantee_percentage')
                                ->label('Porcentaje de Garantía (%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%')
                                ->visible(fn (Forms\Get $get) => $get('production_guaranteed')),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('insurance_included')
                                ->label('Seguro Incluido')
                                ->default(false),

                            Forms\Components\Textarea::make('insurance_details')
                                ->label('Detalles del Seguro')
                                ->rows(2)
                                ->visible(fn (Forms\Get $get) => $get('insurance_included')),
                        ]),

                    Forms\Components\KeyValue::make('risk_allocation')
                        ->label('Asignación de Riesgos')
                        ->keyLabel('Riesgo')
                        ->valueLabel('Responsabilidad')
                        ->addActionLabel('Añadir Riesgo'),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Derechos y Obligaciones')
                ->description('Marco legal del contrato')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\KeyValue::make('buyer_rights')
                                ->label('Derechos del Comprador')
                                ->keyLabel('Derecho')
                                ->valueLabel('Descripción')
                                ->addActionLabel('Añadir Derecho'),

                            Forms\Components\KeyValue::make('buyer_obligations')
                                ->label('Obligaciones del Comprador')
                                ->keyLabel('Obligación')
                                ->valueLabel('Descripción')
                                ->addActionLabel('Añadir Obligación'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\KeyValue::make('seller_rights')
                                ->label('Derechos del Vendedor')
                                ->keyLabel('Derecho')
                                ->valueLabel('Descripción')
                                ->addActionLabel('Añadir Derecho'),

                            Forms\Components\KeyValue::make('seller_obligations')
                                ->label('Obligaciones del Vendedor')
                                ->keyLabel('Obligación')
                                ->valueLabel('Descripción')
                                ->addActionLabel('Añadir Obligación'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Transferibilidad')
                ->description('Condiciones de transferencia del derecho')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('is_transferable')
                                ->label('Es Transferible')
                                ->default(true),

                            Forms\Components\TextInput::make('max_transfers')
                                ->label('Máximo de Transferencias')
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->suffix('transferencias'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('current_transfers')
                                ->label('Transferencias Actuales')
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->default(0)
                                ->suffix('transferencias'),

                            Forms\Components\TextInput::make('transfer_fee_percentage')
                                ->label('Comisión por Transferencia (%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%'),
                        ]),

                    Forms\Components\KeyValue::make('transfer_restrictions')
                        ->label('Restricciones de Transferencia')
                        ->keyLabel('Restricción')
                        ->valueLabel('Descripción')
                        ->addActionLabel('Añadir Restricción'),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Estado y Seguimiento')
                ->description('Estado actual y métricas del derecho')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label('Estado')
                                ->options([
                                    'available' => 'Disponible',
                                    'reserved' => 'Reservado',
                                    'under_negotiation' => 'En Negociación',
                                    'contracted' => 'Contratado',
                                    'active' => 'Activo',
                                    'suspended' => 'Suspendido',
                                    'expired' => 'Expirado',
                                    'cancelled' => 'Cancelado',
                                    'disputed' => 'En Disputa',
                                ])
                                ->default('available')
                                ->required()
                                ->searchable(),

                            Forms\Components\Textarea::make('status_notes')
                                ->label('Notas sobre el Estado')
                                ->rows(2),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DateTimePicker::make('contract_signed_at')
                                ->label('Fecha de Firma del Contrato')
                                ->displayFormat('d/m/Y H:i'),

                            Forms\Components\DateTimePicker::make('activated_at')
                                ->label('Fecha de Activación')
                                ->displayFormat('d/m/Y H:i'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('Derecho Activo')
                                ->default(true),

                            Forms\Components\Toggle::make('is_featured')
                                ->label('Derecho Destacado')
                                ->default(false),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Seguimiento de Rendimiento')
                ->description('Métricas de producción y rendimiento')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('current_month_production_kwh')
                                ->label('Producción Mes Actual (kWh)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->default(0)
                                ->suffix('kWh'),

                            Forms\Components\TextInput::make('ytd_production_kwh')
                                ->label('Producción Año Hasta Fecha (kWh)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->default(0)
                                ->suffix('kWh'),

                            Forms\Components\TextInput::make('lifetime_production_kwh')
                                ->label('Producción Total Histórica (kWh)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->default(0)
                                ->suffix('kWh'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('performance_ratio')
                                ->label('Ratio de Rendimiento (%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(200)
                                ->default(100)
                                ->suffix('%'),

                            Forms\Components\KeyValue::make('monthly_production_history')
                                ->label('Historial Mensual de Producción')
                                ->keyLabel('Mes')
                                ->valueLabel('Datos')
                                ->addActionLabel('Añadir Mes'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Información Regulatoria')
                ->description('Cumplimiento y certificaciones')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('regulatory_framework')
                                ->label('Marco Regulatorio')
                                ->maxLength(255)
                                ->placeholder('Ej: Real Decreto 244/2019'),

                            Forms\Components\Toggle::make('grid_code_compliant')
                                ->label('Cumple Código de Red')
                                ->default(true),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\KeyValue::make('applicable_regulations')
                                ->label('Regulaciones Aplicables')
                                ->keyLabel('Regulación')
                                ->valueLabel('Descripción')
                                ->addActionLabel('Añadir Regulación'),

                            Forms\Components\KeyValue::make('certifications')
                                ->label('Certificaciones')
                                ->keyLabel('Certificación')
                                ->valueLabel('Descripción')
                                ->addActionLabel('Añadir Certificación'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Documentación Legal')
                ->description('Documentos y firmas del contrato')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\KeyValue::make('legal_documents')
                                ->label('Documentos Legales')
                                ->keyLabel('Documento')
                                ->valueLabel('Descripción')
                                ->addActionLabel('Añadir Documento'),

                            Forms\Components\TextInput::make('contract_template_version')
                                ->label('Versión de Plantilla de Contrato')
                                ->maxLength(255),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('electronic_signature_valid')
                                ->label('Firma Electrónica Válida')
                                ->default(false),

                            Forms\Components\KeyValue::make('signature_details')
                                ->label('Detalles de Firma')
                                ->keyLabel('Detalle')
                                ->valueLabel('Valor')
                                ->addActionLabel('Añadir Detalle'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Métricas de Mercado')
                ->description('Estadísticas de interés y ofertas')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('views_count')
                                ->label('Visualizaciones')
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->default(0),

                            Forms\Components\TextInput::make('inquiries_count')
                                ->label('Consultas')
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->default(0),

                            Forms\Components\TextInput::make('offers_received')
                                ->label('Ofertas Recibidas')
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->default(0),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('highest_offer_price')
                                ->label('Mejor Oferta Recibida (€)')
                                ->numeric()
                                ->step(0.0001)
                                ->minValue(0)
                                ->prefix('€'),

                            Forms\Components\TextInput::make('average_market_price')
                                ->label('Precio Promedio de Mercado (€)')
                                ->numeric()
                                ->step(0.0001)
                                ->minValue(0)
                                ->prefix('€'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Configuración Avanzada')
                ->description('Opciones de gestión automática')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('auto_accept_offers')
                                ->label('Aceptar Ofertas Automáticamente')
                                ->default(false),

                            Forms\Components\TextInput::make('auto_accept_threshold')
                                ->label('Umbral de Aceptación Automática (€)')
                                ->numeric()
                                ->step(0.0001)
                                ->minValue(0)
                                ->prefix('€')
                                ->visible(fn (Forms\Get $get) => $get('auto_accept_offers')),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('allow_partial_sales')
                                ->label('Permitir Ventas Parciales')
                                ->default(true),

                            Forms\Components\TextInput::make('minimum_sale_capacity_kw')
                                ->label('Capacidad Mínima de Venta (kW)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->suffix('kW'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Relaciones')
                ->description('Usuarios e instalaciones asociadas')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('seller_id')
                                ->label('Vendedor')
                                ->relationship('seller', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('buyer_id')
                                ->label('Comprador')
                                ->relationship('buyer', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('installation_id')
                                ->label('Instalación Energética')
                                ->relationship('installation', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),

                            Forms\Components\Select::make('project_proposal_id')
                                ->label('Propuesta de Proyecto')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->title)
                    ->wrap(),

                Tables\Columns\TextColumn::make('right_identifier')
                    ->label('Identificador')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('right_type')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'primary' => 'energy_production',
                        'success' => 'excess_energy',
                        'warning' => 'carbon_credits',
                        'info' => 'renewable_certificates',
                        'secondary' => 'grid_injection',
                        'danger' => 'virtual_battery',
                        'gray' => 'demand_response',
                    ]),

                Tables\Columns\TextColumn::make('total_capacity_kw')
                    ->label('Capacidad Total')
                    ->searchable()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix(' kW')
                    ->color('success'),

                Tables\Columns\TextColumn::make('available_capacity_kw')
                    ->label('Capacidad Disponible')
                    ->searchable()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix(' kW')
                    ->color('info'),

                Tables\Columns\TextColumn::make('price_per_kwh')
                    ->label('Precio por kWh')
                    ->searchable()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 4,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->prefix('€')
                    ->color('warning'),

                Tables\Columns\BadgeColumn::make('pricing_model')
                    ->label('Modelo de Precios')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'primary' => 'fixed_price_kwh',
                        'success' => 'market_price',
                        'warning' => 'premium_over_market',
                        'info' => 'auction_based',
                        'secondary' => 'performance_based',
                        'danger' => 'subscription_model',
                        'gray' => 'revenue_sharing',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('duration_years')
                    ->label('Duración')
                    ->searchable()
                    ->sortable()
                    ->numeric()
                    ->suffix(' años')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('valid_from')
                    ->label('Válido Desde')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Válido Hasta')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'success' => 'available',
                        'warning' => 'reserved',
                        'info' => 'under_negotiation',
                        'primary' => 'contracted',
                        'success' => 'active',
                        'danger' => 'suspended',
                        'gray' => 'expired',
                        'danger' => 'cancelled',
                        'warning' => 'disputed',
                    ]),

                Tables\Columns\IconColumn::make('renewable_right')
                    ->label('Renovable')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('production_guaranteed')
                    ->label('Garantizado')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_transferable')
                    ->label('Transferible')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Vendedor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Comprador')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('installation.name')
                    ->label('Instalación')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Visualizaciones')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('inquiries_count')
                    ->label('Consultas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('offers_received')
                    ->label('Ofertas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('right_type')
                    ->label('Tipo de Derecho')
                    ->options([
                        'energy_production' => 'Producción Energética',
                        'excess_energy' => 'Excedentes de Energía',
                        'carbon_credits' => 'Créditos de Carbono',
                        'renewable_certificates' => 'Certificados Renovables',
                        'grid_injection' => 'Inyección a Red',
                        'virtual_battery' => 'Batería Virtual',
                        'demand_response' => 'Respuesta a la Demanda',
                        'capacity_rights' => 'Derechos de Capacidad',
                        'green_certificates' => 'Certificados Verdes',
                        'other' => 'Otro Tipo',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'available' => 'Disponible',
                        'reserved' => 'Reservado',
                        'under_negotiation' => 'En Negociación',
                        'contracted' => 'Contratado',
                        'active' => 'Activo',
                        'suspended' => 'Suspendido',
                        'expired' => 'Expirado',
                        'cancelled' => 'Cancelado',
                        'disputed' => 'En Disputa',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('pricing_model')
                    ->label('Modelo de Precios')
                    ->options([
                        'fixed_price_kwh' => 'Precio Fijo por kWh',
                        'market_price' => 'Precio de Mercado',
                        'premium_over_market' => 'Prima sobre Mercado',
                        'auction_based' => 'Basado en Subasta',
                        'performance_based' => 'Basado en Rendimiento',
                        'subscription_model' => 'Modelo de Suscripción',
                        'revenue_sharing' => 'Participación en Ingresos',
                        'hybrid' => 'Modelo Híbrido',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\Filter::make('high_capacity')
                    ->label('Alta Capacidad')
                    ->query(fn (Builder $query) => $query->where('total_capacity_kw', '>', 1000))
                    ->toggle(),

                Tables\Filters\Filter::make('low_price')
                    ->label('Bajo Precio')
                    ->query(fn (Builder $query) => $query->where('price_per_kwh', '<', 0.10))
                    ->toggle(),

                Tables\Filters\Filter::make('renewable_only')
                    ->label('Solo Renovables')
                    ->query(fn (Builder $query) => $query->where('renewable_right', true))
                    ->toggle(),

                Tables\Filters\Filter::make('guaranteed_production')
                    ->label('Producción Garantizada')
                    ->query(fn (Builder $query) => $query->where('production_guaranteed', true))
                    ->toggle(),

                Tables\Filters\Filter::make('transferable_only')
                    ->label('Solo Transferibles')
                    ->query(fn (Builder $query) => $query->where('is_transferable', true))
                    ->toggle(),

                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query) => $query->where('is_featured', true))
                    ->toggle(),

                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query) => $query->where('is_active', true))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),

                Tables\Actions\Action::make('calculate_revenue')
                    ->label('Calcular Ingresos')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('years')
                            ->label('Años de Contrato')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(10)
                            ->suffix('años'),
                        Forms\Components\TextInput::make('capacity_kw')
                            ->label('Capacidad a Comprar (kW)')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->suffix('kW'),
                    ])
                    ->action(function (ProductionRight $record, array $data): void {
                        $years = $data['years'];
                        $capacity = $data['capacity_kw'];
                        $annualProduction = $record->estimated_annual_production_kwh * ($capacity / $record->total_capacity_kw);
                        $annualRevenue = $annualProduction * $record->price_per_kwh;
                        $totalRevenue = $annualRevenue * $years;
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Cálculo de Ingresos')
                            ->body("Para {$capacity} kW durante {$years} años: Ingreso anual {$annualRevenue}€, Total {$totalRevenue}€")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Calcular ingresos potenciales del derecho'),

                Tables\Actions\Action::make('view_installation')
                    ->label('Ver Instalación')
                    ->icon('heroicon-o-building-office')
                    ->color('info')
                    ->url(fn ($record) => $record->installation ? route('filament.admin.resources.energy-installations.edit', $record->installation) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->installation !== null)
                    ->tooltip('Ver detalles de la instalación energética'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Eliminar Seleccionados'),
                
                Tables\Actions\BulkAction::make('export_market_data')
                    ->label('Exportar Datos de Mercado')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (Collection $records): void {
                        $count = $records->count();
                        $totalCapacity = $records->sum('total_capacity_kw');
                        $avgPrice = $records->avg('price_per_kwh');
                        \Filament\Notifications\Notification::make()
                            ->title('Datos Exportados')
                            ->body("Se han exportado {$count} derechos con capacidad total de {$totalCapacity} kW y precio medio de " . number_format($avgPrice, 4) . " €/kWh")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Exportar estadísticas de mercado'),
                
                Tables\Actions\BulkAction::make('mark_as_featured')
                    ->label('Marcar como Destacados')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(function (Collection $records): void {
                        $records->each(function ($record) {
                            $record->update(['is_featured' => true]);
                        });
                        $count = $records->count();
                        \Filament\Notifications\Notification::make()
                            ->title('Derechos Destacados')
                            ->body("Se han marcado {$count} derechos como destacados")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Marcar derechos seleccionados como destacados'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([25, 50, 100])
            ->searchable()
            ->searchPlaceholder('Buscar por título, tipo, vendedor o instalación...');
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
            'index' => Pages\ListProductionRights::route('/'),
            'create' => Pages\CreateProductionRight::route('/create'),
            'edit' => Pages\EditProductionRight::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
