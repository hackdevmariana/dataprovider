<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnergyServiceResource\Pages;
use App\Filament\Resources\EnergyServiceResource\RelationManagers;
use App\Models\EnergyService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnergyServiceResource extends Resource
{
    protected static ?string $model = EnergyService::class;

    protected static ?string $navigationIcon = 'fas-bolt';

    protected static ?string $navigationGroup = 'Energía y Precios';

    protected static ?string $navigationLabel = 'Servicios Energéticos';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Servicio Energético';

    protected static ?string $pluralModelLabel = 'Servicios Energéticos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('service_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Servicio')
                            ->placeholder('Nombre del servicio energético...'),
                        
                        Forms\Components\TextInput::make('service_code')
                            ->maxLength(100)
                            ->label('Código del Servicio')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada del servicio...'),
                        
                        Forms\Components\Select::make('service_type')
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
                            ->label('Tipo de Servicio'),
                        
                        Forms\Components\Select::make('service_category')
                            ->options([
                                'generation' => '⚡ Generación',
                                'transmission' => '🔌 Transmisión',
                                'distribution' => '📡 Distribución',
                                'storage' => '🔋 Almacenamiento',
                                'consulting' => '💼 Consultoría',
                                'maintenance' => '🔧 Mantenimiento',
                                'installation' => '🛠️ Instalación',
                                'monitoring' => '📊 Monitoreo',
                                'efficiency' => '📈 Eficiencia',
                                'renewable' => '🌱 Renovable',
                                'conventional' => '🛢️ Convencional',
                                'smart_grid' => '🧠 Red Inteligente',
                                'microgrid' => '🏘️ Microred',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría del Servicio'),
                        
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

                Forms\Components\Section::make('Proveedor y Empresa')
                    ->schema([
                        Forms\Components\TextInput::make('provider_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Proveedor')
                            ->placeholder('Nombre de la empresa proveedora...'),
                        
                        Forms\Components\TextInput::make('provider_code')
                            ->maxLength(100)
                            ->label('Código del Proveedor')
                            ->placeholder('Código del proveedor...'),
                        
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255)
                            ->label('Nombre de la Empresa')
                            ->placeholder('Nombre de la empresa...'),
                        
                        Forms\Components\TextInput::make('company_registration')
                            ->maxLength(100)
                            ->label('Registro de la Empresa')
                            ->placeholder('Número de registro...'),
                        
                        Forms\Components\TextInput::make('provider_website')
                            ->label('Sitio Web del Proveedor')
                            ->url()
                            ->placeholder('https://...'),
                        
                        Forms\Components\TextInput::make('provider_phone')
                            ->tel()
                            ->maxLength(20)
                            ->label('Teléfono del Proveedor')
                            ->placeholder('+34...'),
                        
                        Forms\Components\TextInput::make('provider_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email del Proveedor')
                            ->placeholder('contacto@...'),
                        
                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(255)
                            ->label('Persona de Contacto')
                            ->placeholder('Nombre del contacto...'),
                        
                        Forms\Components\TextInput::make('contact_phone')
                            ->tel()
                            ->maxLength(20)
                            ->label('Teléfono de Contacto')
                            ->placeholder('+34...'),
                        
                        Forms\Components\TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email de Contacto')
                            ->placeholder('contacto@...'),
                    ])->columns(2),

                Forms\Components\Section::make('Características Técnicas')
                    ->schema([
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->label('Capacidad')
                            ->placeholder('Capacidad del servicio...'),
                        
                        Forms\Components\Select::make('capacity_unit')
                            ->options([
                                'kW' => 'kW',
                                'MW' => 'MW',
                                'GW' => 'GW',
                                'kWh' => 'kWh',
                                'MWh' => 'MWh',
                                'GWh' => 'GWh',
                                'm3' => 'm³',
                                'l' => 'L',
                                'gal' => 'Galones',
                                'other' => 'Otro',
                            ])
                            ->label('Unidad de Capacidad'),
                        
                        Forms\Components\TextInput::make('efficiency')
                            ->numeric()
                            ->label('Eficiencia (%)')
                            ->placeholder('Porcentaje de eficiencia...')
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('voltage')
                            ->numeric()
                            ->label('Voltaje')
                            ->placeholder('Voltaje del servicio...'),
                        
                        Forms\Components\Select::make('voltage_unit')
                            ->options([
                                'V' => 'V',
                                'kV' => 'kV',
                                'MV' => 'MV',
                                'other' => 'Otro',
                            ])
                            ->label('Unidad de Voltaje'),
                        
                        Forms\Components\TextInput::make('frequency')
                            ->numeric()
                            ->label('Frecuencia (Hz)')
                            ->placeholder('Frecuencia del servicio...'),
                        
                        Forms\Components\TextInput::make('power_factor')
                            ->numeric()
                            ->label('Factor de Potencia')
                            ->placeholder('Factor de potencia...')
                            ->minValue(0)
                            ->maxValue(1),
                        
                        Forms\Components\TextInput::make('reliability')
                            ->numeric()
                            ->label('Confiabilidad (%)')
                            ->placeholder('Porcentaje de confiabilidad...')
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('uptime')
                            ->numeric()
                            ->label('Tiempo de Actividad (%)')
                            ->placeholder('Porcentaje de tiempo activo...')
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('response_time')
                            ->numeric()
                            ->label('Tiempo de Respuesta')
                            ->placeholder('Tiempo de respuesta...'),
                        
                        Forms\Components\Select::make('response_time_unit')
                            ->options([
                                'minutes' => 'Minutos',
                                'hours' => 'Horas',
                                'days' => 'Días',
                                'other' => 'Otro',
                            ])
                            ->label('Unidad de Tiempo de Respuesta'),
                    ])->columns(2),

                Forms\Components\Section::make('Ubicación y Cobertura')
                    ->schema([
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
                        
                        Forms\Components\TextInput::make('service_area')
                            ->maxLength(255)
                            ->label('Área de Servicio')
                            ->placeholder('Área geográfica del servicio...'),
                        
                        Forms\Components\TextInput::make('zip_codes')
                            ->maxLength(500)
                            ->label('Códigos Postales')
                            ->placeholder('Códigos postales cubiertos...'),
                        
                        Forms\Components\Toggle::make('is_mobile')
                            ->label('Es Móvil')
                            ->default(false)
                            ->helperText('El servicio se puede mover'),
                        
                        Forms\Components\Toggle::make('is_portable')
                            ->label('Es Portátil')
                            ->default(false)
                            ->helperText('El servicio es portátil'),
                        
                        Forms\Components\Toggle::make('is_remote')
                            ->label('Es Remoto')
                            ->default(false)
                            ->helperText('El servicio se puede operar remotamente'),
                        
                        Forms\Components\TextInput::make('installation_requirements')
                            ->maxLength(500)
                            ->label('Requisitos de Instalación')
                            ->placeholder('Requisitos para la instalación...'),
                    ])->columns(2),

                Forms\Components\Section::make('Precios y Tarifas')
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->numeric()
                            ->label('Precio Base')
                            ->placeholder('Precio base del servicio...'),
                        
                        Forms\Components\Select::make('price_currency')
                            ->options([
                                'EUR' => '€ EUR',
                                'USD' => '$ USD',
                                'GBP' => '£ GBP',
                                'JPY' => '¥ JPY',
                                'CHF' => 'CHF',
                                'CAD' => 'C$ CAD',
                                'AUD' => 'A$ AUD',
                                'other' => 'Otro',
                            ])
                            ->default('EUR')
                            ->label('Moneda del Precio'),
                        
                        Forms\Components\Select::make('price_unit')
                            ->options([
                                'per_kWh' => 'por kWh',
                                'per_MWh' => 'por MWh',
                                'per_kW' => 'por kW',
                                'per_month' => 'por mes',
                                'per_year' => 'por año',
                                'per_service' => 'por servicio',
                                'per_hour' => 'por hora',
                                'per_day' => 'por día',
                                'other' => 'Otro',
                            ])
                            ->label('Unidad de Precio'),
                        
                        Forms\Components\TextInput::make('setup_fee')
                            ->numeric()
                            ->label('Cargo de Instalación')
                            ->placeholder('Cargo por instalación...'),
                        
                        Forms\Components\TextInput::make('maintenance_fee')
                            ->numeric()
                            ->label('Cargo de Mantenimiento')
                            ->placeholder('Cargo por mantenimiento...'),
                        
                        Forms\Components\TextInput::make('cancellation_fee')
                            ->numeric()
                            ->label('Cargo de Cancelación')
                            ->placeholder('Cargo por cancelación...'),
                        
                        Forms\Components\Toggle::make('has_discounts')
                            ->label('Tiene Descuentos')
                            ->default(false)
                            ->helperText('El servicio ofrece descuentos'),
                        
                        Forms\Components\Textarea::make('discount_details')
                            ->maxLength(500)
                            ->label('Detalles de Descuentos')
                            ->rows(2)
                            ->placeholder('Detalles sobre descuentos disponibles...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_discounts')),
                        
                        Forms\Components\Toggle::make('has_promotions')
                            ->label('Tiene Promociones')
                            ->default(false)
                            ->helperText('El servicio tiene promociones activas'),
                        
                        Forms\Components\Textarea::make('promotion_details')
                            ->maxLength(500)
                            ->label('Detalles de Promociones')
                            ->rows(2)
                            ->placeholder('Detalles sobre promociones...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_promotions')),
                    ])->columns(2),

                Forms\Components\Section::make('Disponibilidad y Horarios')
                    ->schema([
                        Forms\Components\Select::make('availability_status')
                            ->options([
                                'available' => '✅ Disponible',
                                'limited' => '⚠️ Limitada',
                                'unavailable' => '❌ No Disponible',
                                'coming_soon' => '🚀 Próximamente',
                                'discontinued' => '🛑 Discontinuado',
                                'maintenance' => '🔧 En Mantenimiento',
                                'testing' => '🧪 En Pruebas',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('available')
                            ->label('Estado de Disponibilidad'),
                        
                        Forms\Components\TextInput::make('availability_hours')
                            ->maxLength(255)
                            ->label('Horarios de Disponibilidad')
                            ->placeholder('Horarios del servicio...'),
                        
                        Forms\Components\Toggle::make('is_24_7')
                            ->label('24/7')
                            ->default(false)
                            ->helperText('El servicio está disponible 24/7'),
                        
                        Forms\Components\Toggle::make('has_emergency_service')
                            ->label('Servicio de Emergencia')
                            ->default(false)
                            ->helperText('Ofrece servicio de emergencia'),
                        
                        Forms\Components\TextInput::make('emergency_phone')
                            ->tel()
                            ->maxLength(20)
                            ->label('Teléfono de Emergencia')
                            ->placeholder('+34...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_emergency_service')),
                        
                        Forms\Components\TextInput::make('response_time_emergency')
                            ->numeric()
                            ->label('Tiempo de Respuesta Emergencia')
                            ->placeholder('Tiempo de respuesta para emergencias...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_emergency_service')),
                        
                        Forms\Components\Select::make('response_time_emergency_unit')
                            ->options([
                                'minutes' => 'Minutos',
                                'hours' => 'Horas',
                                'other' => 'Otro',
                            ])
                            ->label('Unidad de Tiempo Emergencia')
                            ->visible(fn (Forms\Get $get): bool => $get('has_emergency_service')),
                        
                        Forms\Components\Toggle::make('has_weekend_service')
                            ->label('Servicio en Fines de Semana')
                            ->default(false)
                            ->helperText('Ofrece servicio en fines de semana'),
                        
                        Forms\Components\Toggle::make('has_holiday_service')
                            ->label('Servicio en Festivos')
                            ->default(false)
                            ->helperText('Ofrece servicio en días festivos'),
                    ])->columns(2),

                Forms\Components\Section::make('Certificaciones y Calidad')
                    ->schema([
                        Forms\Components\Toggle::make('is_certified')
                            ->label('Está Certificado')
                            ->default(false)
                            ->helperText('El servicio tiene certificaciones'),
                        
                        Forms\Components\Textarea::make('certifications')
                            ->maxLength(500)
                            ->label('Certificaciones')
                            ->rows(2)
                            ->placeholder('Certificaciones del servicio...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_certified')),
                        
                        Forms\Components\Toggle::make('is_licensed')
                            ->label('Está Licenciado')
                            ->default(false)
                            ->helperText('El servicio tiene licencias'),
                        
                        Forms\Components\Textarea::make('licenses')
                            ->maxLength(500)
                            ->label('Licencias')
                            ->rows(2)
                            ->placeholder('Licencias del servicio...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_licensed')),
                        
                        Forms\Components\Toggle::make('is_insured')
                            ->label('Está Asegurado')
                            ->default(false)
                            ->helperText('El servicio tiene seguro'),
                        
                        Forms\Components\Textarea::make('insurance_details')
                            ->maxLength(500)
                            ->label('Detalles del Seguro')
                            ->rows(2)
                            ->placeholder('Detalles del seguro...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_insured')),
                        
                        Forms\Components\Toggle::make('has_warranty')
                            ->label('Tiene Garantía')
                            ->default(false)
                            ->helperText('El servicio incluye garantía'),
                        
                        Forms\Components\Textarea::make('warranty_details')
                            ->maxLength(500)
                            ->label('Detalles de la Garantía')
                            ->rows(2)
                            ->placeholder('Detalles de la garantía...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_warranty')),
                        
                        Forms\Components\TextInput::make('warranty_duration')
                            ->maxLength(100)
                            ->label('Duración de la Garantía')
                            ->placeholder('Duración de la garantía...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_warranty')),
                        
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
                    ])->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('features')
                            ->maxLength(1000)
                            ->label('Características')
                            ->rows(3)
                            ->placeholder('Características especiales del servicio...'),
                        
                        Forms\Components\Textarea::make('benefits')
                            ->maxLength(1000)
                            ->label('Beneficios')
                            ->rows(3)
                            ->placeholder('Beneficios del servicio...'),
                        
                        Forms\Components\Textarea::make('limitations')
                            ->maxLength(500)
                            ->label('Limitaciones')
                            ->rows(2)
                            ->placeholder('Limitaciones del servicio...'),
                        
                        Forms\Components\Textarea::make('requirements')
                            ->maxLength(500)
                            ->label('Requisitos')
                            ->rows(2)
                            ->placeholder('Requisitos para usar el servicio...'),
                        
                        Forms\Components\Textarea::make('restrictions')
                            ->maxLength(500)
                            ->label('Restricciones')
                            ->rows(2)
                            ->placeholder('Restricciones del servicio...'),
                        
                        Forms\Components\Textarea::make('terms_conditions')
                            ->maxLength(1000)
                            ->label('Términos y Condiciones')
                            ->rows(3)
                            ->placeholder('Términos y condiciones del servicio...'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas adicionales...'),
                    ])->columns(1),

                Forms\Components\Section::make('Estado del Servicio')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '✅ Activo',
                                'inactive' => '❌ Inactivo',
                                'pending' => '⏳ Pendiente',
                                'suspended' => '⏸️ Suspendido',
                                'discontinued' => '🛑 Discontinuado',
                                'maintenance' => '🔧 En Mantenimiento',
                                'testing' => '🧪 En Pruebas',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('active')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Servicio importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_premium')
                            ->label('Premium')
                            ->default(false)
                            ->helperText('Servicio de categoría premium'),
                        
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular')
                            ->default(false)
                            ->helperText('Servicio popular entre los clientes'),
                        
                        Forms\Components\Toggle::make('is_new')
                            ->label('Nuevo')
                            ->default(false)
                            ->helperText('Servicio recién lanzado'),
                        
                        Forms\Components\Toggle::make('is_recommended')
                            ->label('Recomendado')
                            ->default(false)
                            ->helperText('Servicio recomendado por expertos'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado')
                            ->default(false)
                            ->helperText('El servicio ha sido verificado'),
                        
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Aprobado')
                            ->default(false)
                            ->helperText('El servicio ha sido aprobado'),
                        
                        Forms\Components\DatePicker::make('launch_date')
                            ->label('Fecha de Lanzamiento')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\DatePicker::make('last_updated')
                            ->label('Última Actualización')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\TextInput::make('update_frequency')
                            ->maxLength(100)
                            ->label('Frecuencia de Actualización')
                            ->placeholder('Mensual, trimestral, anual...'),
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
                
                Tables\Columns\TextColumn::make('service_name')
                    ->label('Servicio')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('service_type')
                    ->label('Tipo')
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
                
                Tables\Columns\BadgeColumn::make('service_category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'generation',
                        'success' => 'transmission',
                        'warning' => 'distribution',
                        'info' => 'storage',
                        'danger' => 'consulting',
                        'secondary' => 'maintenance',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'generation' => '⚡ Generación',
                        'transmission' => '🔌 Transmisión',
                        'distribution' => '📡 Distribución',
                        'storage' => '🔋 Almacenamiento',
                        'consulting' => '💼 Consultoría',
                        'maintenance' => '🔧 Mantenimiento',
                        'installation' => '🛠️ Instalación',
                        'monitoring' => '📊 Monitoreo',
                        'efficiency' => '📈 Eficiencia',
                        'renewable' => '🌱 Renovable',
                        'conventional' => '🛢️ Convencional',
                        'smart_grid' => '🧠 Red Inteligente',
                        'microgrid' => '🏘️ Microred',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('provider_name')
                    ->label('Proveedor')
                    ->searchable()
                    ->limit(30)
                    ->weight('medium')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacidad')
                    ->numeric()
                    ->sortable()
                    ->suffix(fn ($record): string => $record->capacity_unit ? ' ' . $record->capacity_unit : '')
                    ->color(fn (float $state): string => match (true) {
                        $state >= 1000 => 'success',
                        $state >= 100 => 'info',
                        $state >= 10 => 'warning',
                        $state >= 1 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('efficiency')
                    ->label('Eficiencia')
                    ->numeric()
                    ->sortable()
                    ->suffix('%')
                    ->color(fn (float $state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        $state >= 60 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Precio Base')
                    ->money(fn ($record): string => $record->price_currency ?? 'EUR')
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state <= 50 => 'success',
                        $state <= 100 => 'info',
                        $state <= 200 => 'warning',
                        $state <= 500 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\BadgeColumn::make('availability_status')
                    ->label('Disponibilidad')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'limited',
                        'danger' => 'unavailable',
                        'info' => 'coming_soon',
                        'secondary' => 'discontinued',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => '✅ Disponible',
                        'limited' => '⚠️ Limitada',
                        'unavailable' => '❌ No Disponible',
                        'coming_soon' => '🚀 Próximamente',
                        'discontinued' => '🛑 Discontinuado',
                        'maintenance' => '🔧 En Mantenimiento',
                        'testing' => '🧪 En Pruebas',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_certified')
                    ->label('Certificado')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_licensed')
                    ->label('Licenciado')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_insured')
                    ->label('Asegurado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_premium')
                    ->label('Premium')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'info' => 'pending',
                        'warning' => 'suspended',
                        'secondary' => 'discontinued',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'pending' => '⏳ Pendiente',
                        'suspended' => '⏸️ Suspendido',
                        'discontinued' => '🛑 Discontinuado',
                        'maintenance' => '🔧 En Mantenimiento',
                        'testing' => '🧪 En Pruebas',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_type')
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
                    ->label('Tipo de Servicio'),
                
                Tables\Filters\SelectFilter::make('service_category')
                    ->options([
                        'generation' => '⚡ Generación',
                        'transmission' => '🔌 Transmisión',
                        'distribution' => '📡 Distribución',
                        'storage' => '🔋 Almacenamiento',
                        'consulting' => '💼 Consultoría',
                        'maintenance' => '🔧 Mantenimiento',
                        'installation' => '🛠️ Instalación',
                        'monitoring' => '📊 Monitoreo',
                        'efficiency' => '📈 Eficiencia',
                        'renewable' => '🌱 Renovable',
                        'conventional' => '🛢️ Convencional',
                        'smart_grid' => '🧠 Red Inteligente',
                        'microgrid' => '🏘️ Microred',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría del Servicio'),
                
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
                
                Tables\Filters\SelectFilter::make('availability_status')
                    ->options([
                        'available' => '✅ Disponible',
                        'limited' => '⚠️ Limitada',
                        'unavailable' => '❌ No Disponible',
                        'coming_soon' => '🚀 Próximamente',
                        'discontinued' => '🛑 Discontinuado',
                        'maintenance' => '🔧 En Mantenimiento',
                        'testing' => '🧪 En Pruebas',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado de Disponibilidad'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'pending' => '⏳ Pendiente',
                        'suspended' => '⏸️ Suspendido',
                        'discontinued' => '🛑 Discontinuado',
                        'maintenance' => '🔧 En Mantenimiento',
                        'testing' => '🧪 En Pruebas',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('premium_only')
                    ->label('Solo Premium')
                    ->query(fn (Builder $query): Builder => $query->where('is_premium', true)),
                
                Tables\Filters\Filter::make('certified_only')
                    ->label('Solo Certificados')
                    ->query(fn (Builder $query): Builder => $query->where('is_certified', true)),
                
                Tables\Filters\Filter::make('licensed_only')
                    ->label('Solo Licenciados')
                    ->query(fn (Builder $query): Builder => $query->where('is_licensed', true)),
                
                Tables\Filters\Filter::make('insured_only')
                    ->label('Solo Asegurados')
                    ->query(fn (Builder $query): Builder => $query->where('is_insured', true)),
                
                Tables\Filters\Filter::make('renewable_energy')
                    ->label('Solo Energía Renovable')
                    ->query(fn (Builder $query): Builder => $query->whereIn('energy_source', ['solar', 'wind', 'hydro', 'biomass', 'geothermal'])),
                
                Tables\Filters\Filter::make('high_efficiency')
                    ->label('Alta Eficiencia (80%+)')
                    ->query(fn (Builder $query): Builder => $query->where('efficiency', '>=', 80)),
                
                Tables\Filters\Filter::make('low_price')
                    ->label('Precio Bajo (≤50)')
                    ->query(fn (Builder $query): Builder => $query->where('base_price', '<=', 50)),
                
                Tables\Filters\Filter::make('available_services')
                    ->label('Solo Disponibles')
                    ->query(fn (Builder $query): Builder => $query->where('availability_status', 'available')),
                
                Tables\Filters\Filter::make('active_services')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
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
                
                Tables\Actions\Action::make('toggle_premium')
                    ->label(fn ($record): string => $record->is_premium ? 'Quitar Premium' : 'Marcar Premium')
                    ->icon(fn ($record): string => $record->is_premium ? 'fas-crown' : 'far-crown')
                    ->action(function ($record): void {
                        $record->update(['is_premium' => !$record->is_premium]);
                    })
                    ->color(fn ($record): string => $record->is_premium ? 'primary' : 'secondary'),
                
                Tables\Actions\Action::make('visit_website')
                    ->label('Visitar Sitio Web')
                    ->icon('fas-external-link-alt')
                    ->url(fn ($record): string => $record->provider_website)
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => !empty($record->provider_website))
                    ->color('info'),
                
                Tables\Actions\Action::make('contact_provider')
                    ->label('Contactar Proveedor')
                    ->icon('fas-phone')
                    ->action(function ($record): void {
                        // Aquí se podría implementar la lógica de contacto
                    })
                    ->visible(fn ($record): bool => !empty($record->provider_phone) || !empty($record->provider_email))
                    ->color('success'),
                
                Tables\Actions\Action::make('activate_service')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_service')
                    ->label('Desactivar')
                    ->icon('fas-pause')
                    ->action(function ($record): void {
                        $record->update(['status' => 'inactive']);
                    })
                    ->visible(fn ($record): bool => $record->status === 'active')
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Marcar como Destacados')
                        ->icon('fas-star')
                        ->action(function ($records): void {
                            $records->each->update(['is_featured' => true]);
                        })
                        ->color('warning'),
                    
                    Tables\Actions\BulkAction::make('mark_premium')
                        ->label('Marcar como Premium')
                        ->icon('fas-crown')
                        ->action(function ($records): void {
                            $records->each->update(['is_premium' => true]);
                        })
                        ->color('primary'),
                    
                    Tables\Actions\BulkAction::make('activate_all')
                        ->label('Activar Todos')
                        ->icon('fas-play')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'active']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('deactivate_all')
                        ->label('Desactivar Todos')
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
            'index' => Pages\ListEnergyServices::route('/'),
            'create' => Pages\CreateEnergyService::route('/create'),
            'view' => Pages\ViewEnergyService::route('/{record}'),
            'edit' => Pages\EditEnergyService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
