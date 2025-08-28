<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FestivalScheduleResource\Pages;
use App\Models\FestivalSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FestivalScheduleResource extends Resource
{
    protected static ?string $model = FestivalSchedule::class;

    protected static ?string $navigationIcon = 'fas-calendar-day';

    protected static ?string $navigationGroup = 'Festivales y Eventos';

    protected static ?string $navigationLabel = 'Horarios de Festivales';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Horario de Festival';

    protected static ?string $pluralModelLabel = 'Horarios de Festivales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('schedule_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Horario')
                            ->placeholder('Nombre del horario...'),
                        
                        Forms\Components\TextInput::make('schedule_code')
                            ->maxLength(100)
                            ->label('Código del Horario')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción del horario...'),
                        
                        Forms\Components\Select::make('festival_program_id')
                            ->relationship('festivalProgram', 'program_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Programa del Festival')
                            ->placeholder('Selecciona el programa...'),
                        
                        Forms\Components\Select::make('schedule_type')
                            ->options([
                                'daily' => '📅 Diario',
                                'weekly' => '📅 Semanal',
                                'monthly' => '📅 Mensual',
                                'event_based' => '🎯 Basado en Eventos',
                                'time_slot' => '⏰ Por Franjas Horarias',
                                'venue_based' => '🏢 Basado en Venues',
                                'category_based' => '🏷️ Basado en Categorías',
                                'custom' => '⚙️ Personalizado',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Horario'),
                        
                        Forms\Components\Select::make('timezone')
                            ->options([
                                'UTC' => '🌍 UTC',
                                'Europe/Madrid' => '🇪🇸 Madrid (CET/CEST)',
                                'Europe/London' => '🇬🇧 Londres (GMT/BST)',
                                'Europe/Paris' => '🇫🇷 París (CET/CEST)',
                                'Europe/Berlin' => '🇩🇪 Berlín (CET/CEST)',
                                'America/New_York' => '🇺🇸 Nueva York (EST/EDT)',
                                'America/Los_Angeles' => '🇺🇸 Los Ángeles (PST/PDT)',
                                'Asia/Tokyo' => '🇯🇵 Tokio (JST)',
                                'Asia/Shanghai' => '🇨🇳 Shanghái (CST)',
                                'Australia/Sydney' => '🇦🇺 Sídney (AEST/AEDT)',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('Europe/Madrid')
                            ->label('Zona Horaria'),
                        
                        Forms\Components\Toggle::make('is_master_schedule')
                            ->label('Es Horario Maestro')
                            ->default(false)
                            ->helperText('Horario principal del festival'),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Es Público')
                            ->default(true)
                            ->helperText('El horario es visible públicamente'),
                    ])->columns(2),

                Forms\Components\Section::make('Período y Duración')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->label('Fecha de Inicio')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha de inicio del horario'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->label('Fecha de Fin')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha de fin del horario'),
                        
                        Forms\Components\TextInput::make('total_days')
                            ->numeric()
                            ->label('Total de Días')
                            ->placeholder('Número total de días...')
                            ->disabled()
                            ->helperText('Calculado automáticamente'),
                        
                        Forms\Components\TimePicker::make('daily_start_time')
                            ->label('Hora de Inicio Diario')
                            ->displayFormat('H:i')
                            ->helperText('Hora de inicio de cada día'),
                        
                        Forms\Components\TimePicker::make('daily_end_time')
                            ->label('Hora de Fin Diario')
                            ->displayFormat('H:i')
                            ->helperText('Hora de fin de cada día'),
                        
                        Forms\Components\TextInput::make('daily_hours')
                            ->numeric()
                            ->label('Horas Diarias')
                            ->placeholder('Número de horas por día...')
                            ->disabled()
                            ->helperText('Calculado automáticamente'),
                        
                        Forms\Components\Toggle::make('includes_weekends')
                            ->label('Incluye Fines de Semana')
                            ->default(true)
                            ->helperText('El horario incluye sábados y domingos'),
                        
                        Forms\Components\Toggle::make('includes_holidays')
                            ->label('Incluye Festivos')
                            ->default(false)
                            ->helperText('El horario incluye días festivos'),
                        
                        Forms\Components\Textarea::make('excluded_dates')
                            ->maxLength(500)
                            ->label('Fechas Excluidas')
                            ->rows(2)
                            ->placeholder('Fechas específicas excluidas...'),
                    ])->columns(2),

                Forms\Components\Section::make('Estructura del Horario')
                    ->schema([
                        Forms\Components\Select::make('time_slot_duration')
                            ->options([
                                15 => '15 minutos',
                                30 => '30 minutos',
                                45 => '45 minutos',
                                60 => '1 hora',
                                90 => '1 hora 30 minutos',
                                120 => '2 horas',
                                180 => '3 horas',
                                240 => '4 horas',
                                480 => '8 horas',
                                'custom' => 'Personalizado',
                                'other' => 'Otro',
                            ])
                            ->required()
                            ->default(60)
                            ->label('Duración de Franjas Horarias'),
                        
                        Forms\Components\TextInput::make('custom_time_slot')
                            ->numeric()
                            ->label('Franja Horaria Personalizada (minutos)')
                            ->placeholder('Duración en minutos...')
                            ->visible(fn (Forms\Get $get): bool => $get('time_slot_duration') === 'custom'),
                        
                        Forms\Components\Toggle::make('has_breaks')
                            ->label('Tiene Pausas')
                            ->default(true)
                            ->helperText('El horario incluye pausas'),
                        
                        Forms\Components\TextInput::make('break_duration')
                            ->numeric()
                            ->label('Duración de Pausas (minutos)')
                            ->placeholder('Duración de las pausas...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_breaks')),
                        
                        Forms\Components\TextInput::make('break_frequency')
                            ->numeric()
                            ->label('Frecuencia de Pausas (horas)')
                            ->placeholder('Cada cuántas horas...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_breaks')),
                        
                        Forms\Components\Toggle::make('has_lunch_break')
                            ->label('Tiene Pausa para Almuerzo')
                            ->default(true)
                            ->helperText('Incluye pausa para almuerzo'),
                        
                        Forms\Components\TimePicker::make('lunch_start_time')
                            ->label('Inicio de Almuerzo')
                            ->displayFormat('H:i')
                            ->visible(fn (Forms\Get $get): bool => $get('has_lunch_break')),
                        
                        Forms\Components\TimePicker::make('lunch_end_time')
                            ->label('Fin de Almuerzo')
                            ->displayFormat('H:i')
                            ->visible(fn (Forms\Get $get): bool => $get('has_lunch_break')),
                        
                        Forms\Components\Toggle::make('has_dinner_break')
                            ->label('Tiene Pausa para Cena')
                            ->default(false)
                            ->helperText('Incluye pausa para cena'),
                        
                        Forms\Components\TimePicker::make('dinner_start_time')
                            ->label('Inicio de Cena')
                            ->displayFormat('H:i')
                            ->visible(fn (Forms\Get $get): bool => $get('has_dinner_break')),
                        
                        Forms\Components\TimePicker::make('dinner_end_time')
                            ->label('Fin de Cena')
                            ->displayFormat('H:i')
                            ->visible(fn (Forms\Get $get): bool => $get('has_dinner_break')),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración de Venues')
                    ->schema([
                        Forms\Components\Toggle::make('multi_venue')
                            ->label('Múltiples Venues')
                            ->default(false)
                            ->helperText('El horario incluye múltiples venues'),
                        
                        Forms\Components\Textarea::make('venues_included')
                            ->maxLength(500)
                            ->label('Venues Incluidos')
                            ->rows(2)
                            ->placeholder('Lista de venues incluidos...')
                            ->visible(fn (Forms\Get $get): bool => $get('multi_venue')),
                        
                        Forms\Components\Toggle::make('venue_specific_times')
                            ->label('Horarios Específicos por Venue')
                            ->default(false)
                            ->helperText('Cada venue tiene horarios específicos')
                            ->visible(fn (Forms\Get $get): bool => $get('multi_venue')),
                        
                        Forms\Components\Toggle::make('venue_rotation')
                            ->label('Rotación de Venues')
                            ->default(false)
                            ->helperText('Los eventos rotan entre venues')
                            ->visible(fn (Forms\Get $get): bool => $get('multi_venue')),
                        
                        Forms\Components\TextInput::make('max_venues_simultaneous')
                            ->numeric()
                            ->label('Máximo de Venues Simultáneos')
                            ->placeholder('Número máximo...')
                            ->visible(fn (Forms\Get $get): bool => $get('multi_venue')),
                        
                        Forms\Components\Toggle::make('venue_capacity_tracking')
                            ->label('Seguimiento de Capacidad por Venue')
                            ->default(false)
                            ->helperText('Controla la capacidad de cada venue'),
                        
                        Forms\Components\Toggle::make('venue_conflict_prevention')
                            ->label('Prevención de Conflictos de Venue')
                            ->default(true)
                            ->helperText('Evita conflictos de horarios en venues'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración de Eventos')
                    ->schema([
                        Forms\Components\Toggle::make('event_overlap_allowed')
                            ->label('Permite Superposición de Eventos')
                            ->default(false)
                            ->helperText('Permite que eventos se superpongan'),
                        
                        Forms\Components\TextInput::make('max_concurrent_events')
                            ->numeric()
                            ->label('Máximo de Eventos Simultáneos')
                            ->placeholder('Número máximo...'),
                        
                        Forms\Components\Toggle::make('event_dependencies')
                            ->label('Tiene Dependencias de Eventos')
                            ->default(false)
                            ->helperText('Algunos eventos dependen de otros'),
                        
                        Forms\Components\Textarea::make('dependency_rules')
                            ->maxLength(500)
                            ->label('Reglas de Dependencia')
                            ->rows(2)
                            ->placeholder('Reglas de dependencia entre eventos...')
                            ->visible(fn (Forms\Get $get): bool => $get('event_dependencies')),
                        
                        Forms\Components\Toggle::make('buffer_time')
                            ->label('Tiene Tiempo de Buffer')
                            ->default(true)
                            ->helperText('Incluye tiempo de transición entre eventos'),
                        
                        Forms\Components\TextInput::make('buffer_duration')
                            ->numeric()
                            ->label('Duración del Buffer (minutos)')
                            ->placeholder('Duración del buffer...')
                            ->visible(fn (Forms\Get $get): bool => $get('buffer_time')),
                        
                        Forms\Components\Toggle::make('setup_time')
                            ->label('Incluye Tiempo de Preparación')
                            ->default(true)
                            ->helperText('Incluye tiempo para preparar eventos'),
                        
                        Forms\Components\TextInput::make('setup_duration')
                            ->numeric()
                            ->label('Duración de Preparación (minutos)')
                            ->placeholder('Duración de preparación...')
                            ->visible(fn (Forms\Get $get): bool => $get('setup_time')),
                        
                        Forms\Components\Toggle::make('cleanup_time')
                            ->label('Incluye Tiempo de Limpieza')
                            ->default(true)
                            ->helperText('Incluye tiempo para limpiar después de eventos'),
                        
                        Forms\Components\TextInput::make('cleanup_duration')
                            ->numeric()
                            ->label('Duración de Limpieza (minutos)')
                            ->placeholder('Duración de limpieza...')
                            ->visible(fn (Forms\Get $get): bool => $get('cleanup_time')),
                    ])->columns(2),

                Forms\Components\Section::make('Personalización y Estilo')
                    ->schema([
                        Forms\Components\Select::make('display_format')
                            ->options([
                                'grid' => '📊 Cuadrícula',
                                'list' => '📋 Lista',
                                'timeline' => '⏰ Línea de Tiempo',
                                'calendar' => '📅 Calendario',
                                'gantt' => '📈 Gantt',
                                'kanban' => '📋 Kanban',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('grid')
                            ->label('Formato de Visualización'),
                        
                        Forms\Components\Toggle::make('color_coded')
                            ->label('Código de Colores')
                            ->default(true)
                            ->helperText('Usa colores para categorizar eventos'),
                        
                        Forms\Components\Toggle::make('show_times')
                            ->label('Muestra Horarios')
                            ->default(true)
                            ->helperText('Muestra horarios específicos'),
                        
                        Forms\Components\Toggle::make('show_durations')
                            ->label('Muestra Duración')
                            ->default(true)
                            ->helperText('Muestra duración de eventos'),
                        
                        Forms\Components\Toggle::make('show_venues')
                            ->label('Muestra Venues')
                            ->default(true)
                            ->helperText('Muestra venues de eventos'),
                        
                        Forms\Components\Toggle::make('show_categories')
                            ->label('Muestra Categorías')
                            ->default(true)
                            ->helperText('Muestra categorías de eventos'),
                        
                        Forms\Components\Toggle::make('show_participants')
                            ->label('Muestra Participantes')
                            ->default(false)
                            ->helperText('Muestra participantes de eventos'),
                        
                        Forms\Components\Toggle::make('show_capacity')
                            ->label('Muestra Capacidad')
                            ->default(false)
                            ->helperText('Muestra capacidad de eventos'),
                        
                        Forms\Components\Toggle::make('show_status')
                            ->label('Muestra Estado')
                            ->default(true)
                            ->helperText('Muestra estado de eventos'),
                        
                        Forms\Components\TextInput::make('refresh_interval')
                            ->numeric()
                            ->label('Intervalo de Actualización (segundos)')
                            ->placeholder('Cada cuántos segundos...')
                            ->default(300)
                            ->helperText('Frecuencia de actualización automática'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Calidad')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => '📝 Borrador',
                                'active' => '✅ Activo',
                                'inactive' => '❌ Inactivo',
                                'testing' => '🧪 En Pruebas',
                                'maintenance' => '🔧 En Mantenimiento',
                                'archived' => '📦 Archivado',
                                'deprecated' => '⚠️ Deprecado',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('draft')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Horario importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado')
                            ->default(false)
                            ->helperText('El horario ha sido verificado'),
                        
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Aprobado')
                            ->default(false)
                            ->helperText('El horario ha sido aprobado'),
                        
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
                        
                        Forms\Components\TextInput::make('reviewer')
                            ->maxLength(255)
                            ->label('Revisor')
                            ->placeholder('Persona que revisó el horario...'),
                        
                        Forms\Components\DatePicker::make('review_date')
                            ->label('Fecha de Revisión')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\TextInput::make('update_frequency')
                            ->maxLength(100)
                            ->label('Frecuencia de Actualización')
                            ->placeholder('Mensual, semanal, diario...'),
                        
                        Forms\Components\DatePicker::make('last_updated')
                            ->label('Última Actualización')
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
                
                Tables\Columns\TextColumn::make('schedule_name')
                    ->label('Horario')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('festivalProgram.program_name')
                    ->label('Programa')
                    ->searchable()
                    ->limit(30)
                    ->weight('medium')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('schedule_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'daily',
                        'success' => 'weekly',
                        'warning' => 'monthly',
                        'info' => 'event_based',
                        'danger' => 'time_slot',
                        'secondary' => 'venue_based',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'daily' => '📅 Diario',
                        'weekly' => '📅 Semanal',
                        'monthly' => '📅 Mensual',
                        'event_based' => '🎯 Basado en Eventos',
                        'time_slot' => '⏰ Por Franjas Horarias',
                        'venue_based' => '🏢 Basado en Venues',
                        'category_based' => '🏷️ Basado en Categorías',
                        'custom' => '⚙️ Personalizado',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('timezone')
                    ->label('Zona Horaria')
                    ->colors([
                        'success' => 'Europe/Madrid',
                        'primary' => 'UTC',
                        'info' => 'Europe/London',
                        'warning' => 'Europe/Paris',
                        'danger' => 'America/New_York',
                        'secondary' => 'Asia/Tokyo',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'UTC' => '🌍 UTC',
                        'Europe/Madrid' => '🇪🇸 Madrid',
                        'Europe/London' => '🇬🇧 Londres',
                        'Europe/Paris' => '🇫🇷 París',
                        'Europe/Berlin' => '🇩🇪 Berlín',
                        'America/New_York' => '🇺🇸 Nueva York',
                        'America/Los_Angeles' => '🇺🇸 Los Ángeles',
                        'Asia/Tokyo' => '🇯🇵 Tokio',
                        'Asia/Shanghai' => '🇨🇳 Shanghái',
                        'Australia/Sydney' => '🇦🇺 Sídney',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->end_date && $record->end_date->isPast() ? 'danger' : 
                        ($record->end_date && $record->end_date->diffInDays(now()) <= 7 ? 'warning' : 'success')
                    ),
                
                Tables\Columns\TextColumn::make('total_days')
                    ->label('Días')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 3 => 'success',
                        $state <= 7 => 'info',
                        $state <= 14 => 'warning',
                        $state <= 30 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('daily_start_time')
                    ->label('Inicio Diario')
                    ->time('H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('daily_end_time')
                    ->label('Fin Diario')
                    ->time('H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('daily_hours')
                    ->label('Horas Diarias')
                    ->numeric()
                    ->sortable()
                    ->suffix(' h')
                    ->color(fn (int $state): string => match (true) {
                        $state <= 6 => 'success',
                        $state <= 8 => 'info',
                        $state <= 12 => 'warning',
                        $state <= 16 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('time_slot_duration')
                    ->label('Franja Horaria')
                    ->formatStateUsing(fn ($state): string => 
                        is_numeric($state) ? $state . ' min' : $state
                    )
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_master_schedule')
                    ->label('Maestro')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('includes_weekends')
                    ->label('Fines de Semana')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('has_breaks')
                    ->label('Pausas')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('multi_venue')
                    ->label('Múltiples Venues')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('color_coded')
                    ->label('Código de Colores')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'danger' => 'inactive',
                        'info' => 'testing',
                        'warning' => 'maintenance',
                        'dark' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'testing' => '🧪 En Pruebas',
                        'maintenance' => '🔧 En Mantenimiento',
                        'archived' => '📦 Archivado',
                        'deprecated' => '⚠️ Deprecado',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Aprobado')
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
                        'very_good' => '🟢 Muy Bueno',
                        'good' => '🟡 Bueno',
                        'fair' => '🟠 Regular',
                        'poor' => '🔴 Pobre',
                        'not_rated' => '⚫ No Evaluado',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('schedule_type')
                    ->options([
                        'daily' => '📅 Diario',
                        'weekly' => '📅 Semanal',
                        'monthly' => '📅 Mensual',
                        'event_based' => '🎯 Basado en Eventos',
                        'time_slot' => '⏰ Por Franjas Horarias',
                        'venue_based' => '🏢 Basado en Venues',
                        'category_based' => '🏷️ Basado en Categorías',
                        'custom' => '⚙️ Personalizado',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Horario'),
                
                Tables\Filters\SelectFilter::make('timezone')
                    ->options([
                        'UTC' => '🌍 UTC',
                        'Europe/Madrid' => '🇪🇸 Madrid',
                        'Europe/London' => '🇬🇧 Londres',
                        'Europe/Paris' => '🇫🇷 París',
                        'Europe/Berlin' => '🇩🇪 Berlín',
                        'America/New_York' => '🇺🇸 Nueva York',
                        'America/Los_Angeles' => '🇺🇸 Los Ángeles',
                        'Asia/Tokyo' => '🇯🇵 Tokio',
                        'Asia/Shanghai' => '🇨🇳 Shanghái',
                        'Australia/Sydney' => '🇦🇺 Sídney',
                        'other' => '❓ Otro',
                    ])
                    ->label('Zona Horaria'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'testing' => '🧪 En Pruebas',
                        'maintenance' => '🔧 En Mantenimiento',
                        'archived' => '📦 Archivado',
                        'deprecated' => '⚠️ Deprecado',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('master_schedules')
                    ->label('Solo Horarios Maestros')
                    ->query(fn (Builder $query): Builder => $query->where('is_master_schedule', true)),
                
                Tables\Filters\Filter::make('public_schedules')
                    ->label('Solo Públicos')
                    ->query(fn (Builder $query): Builder => $query->where('is_public', true)),
                
                Tables\Filters\Filter::make('active_schedules')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('current_schedules')
                    ->label('Horarios Actuales')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '<=', now())->where('end_date', '>=', now())),
                
                Tables\Filters\Filter::make('upcoming_schedules')
                    ->label('Horarios Próximos')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '>', now())),
                
                Tables\Filters\Filter::make('long_duration')
                    ->label('Larga Duración (14+ días)')
                    ->query(fn (Builder $query): Builder => $query->where('total_days', '>=', 14)),
                
                Tables\Filters\Filter::make('short_duration')
                    ->label('Corta Duración (≤3 días)')
                    ->query(fn (Builder $query): Builder => $query->where('total_days', '<=', 3)),
                
                Tables\Filters\Filter::make('includes_weekends')
                    ->label('Incluye Fines de Semana')
                    ->query(fn (Builder $query): Builder => $query->where('includes_weekends', true)),
                
                Tables\Filters\Filter::make('multi_venue')
                    ->label('Múltiples Venues')
                    ->query(fn (Builder $query): Builder => $query->where('multi_venue', true)),
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
                    ->label('Marcar como Verificado')
                    ->icon('fas-check-circle')
                    ->action(function ($record): void {
                        $record->update(['is_verified' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('approve_schedule')
                    ->label('Aprobar')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['is_approved' => true, 'status' => 'active']);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('activate_schedule')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_schedule')
                    ->label('Desactivar')
                    ->icon('fas-pause')
                    ->action(function ($record): void {
                        $record->update(['status' => 'inactive']);
                    })
                    ->visible(fn ($record): bool => $record->status === 'active')
                    ->color('warning'),
                
                Tables\Actions\Action::make('test_schedule')
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
                        ->label('Marcar como Destacados')
                        ->icon('fas-star')
                        ->action(function ($records): void {
                            $records->each->update(['is_featured' => true]);
                        })
                        ->color('warning'),
                    
                    Tables\Actions\BulkAction::make('mark_verified')
                        ->label('Marcar como Verificados')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_verified' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('approve_all')
                        ->label('Aprobar Todos')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_approved' => true, 'status' => 'active']);
                        })
                        ->color('success'),
                    
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
            'index' => Pages\ListFestivalSchedules::route('/'),
            'create' => Pages\CreateFestivalSchedule::route('/create'),
            'view' => Pages\ViewFestivalSchedule::route('/{record}'),
            'edit' => Pages\EditFestivalSchedule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
