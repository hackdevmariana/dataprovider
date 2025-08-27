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

    protected static ?string $navigationIcon = 'fas-plug';

    protected static ?string $navigationGroup = 'Energía y Precios';

    protected static ?string $navigationLabel = 'Servicios Energéticos';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Servicio Energético';

    protected static ?string $pluralModelLabel = 'Servicios Energéticos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Servicio')
                            ->placeholder('Nombre del servicio energético...'),
                        
                        Forms\Components\TextInput::make('service_code')
                            ->maxLength(100)
                            ->label('Código de Servicio')
                            ->placeholder('Código único del servicio...'),
                        
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
                                'maintenance' => '🔧 Mantenimiento',
                                'consulting' => '💼 Consultoría',
                                'installation' => '🔌 Instalación',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Servicio'),
                        
                        Forms\Components\Select::make('category')
                            ->options([
                                'generation' => '⚡ Generación',
                                'distribution' => '📡 Distribución',
                                'transmission' => '🔌 Transmisión',
                                'retail' => '🏪 Comercialización',
                                'maintenance' => '🔧 Mantenimiento',
                                'consulting' => '💼 Consultoría',
                                'installation' => '🔌 Instalación',
                                'monitoring' => '📊 Monitoreo',
                                'storage' => '🔋 Almacenamiento',
                                'efficiency' => '💡 Eficiencia',
                                'renewable' => '🌱 Renovable',
                                'conventional' => '🛢️ Convencional',
                                'hybrid' => '🔄 Híbrido',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría'),
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
                            ->placeholder('Código único del proveedor...'),
                        
                        Forms\Components\TextInput::make('company_registration')
                            ->maxLength(100)
                            ->label('Registro Mercantil')
                            ->placeholder('Número de registro...'),
                        
                        Forms\Components\TextInput::make('tax_id')
                            ->maxLength(100)
                            ->label('CIF/NIF')
                            ->placeholder('Identificación fiscal...'),
                        
                        Forms\Components\TextInput::make('website')
                            ->maxLength(500)
                            ->label('Sitio Web')
                            ->url()
                            ->placeholder('https://www.ejemplo.com'),
                        
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50)
                            ->label('Teléfono'),
                    ])->columns(2),

                Forms\Components\Section::make('Características Técnicas')
                    ->schema([
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('MW')
                            ->label('Capacidad')
                            ->helperText('Capacidad en megavatios'),
                        
                        Forms\Components\Select::make('capacity_unit')
                            ->options([
                                'MW' => 'MW (Megavatio)',
                                'kW' => 'kW (Kilovatio)',
                                'W' => 'W (Vatio)',
                                'MWh' => 'MWh (Megavatio-hora)',
                                'kWh' => 'kWh (Kilovatio-hora)',
                                'Wh' => 'Wh (Vatio-hora)',
                                'm³/h' => 'm³/h (Metros cúbicos por hora)',
                                'l/h' => 'l/h (Litros por hora)',
                                'kg/h' => 'kg/h (Kilogramos por hora)',
                                'other' => 'Otro',
                            ])
                            ->label('Unidad de Capacidad'),
                        
                        Forms\Components\TextInput::make('efficiency')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->label('Eficiencia')
                            ->helperText('Porcentaje de eficiencia'),
                        
                        Forms\Components\TextInput::make('voltage')
                            ->numeric()
                            ->suffix('V')
                            ->label('Voltaje')
                            ->helperText('Voltaje en voltios'),
                        
                        Forms\Components\TextInput::make('frequency')
                            ->numeric()
                            ->suffix('Hz')
                            ->label('Frecuencia')
                            ->helperText('Frecuencia en hercios'),
                        
                        Forms\Components\TextInput::make('power_factor')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(1)
                            ->label('Factor de Potencia')
                            ->helperText('Valor entre 0 y 1'),
                    ])->columns(2),

                Forms\Components\Section::make('Ubicación y Cobertura')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255)
                            ->label('Ubicación')
                            ->placeholder('Ciudad, región o lugar específico...'),
                        
                        Forms\Components\TextInput::make('country')
                            ->maxLength(100)
                            ->label('País')
                            ->placeholder('País donde se ofrece el servicio...'),
                        
                        Forms\Components\TextInput::make('region')
                            ->maxLength(100)
                            ->label('Región')
                            ->placeholder('Región, provincia o estado...'),
                        
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20)
                            ->label('Código Postal'),
                        
                        Forms\Components\TextInput::make('coordinates')
                            ->maxLength(100)
                            ->label('Coordenadas')
                            ->placeholder('Latitud, Longitud...'),
                        
                        Forms\Components\Select::make('coverage_area')
                            ->options([
                                'local' => '🏠 Local',
                                'regional' => '🏘️ Regional',
                                'national' => '🏳️ Nacional',
                                'continental' => '🌎 Continental',
                                'global' => '🌍 Global',
                            ])
                            ->label('Área de Cobertura'),
                    ])->columns(2),

                Forms\Components\Section::make('Precios y Tarifas')
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->label('Precio Base')
                            ->helperText('Precio base del servicio'),
                        
                        Forms\Components\Select::make('price_unit')
                            ->options([
                                '€/kWh' => '€/kWh (Euro por kilovatio-hora)',
                                '€/MWh' => '€/MWh (Euro por megavatio-hora)',
                                '€/m³' => '€/m³ (Euro por metro cúbico)',
                                '€/l' => '€/l (Euro por litro)',
                                '€/kg' => '€/kg (Euro por kilogramo)',
                                '€/hora' => '€/hora (Euro por hora)',
                                '€/mes' => '€/mes (Euro por mes)',
                                '€/año' => '€/año (Euro por año)',
                                '€/servicio' => '€/servicio (Euro por servicio)',
                                'other' => 'Otro',
                            ])
                            ->label('Unidad de Precio'),
                        
                        Forms\Components\Toggle::make('has_fixed_price')
                            ->label('Precio Fijo')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_variable_price')
                            ->label('Precio Variable')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('price_variability')
                            ->maxLength(100)
                            ->label('Variabilidad del Precio')
                            ->placeholder('Horaria, estacional, por demanda...'),
                        
                        Forms\Components\KeyValue::make('price_tiers')
                            ->label('Niveles de Precio')
                            ->keyLabel('Nivel')
                            ->valueLabel('Precio')
                            ->addActionLabel('Agregar Nivel'),
                    ])->columns(2),

                Forms\Components\Section::make('Disponibilidad y Horarios')
                    ->schema([
                        Forms\Components\Toggle::make('is_available_24_7')
                            ->label('Disponible 24/7')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('business_hours')
                            ->maxLength(255)
                            ->label('Horario Comercial')
                            ->placeholder('L-V 9:00-18:00, S 9:00-14:00...'),
                        
                        Forms\Components\TextInput::make('response_time')
                            ->maxLength(100)
                            ->label('Tiempo de Respuesta')
                            ->placeholder('2 horas, 24 horas, inmediato...'),
                        
                        Forms\Components\Select::make('availability_status')
                            ->options([
                                'available' => '🟢 Disponible',
                                'limited' => '🟡 Limitado',
                                'unavailable' => '🔴 No Disponible',
                                'maintenance' => '🔧 En Mantenimiento',
                                'planned_outage' => '📅 Corte Programado',
                                'emergency_outage' => '🚨 Corte de Emergencia',
                            ])
                            ->default('available')
                            ->label('Estado de Disponibilidad'),
                    ])->columns(2),

                Forms\Components\Section::make('Certificaciones y Calidad')
                    ->schema([
                        Forms\Components\Toggle::make('is_certified')
                            ->label('Certificado')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('certification_body')
                            ->maxLength(255)
                            ->label('Organismo Certificador')
                            ->placeholder('Nombre del organismo...'),
                        
                        Forms\Components\TextInput::make('certification_number')
                            ->maxLength(100)
                            ->label('Número de Certificación')
                            ->placeholder('Número de certificado...'),
                        
                        Forms\Components\DatePicker::make('certification_date')
                            ->label('Fecha de Certificación')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\DatePicker::make('certification_expiry')
                            ->label('Vencimiento de Certificación')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\Select::make('quality_rating')
                            ->options([
                                'excellent' => '🟢 Excelente (5/5)',
                                'very_good' => '🟢 Muy Bueno (4/5)',
                                'good' => '🟡 Bueno (3/5)',
                                'fair' => '🟠 Regular (2/5)',
                                'poor' => '🔴 Pobre (1/5)',
                                'not_rated' => '⚫ No Evaluado',
                            ])
                            ->label('Calificación de Calidad'),
                    ])->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\KeyValue::make('additional_features')
                            ->label('Características Adicionales')
                            ->keyLabel('Característica')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Característica'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas adicionales o comentarios...'),
                        
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Campo'),
                    ])->columns(1),

                Forms\Components\Section::make('Estado del Servicio')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Indica si el servicio está activo'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Servicio importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_premium')
                            ->label('Premium')
                            ->default(false)
                            ->helperText('Servicio de alta calidad'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '✅ Activo',
                                'inactive' => '❌ Inactivo',
                                'maintenance' => '🔧 En Mantenimiento',
                                'discontinued' => '🚫 Discontinuado',
                                'beta' => '🧪 Beta',
                                'deprecated' => '⚠️ Deprecado',
                            ])
                            ->default('active')
                            ->label('Estado'),
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
                
                Tables\Columns\TextColumn::make('name')
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
                        'secondary' => 'maintenance',
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
                        'maintenance' => '🔧 Mantenimiento',
                        'consulting' => '💼 Consultoría',
                        'installation' => '🔌 Instalación',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'generation',
                        'success' => 'distribution',
                        'warning' => 'transmission',
                        'info' => 'retail',
                        'danger' => 'maintenance',
                        'secondary' => 'consulting',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'generation' => '⚡ Generación',
                        'distribution' => '📡 Distribución',
                        'transmission' => '🔌 Transmisión',
                        'retail' => '🏪 Comercialización',
                        'maintenance' => '🔧 Mantenimiento',
                        'consulting' => '💼 Consultoría',
                        'installation' => '🔌 Instalación',
                        'monitoring' => '📊 Monitoreo',
                        'storage' => '🔋 Almacenamiento',
                        'efficiency' => '💡 Eficiencia',
                        'renewable' => '🌱 Renovable',
                        'conventional' => '🛢️ Convencional',
                        'hybrid' => '🔄 Híbrido',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('provider_name')
                    ->label('Proveedor')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('location')
                    ->label('Ubicación')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacidad')
                    ->numeric()
                    ->suffix(fn ($record): string => $record->capacity_unit ?? 'MW')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('efficiency')
                    ->label('Eficiencia')
                    ->numeric()
                    ->suffix('%')
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        $state >= 60 => 'danger',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Precio Base')
                    ->money('EUR')
                    ->suffix(fn ($record): string => '/' . ($record->price_unit ?? 'kWh'))
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state < 0.10 => 'success',
                        $state < 0.20 => 'info',
                        $state < 0.50 => 'warning',
                        $state < 1.00 => 'danger',
                        default => 'dark',
                    }),
                
                Tables\Columns\BadgeColumn::make('availability_status')
                    ->label('Disponibilidad')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'limited',
                        'danger' => 'unavailable',
                        'info' => 'maintenance',
                        'secondary' => 'planned_outage',
                        'dark' => 'emergency_outage',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => '🟢 Disponible',
                        'limited' => '🟡 Limitado',
                        'unavailable' => '🔴 No Disponible',
                        'maintenance' => '🔧 Mantenimiento',
                        'planned_outage' => '📅 Corte Programado',
                        'emergency_outage' => '🚨 Corte Emergencia',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_certified')
                    ->label('Certificado')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                
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
                        'warning' => 'maintenance',
                        'secondary' => 'discontinued',
                        'info' => 'beta',
                        'dark' => 'deprecated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'maintenance' => '🔧 Mantenimiento',
                        'discontinued' => '🚫 Discontinuado',
                        'beta' => '🧪 Beta',
                        'deprecated' => '⚠️ Deprecado',
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
                        'maintenance' => '🔧 Mantenimiento',
                        'consulting' => '💼 Consultoría',
                        'installation' => '🔌 Instalación',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Servicio'),
                
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'generation' => '⚡ Generación',
                        'distribution' => '📡 Distribución',
                        'transmission' => '🔌 Transmisión',
                        'retail' => '🏪 Comercialización',
                        'maintenance' => '🔧 Mantenimiento',
                        'consulting' => '💼 Consultoría',
                        'installation' => '🔌 Instalación',
                        'monitoring' => '📊 Monitoreo',
                        'storage' => '🔋 Almacenamiento',
                        'efficiency' => '💡 Eficiencia',
                        'renewable' => '🌱 Renovable',
                        'conventional' => '🛢️ Convencional',
                        'hybrid' => '🔄 Híbrido',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría'),
                
                Tables\Filters\SelectFilter::make('availability_status')
                    ->options([
                        'available' => '🟢 Disponible',
                        'limited' => '🟡 Limitado',
                        'unavailable' => '🔴 No Disponible',
                        'maintenance' => '🔧 En Mantenimiento',
                        'planned_outage' => '📅 Corte Programado',
                        'emergency_outage' => '🚨 Corte de Emergencia',
                    ])
                    ->label('Estado de Disponibilidad'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'maintenance' => '🔧 En Mantenimiento',
                        'discontinued' => '🚫 Discontinuado',
                        'beta' => '🧪 Beta',
                        'deprecated' => '⚠️ Deprecado',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                
                Tables\Filters\Filter::make('certified_only')
                    ->label('Solo Certificados')
                    ->query(fn (Builder $query): Builder => $query->where('is_certified', true)),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('premium_only')
                    ->label('Solo Premium')
                    ->query(fn (Builder $query): Builder => $query->where('is_premium', true)),
                
                Tables\Filters\Filter::make('high_efficiency')
                    ->label('Alta Eficiencia')
                    ->query(fn (Builder $query): Builder => $query->where('efficiency', '>=', 90)),
                
                Tables\Filters\Filter::make('low_price')
                    ->label('Precios Bajos')
                    ->query(fn (Builder $query): Builder => $query->where('base_price', '<=', 0.20)),
                
                Tables\Filters\Filter::make('renewable_energy')
                    ->label('Energía Renovable')
                    ->query(fn (Builder $query): Builder => $query->whereIn('service_type', ['solar', 'wind', 'hydro', 'biomass', 'geothermal'])),
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
                    ->label('Visitar Web')
                    ->icon('fas-external-link-alt')
                    ->url(fn ($record): string => $record->website)
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => !empty($record->website))
                    ->color('primary'),
                
                Tables\Actions\Action::make('contact_provider')
                    ->label('Contactar')
                    ->icon('fas-phone')
                    ->url(fn ($record): string => "tel:{$record->phone}")
                    ->visible(fn ($record): bool => !empty($record->phone))
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_active' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar')
                        ->icon('fas-times')
                        ->action(function ($records): void {
                            $records->each->update(['is_active' => false]);
                        })
                        ->color('danger'),
                    
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
                ]),
            ])
            ->defaultSort('name', 'asc')
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
