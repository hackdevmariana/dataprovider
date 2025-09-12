<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyCertificationResource\Pages;
use App\Filament\Resources\CompanyCertificationResource\RelationManagers;
use App\Models\CompanyCertification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyCertificationResource extends Resource
{
    protected static ?string $model = CompanyCertification::class;

    protected static ?string $navigationIcon = 'fas-certificate';

    protected static ?string $navigationGroup = 'Empresas y Certificaciones';

    protected static ?string $navigationLabel = 'Certificaciones Empresariales';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Certificación Empresarial';

    protected static ?string $pluralModelLabel = 'Certificaciones Empresariales';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('certification_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Certificación')
                            ->placeholder('Nombre oficial de la certificación...'),
                        
                        Forms\Components\TextInput::make('certification_code')
                            ->maxLength(100)
                            ->label('Código de Certificación')
                            ->placeholder('Código único de la certificación...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada de la certificación...'),
                        
                        Forms\Components\Select::make('certification_type')
                            ->options([
                                'quality' => '🏆 Calidad',
                                'environmental' => '🌱 Ambiental',
                                'safety' => '🛡️ Seguridad',
                                'information_security' => '🔒 Seguridad Informática',
                                'food_safety' => '🍽️ Seguridad Alimentaria',
                                'occupational_health' => '👷 Salud Ocupacional',
                                'energy_efficiency' => '💡 Eficiencia Energética',
                                'sustainability' => '♻️ Sostenibilidad',
                                'social_responsibility' => '🤝 Responsabilidad Social',
                                'financial' => '💰 Financiera',
                                'technical' => '⚙️ Técnica',
                                'management' => '📊 Gestión',
                                'compliance' => '✅ Cumplimiento',
                                'accreditation' => '🎖️ Acreditación',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Certificación'),
                        
                        Forms\Components\Select::make('industry_sector')
                            ->options([
                                'manufacturing' => '🏭 Manufactura',
                                'construction' => '🏗️ Construcción',
                                'energy' => '⚡ Energía',
                                'healthcare' => '🏥 Salud',
                                'food_beverage' => '🍽️ Alimentación y Bebidas',
                                'automotive' => '🚗 Automotriz',
                                'aerospace' => '✈️ Aeroespacial',
                                'technology' => '💻 Tecnología',
                                'finance' => '🏦 Finanzas',
                                'retail' => '🏪 Comercio Minorista',
                                'logistics' => '📦 Logística',
                                'education' => '🎓 Educación',
                                'government' => '🏛️ Gobierno',
                                'non_profit' => '🤝 Sin Fines de Lucro',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Sector Industrial'),
                    ])->columns(2),

                Forms\Components\Section::make('Organismo Certificador')
                    ->schema([
                        Forms\Components\TextInput::make('certifying_body')
                            ->required()
                            ->maxLength(255)
                            ->label('Organismo Certificador')
                            ->placeholder('Nombre del organismo que otorga la certificación...'),
                        
                        Forms\Components\TextInput::make('certifying_body_code')
                            ->maxLength(100)
                            ->label('Código del Organismo')
                            ->placeholder('Código único del organismo...'),
                        
                        Forms\Components\TextInput::make('accreditation_number')
                            ->maxLength(100)
                            ->label('Número de Acreditación')
                            ->placeholder('Número de acreditación del organismo...'),
                        
                        Forms\Components\TextInput::make('certifying_body_website')
                            ->maxLength(500)
                            ->label('Sitio Web del Organismo')
                            ->url()
                            ->placeholder('https://www.ejemplo.com'),
                        
                        Forms\Components\TextInput::make('certifying_body_phone')
                            ->tel()
                            ->maxLength(50)
                            ->label('Teléfono del Organismo'),
                        
                        Forms\Components\TextInput::make('certifying_body_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email del Organismo'),
                    ])->columns(2),

                Forms\Components\Section::make('Empresa Certificada')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Empresa')
                            ->placeholder('Nombre de la empresa certificada...'),
                        
                        Forms\Components\TextInput::make('company_registration')
                            ->maxLength(100)
                            ->label('Registro Mercantil')
                            ->placeholder('Número de registro mercantil...'),
                        
                        Forms\Components\TextInput::make('tax_id')
                            ->maxLength(100)
                            ->label('CIF/NIF')
                            ->placeholder('Identificación fiscal de la empresa...'),
                        
                        Forms\Components\TextInput::make('company_website')
                            ->maxLength(500)
                            ->label('Sitio Web de la Empresa')
                            ->url()
                            ->placeholder('https://www.ejemplo.com'),
                        
                        Forms\Components\TextInput::make('company_phone')
                            ->tel()
                            ->maxLength(50)
                            ->label('Teléfono de la Empresa'),
                        
                        Forms\Components\TextInput::make('company_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email de la Empresa'),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas de Certificación')
                    ->schema([
                        Forms\Components\DatePicker::make('certification_date')
                            ->required()
                            ->label('Fecha de Certificación')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha cuando se otorgó la certificación'),
                        
                        Forms\Components\DatePicker::make('expiry_date')
                            ->required()
                            ->label('Fecha de Vencimiento')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha cuando expira la certificación'),
                        
                        Forms\Components\DatePicker::make('renewal_date')
                            ->label('Fecha de Renovación')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha de la última renovación'),
                        
                        Forms\Components\DatePicker::make('next_audit_date')
                            ->label('Próxima Auditoría')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha de la próxima auditoría programada'),
                        
                        Forms\Components\TextInput::make('validity_period')
                            ->maxLength(100)
                            ->label('Período de Validez')
                            ->placeholder('3 años, 5 años, indefinido...'),
                    ])->columns(2),

                Forms\Components\Section::make('Alcance y Cobertura')
                    ->schema([
                        Forms\Components\Textarea::make('scope')
                            ->required()
                            ->maxLength(1000)
                            ->label('Alcance de la Certificación')
                            ->rows(3)
                            ->placeholder('Descripción del alcance de la certificación...'),
                        
                        Forms\Components\TextInput::make('coverage_area')
                            ->maxLength(255)
                            ->label('Área de Cobertura')
                            ->placeholder('Local, regional, nacional, internacional...'),
                        
                        Forms\Components\TextInput::make('facilities_covered')
                            ->maxLength(500)
                            ->label('Instalaciones Cubiertas')
                            ->placeholder('Plantas, oficinas, centros de distribución...'),
                        
                        Forms\Components\TextInput::make('products_services_covered')
                            ->maxLength(500)
                            ->label('Productos/Servicios Cubiertos')
                            ->placeholder('Productos o servicios específicos...'),
                        
                        Forms\Components\TextInput::make('processes_covered')
                            ->maxLength(500)
                            ->label('Procesos Cubiertos')
                            ->placeholder('Procesos de producción, gestión, etc...'),
                    ])->columns(1),

                Forms\Components\Section::make('Estándares y Normas')
                    ->schema([
                        Forms\Components\TextInput::make('standard_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Estándar')
                            ->placeholder('ISO 9001, ISO 14001, OHSAS 18001...'),
                        
                        Forms\Components\TextInput::make('standard_version')
                            ->maxLength(50)
                            ->label('Versión del Estándar')
                            ->placeholder('2015, 2018, 2021...'),
                        
                        Forms\Components\TextInput::make('standard_edition')
                            ->maxLength(100)
                            ->label('Edición del Estándar')
                            ->placeholder('Primera edición, revisión...'),
                        
                        Forms\Components\Textarea::make('standard_description')
                            ->maxLength(500)
                            ->label('Descripción del Estándar')
                            ->rows(2)
                            ->placeholder('Breve descripción del estándar...'),
                        
                        Forms\Components\KeyValue::make('additional_standards')
                            ->label('Estándares Adicionales')
                            ->keyLabel('Estándar')
                            ->valueLabel('Versión')
                            ->addActionLabel('Agregar Estándar'),
                    ])->columns(2),

                Forms\Components\Section::make('Auditoría y Cumplimiento')
                    ->schema([
                        Forms\Components\Select::make('audit_type')
                            ->options([
                                'initial' => '🎯 Inicial',
                                'surveillance' => '👁️ Vigilancia',
                                'renewal' => '🔄 Renovación',
                                'follow_up' => '📋 Seguimiento',
                                'special' => '⭐ Especial',
                                'other' => '❓ Otro',
                            ])
                            ->label('Tipo de Auditoría'),
                        
                        Forms\Components\TextInput::make('auditor_name')
                            ->maxLength(255)
                            ->label('Nombre del Auditor')
                            ->placeholder('Nombre del auditor principal...'),
                        
                        Forms\Components\TextInput::make('audit_duration')
                            ->maxLength(100)
                            ->label('Duración de la Auditoría')
                            ->placeholder('3 días, 1 semana...'),
                        
                        Forms\Components\Select::make('compliance_level')
                            ->options([
                                'full' => '🟢 Cumplimiento Total',
                                'major' => '🟡 Cumplimiento Mayor',
                                'minor' => '🟠 Cumplimiento Menor',
                                'non_compliant' => '🔴 No Cumple',
                                'conditional' => '🟣 Condicional',
                                'not_assessed' => '⚫ No Evaluado',
                            ])
                            ->label('Nivel de Cumplimiento'),
                        
                        Forms\Components\TextInput::make('non_conformities')
                            ->maxLength(100)
                            ->label('No Conformidades')
                            ->placeholder('Número de no conformidades encontradas...'),
                        
                        Forms\Components\Textarea::make('corrective_actions')
                            ->maxLength(500)
                            ->label('Acciones Correctivas')
                            ->rows(2)
                            ->placeholder('Acciones correctivas implementadas...'),
                    ])->columns(2),

                Forms\Components\Section::make('Beneficios y Resultados')
                    ->schema([
                        Forms\Components\Textarea::make('benefits')
                            ->maxLength(1000)
                            ->label('Beneficios Obtenidos')
                            ->rows(3)
                            ->placeholder('Beneficios de la certificación...'),
                        
                        Forms\Components\TextInput::make('performance_improvement')
                            ->maxLength(255)
                            ->label('Mejora del Rendimiento')
                            ->placeholder('Porcentaje de mejora o métricas...'),
                        
                        Forms\Components\TextInput::make('cost_savings')
                            ->maxLength(255)
                            ->label('Ahorro de Costos')
                            ->placeholder('Ahorros estimados o reales...'),
                        
                        Forms\Components\TextInput::make('market_advantage')
                            ->maxLength(255)
                            ->label('Ventaja Competitiva')
                            ->placeholder('Ventajas en el mercado...'),
                        
                        Forms\Components\KeyValue::make('key_metrics')
                            ->label('Métricas Clave')
                            ->keyLabel('Métrica')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Métrica'),
                    ])->columns(1),

                Forms\Components\Section::make('Documentación')
                    ->schema([
                        Forms\Components\TextInput::make('certificate_number')
                            ->maxLength(100)
                            ->label('Número de Certificado')
                            ->placeholder('Número oficial del certificado...'),
                        
                        Forms\Components\TextInput::make('document_reference')
                            ->maxLength(100)
                            ->label('Referencia del Documento')
                            ->placeholder('Referencia interna del documento...'),
                        
                        Forms\Components\Toggle::make('has_certificate_copy')
                            ->label('Copia del Certificado Disponible')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_audit_reports')
                            ->label('Informes de Auditoría Disponibles')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_procedures_documentation')
                            ->label('Documentación de Procedimientos')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('documentation_location')
                            ->maxLength(255)
                            ->label('Ubicación de la Documentación')
                            ->placeholder('Archivo, sistema digital, etc...'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Seguimiento')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '✅ Activa',
                                'expired' => '❌ Expirada',
                                'suspended' => '⏸️ Suspendida',
                                'withdrawn' => '🚫 Retirada',
                                'under_review' => '👀 En Revisión',
                                'pending_renewal' => '⏳ Pendiente de Renovación',
                                'conditional' => '🟣 Condicional',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('active')
                            ->label('Estado de la Certificación'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Certificación importante para destacar'),
                        
                        Forms\Components\Toggle::make('requires_renewal')
                            ->label('Requiere Renovación')
                            ->default(false)
                            ->helperText('Indica si la certificación requiere renovación'),
                        
                        Forms\Components\Toggle::make('is_compliant')
                            ->label('Cumple Requisitos')
                            ->default(true)
                            ->helperText('Indica si cumple con todos los requisitos'),
                        
                        Forms\Components\TextInput::make('risk_level')
                            ->maxLength(100)
                            ->label('Nivel de Riesgo')
                            ->placeholder('Bajo, medio, alto...'),
                        
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
                
                Tables\Columns\TextColumn::make('certification_name')
                    ->label('Certificación')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('certification_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'quality',
                        'success' => 'environmental',
                        'warning' => 'safety',
                        'info' => 'information_security',
                        'danger' => 'food_safety',
                        'secondary' => 'occupational_health',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'quality' => '🏆 Calidad',
                        'environmental' => '🌱 Ambiental',
                        'safety' => '🛡️ Seguridad',
                        'information_security' => '🔒 Seguridad Informática',
                        'food_safety' => '🍽️ Seguridad Alimentaria',
                        'occupational_health' => '👷 Salud Ocupacional',
                        'energy_efficiency' => '💡 Eficiencia Energética',
                        'sustainability' => '♻️ Sostenibilidad',
                        'social_responsibility' => '🤝 Responsabilidad Social',
                        'financial' => '💰 Financiera',
                        'technical' => '⚙️ Técnica',
                        'management' => '📊 Gestión',
                        'compliance' => '✅ Cumplimiento',
                        'accreditation' => '🎖️ Acreditación',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Empresa')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('certifying_body')
                    ->label('Organismo')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('standard_name')
                    ->label('Estándar')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('certification_date')
                    ->label('Certificación')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->expiry_date && $record->expiry_date->isPast() ? 'danger' : 
                        ($record->expiry_date && $record->expiry_date->diffInDays(now()) <= 90 ? 'warning' : 'success')
                    ),
                
                Tables\Columns\TextColumn::make('days_until_expiry')
                    ->label('Días Restantes')
                    ->formatStateUsing(function ($record) {
                        if (!$record->expiry_date) return 'N/A';
                        $days = $record->expiry_date->diffInDays(now(), false);
                        return $days > 0 ? "+{$days}" : abs($days);
                    })
                    ->color(function ($record) {
                        if (!$record->expiry_date) return 'success';
                        return $record->expiry_date->isPast() ? 'danger' : 
                               ($record->expiry_date->diffInDays(now()) <= 90 ? 'warning' : 'success');
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'expired',
                        'warning' => 'suspended',
                        'secondary' => 'withdrawn',
                        'info' => 'under_review',
                        'primary' => 'pending_renewal',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '✅ Activa',
                        'expired' => '❌ Expirada',
                        'suspended' => '⏸️ Suspendida',
                        'withdrawn' => '🚫 Retirada',
                        'under_review' => '👀 En Revisión',
                        'pending_renewal' => '⏳ Pendiente Renovación',
                        'conditional' => '🟣 Condicional',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('compliance_level')
                    ->label('Cumplimiento')
                    ->colors([
                        'success' => 'full',
                        'warning' => 'major',
                        'info' => 'minor',
                        'danger' => 'non_compliant',
                        'secondary' => 'conditional',
                        'dark' => 'not_assessed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'full' => '🟢 Total',
                        'major' => '🟡 Mayor',
                        'minor' => '🟠 Menor',
                        'non_compliant' => '🔴 No Cumple',
                        'conditional' => '🟣 Condicional',
                        'not_assessed' => '⚫ No Evaluado',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('requires_renewal')
                    ->label('Requiere Renovación')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success'),
                
                Tables\Columns\IconColumn::make('is_compliant')
                    ->label('Cumple')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('certification_type')
                    ->options([
                        'quality' => '🏆 Calidad',
                        'environmental' => '🌱 Ambiental',
                        'safety' => '🛡️ Seguridad',
                        'information_security' => '🔒 Seguridad Informática',
                        'food_safety' => '🍽️ Seguridad Alimentaria',
                        'occupational_health' => '👷 Salud Ocupacional',
                        'energy_efficiency' => '💡 Eficiencia Energética',
                        'sustainability' => '♻️ Sostenibilidad',
                        'social_responsibility' => '🤝 Responsabilidad Social',
                        'financial' => '💰 Financiera',
                        'technical' => '⚙️ Técnica',
                        'management' => '📊 Gestión',
                        'compliance' => '✅ Cumplimiento',
                        'accreditation' => '🎖️ Acreditación',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Certificación'),
                
                Tables\Filters\SelectFilter::make('industry_sector')
                    ->options([
                        'manufacturing' => '🏭 Manufactura',
                        'construction' => '🏗️ Construcción',
                        'energy' => '⚡ Energía',
                        'healthcare' => '🏥 Salud',
                        'food_beverage' => '🍽️ Alimentación y Bebidas',
                        'automotive' => '🚗 Automotriz',
                        'aerospace' => '✈️ Aeroespacial',
                        'technology' => '💻 Tecnología',
                        'finance' => '🏦 Finanzas',
                        'retail' => '🏪 Comercio Minorista',
                        'logistics' => '📦 Logística',
                        'education' => '🎓 Educación',
                        'government' => '🏛️ Gobierno',
                        'non_profit' => '🤝 Sin Fines de Lucro',
                        'other' => '❓ Otro',
                    ])
                    ->label('Sector Industrial'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '✅ Activa',
                        'expired' => '❌ Expirada',
                        'suspended' => '⏸️ Suspendida',
                        'withdrawn' => '🚫 Retirada',
                        'under_review' => '👀 En Revisión',
                        'pending_renewal' => '⏳ Pendiente de Renovación',
                        'conditional' => '🟣 Condicional',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\SelectFilter::make('compliance_level')
                    ->options([
                        'full' => '🟢 Cumplimiento Total',
                        'major' => '🟡 Cumplimiento Mayor',
                        'minor' => '🟠 Cumplimiento Menor',
                        'non_compliant' => '🔴 No Cumple',
                        'conditional' => '🟣 Condicional',
                        'not_assessed' => '⚫ No Evaluado',
                    ])
                    ->label('Nivel de Cumplimiento'),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('expired_only')
                    ->label('Solo Expiradas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'expired')),
                
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiran Pronto (90 días)')
                    ->query(fn (Builder $query): Builder => $query->where('expiry_date', '<=', now()->addDays(90))->where('status', 'active')),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('requires_renewal')
                    ->label('Requieren Renovación')
                    ->query(fn (Builder $query): Builder => $query->where('requires_renewal', true)),
                
                Tables\Filters\Filter::make('compliant_only')
                    ->label('Solo Cumplen')
                    ->query(fn (Builder $query): Builder => $query->where('is_compliant', true)),
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
                
                Tables\Actions\Action::make('mark_renewal_required')
                    ->label('Marcar Renovación Requerida')
                    ->icon('fas-clock')
                    ->action(function ($record): void {
                        $record->update(['requires_renewal' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->requires_renewal)
                    ->color('warning'),
                
                Tables\Actions\Action::make('visit_certifying_body')
                    ->label('Visitar Organismo')
                    ->icon('fas-external-link-alt')
                    ->url(fn ($record): string => $record->certifying_body_website)
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => !empty($record->certifying_body_website))
                    ->color('primary'),
                
                Tables\Actions\Action::make('contact_certifying_body')
                    ->label('Contactar Organismo')
                    ->icon('fas-phone')
                    ->url(fn ($record): string => "tel:{$record->certifying_body_phone}")
                    ->visible(fn ($record): bool => !empty($record->certifying_body_phone))
                    ->color('success'),
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
                    
                    Tables\Actions\BulkAction::make('mark_renewal_required')
                        ->label('Marcar Renovación Requerida')
                        ->icon('fas-clock')
                        ->action(function ($records): void {
                            $records->each->update(['requires_renewal' => true]);
                        })
                        ->color('warning'),
                    
                    Tables\Actions\BulkAction::make('mark_compliant')
                        ->label('Marcar como Cumplen')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_compliant' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_non_compliant')
                        ->label('Marcar como No Cumplen')
                        ->icon('fas-times')
                        ->action(function ($records): void {
                            $records->each->update(['is_compliant' => false]);
                        })
                        ->color('danger'),
                ]),
            ])
            ->defaultSort('expiry_date', 'asc')
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
            'index' => Pages\ListCompanyCertifications::route('/'),
            'create' => Pages\CreateCompanyCertification::route('/create'),
            'view' => Pages\ViewCompanyCertification::route('/{record}'),
            'edit' => Pages\EditCompanyCertification::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
