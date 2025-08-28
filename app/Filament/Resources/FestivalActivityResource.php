<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FestivalActivityResource\Pages;
use App\Models\FestivalActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FestivalActivityResource extends Resource
{
    protected static ?string $model = FestivalActivity::class;

    protected static ?string $navigationIcon = 'fas-theater-masks';

    protected static ?string $navigationGroup = 'Festivales y Eventos';

    protected static ?string $navigationLabel = 'Actividades de Festivales';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Actividad de Festival';

    protected static ?string $pluralModelLabel = 'Actividades de Festivales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('activity_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Actividad')
                            ->placeholder('Nombre de la actividad...'),
                        
                        Forms\Components\TextInput::make('activity_code')
                            ->maxLength(100)
                            ->label('Código de Actividad')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción de la actividad...'),
                        
                        Forms\Components\Select::make('festival_program_id')
                            ->relationship('festivalProgram', 'program_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Programa del Festival')
                            ->placeholder('Selecciona el programa...'),
                        
                        Forms\Components\Select::make('activity_type')
                            ->options([
                                'performance' => '🎭 Presentación',
                                'workshop' => '🔧 Taller',
                                'exhibition' => '🖼️ Exposición',
                                'competition' => '🏆 Competencia',
                                'lecture' => '📚 Conferencia',
                                'demonstration' => '🎯 Demostración',
                                'interactive' => '🖱️ Interactiva',
                                'ceremony' => '🎉 Ceremonia',
                                'parade' => '🎊 Desfile',
                                'concert' => '🎵 Concierto',
                                'dance' => '💃 Baile',
                                'theater' => '🎪 Teatro',
                                'film' => '🎬 Cine',
                                'food' => '🍕 Gastronomía',
                                'sports' => '⚽ Deportes',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Actividad'),
                        
                        Forms\Components\Select::make('category')
                            ->options([
                                'arts' => '🎨 Artes',
                                'music' => '🎵 Música',
                                'dance' => '💃 Danza',
                                'theater' => '🎪 Teatro',
                                'film' => '🎬 Cine',
                                'literature' => '📚 Literatura',
                                'food' => '🍕 Gastronomía',
                                'sports' => '⚽ Deportes',
                                'technology' => '💻 Tecnología',
                                'science' => '🔬 Ciencia',
                                'education' => '🎓 Educación',
                                'culture' => '🏛️ Cultura',
                                'religion' => '⛪ Religión',
                                'business' => '💼 Negocios',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría'),
                        
                        Forms\Components\Select::make('target_audience')
                            ->options([
                                'all_ages' => '👥 Todas las Edades',
                                'children' => '👶 Niños',
                                'youth' => '🧑‍🎓 Jóvenes',
                                'adults' => '👨‍💼 Adultos',
                                'seniors' => '👴 Mayores',
                                'families' => '👨‍👩‍👧‍👦 Familias',
                                'students' => '🎓 Estudiantes',
                                'professionals' => '💼 Profesionales',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Audiencia Objetivo'),
                    ])->columns(2),

                Forms\Components\Section::make('Programación y Horarios')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->required()
                            ->label('Hora de Inicio')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Cuándo comienza la actividad'),
                        
                        Forms\Components\DateTimePicker::make('end_time')
                            ->required()
                            ->label('Hora de Fin')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Cuándo termina la actividad'),
                        
                        Forms\Components\TextInput::make('duration_minutes')
                            ->numeric()
                            ->label('Duración (minutos)')
                            ->placeholder('Duración en minutos...'),
                        
                        Forms\Components\Toggle::make('is_recurring')
                            ->label('Es Recurrente')
                            ->default(false)
                            ->helperText('La actividad se repite'),
                        
                        Forms\Components\Select::make('recurrence_pattern')
                            ->options([
                                'daily' => '📅 Diario',
                                'weekly' => '📅 Semanal',
                                'monthly' => '📅 Mensual',
                                'yearly' => '📅 Anual',
                                'custom' => '⚙️ Personalizado',
                                'other' => '❓ Otro',
                            ])
                            ->label('Patrón de Recurrencia')
                            ->visible(fn (Forms\Get $get): bool => $get('is_recurring')),
                        
                        Forms\Components\TextInput::make('recurrence_interval')
                            ->numeric()
                            ->label('Intervalo de Recurrencia')
                            ->placeholder('Cada X días/semanas/meses...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_recurring')),
                        
                        Forms\Components\DatePicker::make('recurrence_end_date')
                            ->label('Fecha de Fin de Recurrencia')
                            ->displayFormat('d/m/Y')
                            ->visible(fn (Forms\Get $get): bool => $get('is_recurring')),
                        
                        Forms\Components\Toggle::make('has_breaks')
                            ->label('Tiene Pausas')
                            ->default(false)
                            ->helperText('La actividad incluye pausas'),
                        
                        Forms\Components\Textarea::make('break_schedule')
                            ->maxLength(500)
                            ->label('Horario de Pausas')
                            ->rows(2)
                            ->placeholder('Horario de las pausas...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_breaks')),
                    ])->columns(2),

                Forms\Components\Section::make('Ubicación y Espacio')
                    ->schema([
                        Forms\Components\TextInput::make('venue_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Venue')
                            ->placeholder('Nombre del lugar...'),
                        
                        Forms\Components\TextInput::make('venue_address')
                            ->maxLength(500)
                            ->label('Dirección del Venue')
                            ->placeholder('Dirección completa...'),
                        
                        Forms\Components\TextInput::make('venue_city')
                            ->maxLength(100)
                            ->label('Ciudad')
                            ->placeholder('Ciudad...'),
                        
                        Forms\Components\TextInput::make('venue_country')
                            ->maxLength(100)
                            ->label('País')
                            ->placeholder('País...'),
                        
                        Forms\Components\TextInput::make('venue_postal_code')
                            ->maxLength(20)
                            ->label('Código Postal')
                            ->placeholder('Código postal...'),
                        
                        Forms\Components\TextInput::make('room_name')
                            ->maxLength(255)
                            ->label('Nombre de la Sala')
                            ->placeholder('Nombre de la sala específica...'),
                        
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->label('Capacidad')
                            ->placeholder('Número máximo de personas...'),
                        
                        Forms\Components\Select::make('venue_type')
                            ->options([
                                'indoor' => '🏠 Interior',
                                'outdoor' => '🌳 Exterior',
                                'mixed' => '🔄 Mixto',
                                'virtual' => '💻 Virtual',
                                'hybrid' => '🔄 Híbrido',
                                'other' => '❓ Otro',
                            ])
                            ->label('Tipo de Venue'),
                        
                        Forms\Components\TextInput::make('coordinates')
                            ->maxLength(100)
                            ->label('Coordenadas')
                            ->placeholder('Latitud, Longitud...'),
                    ])->columns(2),

                Forms\Components\Section::make('Participantes y Organización')
                    ->schema([
                        Forms\Components\TextInput::make('organizer_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Organizador')
                            ->placeholder('Nombre del organizador...'),
                        
                        Forms\Components\TextInput::make('organizer_contact')
                            ->maxLength(255)
                            ->label('Contacto del Organizador')
                            ->placeholder('Email o teléfono...'),
                        
                        Forms\Components\TextInput::make('main_performer')
                            ->maxLength(255)
                            ->label('Artista Principal')
                            ->placeholder('Artista o grupo principal...'),
                        
                        Forms\Components\Textarea::make('participants')
                            ->maxLength(500)
                            ->label('Participantes')
                            ->rows(2)
                            ->placeholder('Lista de participantes...'),
                        
                        Forms\Components\TextInput::make('min_participants')
                            ->numeric()
                            ->label('Mínimo de Participantes')
                            ->placeholder('Número mínimo...'),
                        
                        Forms\Components\TextInput::make('max_participants')
                            ->numeric()
                            ->label('Máximo de Participantes')
                            ->placeholder('Número máximo...'),
                        
                        Forms\Components\Toggle::make('requires_registration')
                            ->label('Requiere Registro')
                            ->default(false)
                            ->helperText('La actividad requiere registro previo'),
                        
                        Forms\Components\DatePicker::make('registration_deadline')
                            ->label('Fecha Límite de Registro')
                            ->displayFormat('d/m/Y')
                            ->visible(fn (Forms\Get $get): bool => $get('requires_registration')),
                        
                        Forms\Components\Toggle::make('is_free')
                            ->label('Es Gratuita')
                            ->default(true)
                            ->helperText('La actividad es gratuita'),
                        
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->label('Precio')
                            ->placeholder('Precio de la actividad...')
                            ->visible(fn (Forms\Get $get): bool => !$get('is_free')),
                        
                        Forms\Components\Select::make('currency')
                            ->options([
                                'EUR' => '€ EUR',
                                'USD' => '$ USD',
                                'GBP' => '£ GBP',
                                'other' => 'Otro',
                            ])
                            ->default('EUR')
                            ->label('Moneda')
                            ->visible(fn (Forms\Get $get): bool => !$get('is_free')),
                    ])->columns(2),

                Forms\Components\Section::make('Características y Requisitos')
                    ->schema([
                        Forms\Components\Toggle::make('is_interactive')
                            ->label('Es Interactiva')
                            ->default(false)
                            ->helperText('La actividad es interactiva'),
                        
                        Forms\Components\Toggle::make('is_educational')
                            ->label('Es Educativa')
                            ->default(false)
                            ->helperText('La actividad tiene valor educativo'),
                        
                        Forms\Components\Toggle::make('is_entertainment')
                            ->label('Es de Entretenimiento')
                            ->default(true)
                            ->helperText('La actividad es de entretenimiento'),
                        
                        Forms\Components\Toggle::make('is_cultural')
                            ->label('Es Cultural')
                            ->default(false)
                            ->helperText('La actividad tiene valor cultural'),
                        
                        Forms\Components\Toggle::make('is_accessible')
                            ->label('Es Accesible')
                            ->default(false)
                            ->helperText('La actividad es accesible para personas con discapacidad'),
                        
                        Forms\Components\Textarea::make('accessibility_features')
                            ->maxLength(500)
                            ->label('Características de Accesibilidad')
                            ->rows(2)
                            ->placeholder('Características de accesibilidad...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_accessible')),
                        
                        Forms\Components\Textarea::make('requirements')
                            ->maxLength(500)
                            ->label('Requisitos')
                            ->rows(2)
                            ->placeholder('Requisitos para participar...'),
                        
                        Forms\Components\Textarea::make('materials_provided')
                            ->maxLength(500)
                            ->label('Materiales Proporcionados')
                            ->rows(2)
                            ->placeholder('Materiales que se proporcionan...'),
                        
                        Forms\Components\Textarea::make('materials_needed')
                            ->maxLength(500)
                            ->label('Materiales Necesarios')
                            ->rows(2)
                            ->placeholder('Materiales que debe traer el participante...'),
                        
                        Forms\Components\Textarea::make('special_instructions')
                            ->maxLength(500)
                            ->label('Instrucciones Especiales')
                            ->rows(2)
                            ->placeholder('Instrucciones especiales...'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Calidad')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => '📝 Borrador',
                                'scheduled' => '📅 Programada',
                                'active' => '✅ Activa',
                                'completed' => '✅ Completada',
                                'cancelled' => '❌ Cancelada',
                                'postponed' => '⏸️ Aplazada',
                                'sold_out' => '🎫 Agotada',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('draft')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Actividad importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular')
                            ->default(false)
                            ->helperText('Actividad popular entre los visitantes'),
                        
                        Forms\Components\Toggle::make('is_new')
                            ->label('Nueva')
                            ->default(false)
                            ->helperText('Actividad recién añadida'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('La actividad ha sido verificada'),
                        
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
                            ->placeholder('Persona que revisó la actividad...'),
                        
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
                
                Tables\Columns\TextColumn::make('activity_name')
                    ->label('Actividad')
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
                
                Tables\Columns\BadgeColumn::make('activity_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'performance',
                        'success' => 'workshop',
                        'warning' => 'exhibition',
                        'info' => 'competition',
                        'danger' => 'lecture',
                        'secondary' => 'demonstration',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'performance' => '🎭 Presentación',
                        'workshop' => '🔧 Taller',
                        'exhibition' => '🖼️ Exposición',
                        'competition' => '🏆 Competencia',
                        'lecture' => '📚 Conferencia',
                        'demonstration' => '🎯 Demostración',
                        'interactive' => '🖱️ Interactiva',
                        'ceremony' => '🎉 Ceremonia',
                        'parade' => '🎊 Desfile',
                        'concert' => '🎵 Concierto',
                        'dance' => '💃 Baile',
                        'theater' => '🎪 Teatro',
                        'film' => '🎬 Cine',
                        'food' => '🍕 Gastronomía',
                        'sports' => '⚽ Deportes',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'success' => 'arts',
                        'info' => 'music',
                        'warning' => 'dance',
                        'primary' => 'theater',
                        'danger' => 'film',
                        'secondary' => 'literature',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'arts' => '🎨 Artes',
                        'music' => '🎵 Música',
                        'dance' => '💃 Danza',
                        'theater' => '🎪 Teatro',
                        'film' => '🎬 Cine',
                        'literature' => '📚 Literatura',
                        'food' => '🍕 Gastronomía',
                        'sports' => '⚽ Deportes',
                        'technology' => '💻 Tecnología',
                        'science' => '🔬 Ciencia',
                        'education' => '🎓 Educación',
                        'culture' => '🏛️ Cultura',
                        'religion' => '⛪ Religión',
                        'business' => '💼 Negocios',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duración')
                    ->numeric()
                    ->sortable()
                    ->suffix(' min')
                    ->color(fn (int $state): string => match (true) {
                        $state <= 30 => 'success',
                        $state <= 60 => 'info',
                        $state <= 120 => 'warning',
                        $state <= 240 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('venue_name')
                    ->label('Venue')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacidad')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 50 => 'success',
                        $state <= 200 => 'info',
                        $state <= 500 => 'warning',
                        $state <= 1000 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\IconColumn::make('is_free')
                    ->label('Gratuita')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('requires_registration')
                    ->label('Registro')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_interactive')
                    ->label('Interactiva')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_accessible')
                    ->label('Accesible')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'info' => 'scheduled',
                        'success' => 'active',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                        'warning' => 'postponed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'scheduled' => '📅 Programada',
                        'active' => '✅ Activa',
                        'completed' => '✅ Completada',
                        'cancelled' => '❌ Cancelada',
                        'postponed' => '⏸️ Aplazada',
                        'sold_out' => '🎫 Agotada',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->trueColor('success')
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
                Tables\Filters\SelectFilter::make('activity_type')
                    ->options([
                        'performance' => '🎭 Presentación',
                        'workshop' => '🔧 Taller',
                        'exhibition' => '🖼️ Exposición',
                        'competition' => '🏆 Competencia',
                        'lecture' => '📚 Conferencia',
                        'demonstration' => '🎯 Demostración',
                        'interactive' => '🖱️ Interactiva',
                        'ceremony' => '🎉 Ceremonia',
                        'parade' => '🎊 Desfile',
                        'concert' => '🎵 Concierto',
                        'dance' => '💃 Baile',
                        'theater' => '🎪 Teatro',
                        'film' => '🎬 Cine',
                        'food' => '🍕 Gastronomía',
                        'sports' => '⚽ Deportes',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Actividad'),
                
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'arts' => '🎨 Artes',
                        'music' => '🎵 Música',
                        'dance' => '💃 Danza',
                        'theater' => '🎪 Teatro',
                        'film' => '🎬 Cine',
                        'literature' => '📚 Literatura',
                        'food' => '🍕 Gastronomía',
                        'sports' => '⚽ Deportes',
                        'technology' => '💻 Tecnología',
                        'science' => '🔬 Ciencia',
                        'education' => '🎓 Educación',
                        'culture' => '🏛️ Cultura',
                        'religion' => '⛪ Religión',
                        'business' => '💼 Negocios',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => '📝 Borrador',
                        'scheduled' => '📅 Programada',
                        'active' => '✅ Activa',
                        'completed' => '✅ Completada',
                        'cancelled' => '❌ Cancelada',
                        'postponed' => '⏸️ Aplazada',
                        'sold_out' => '🎫 Agotada',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('free_activities')
                    ->label('Solo Gratuitas')
                    ->query(fn (Builder $query): Builder => $query->where('is_free', true)),
                
                Tables\Filters\Filter::make('interactive_activities')
                    ->label('Solo Interactivas')
                    ->query(fn (Builder $query): Builder => $query->where('is_interactive', true)),
                
                Tables\Filters\Filter::make('accessible_activities')
                    ->label('Solo Accesibles')
                    ->query(fn (Builder $query): Builder => $query->where('is_accessible', true)),
                
                Tables\Filters\Filter::make('upcoming_activities')
                    ->label('Próximas Actividades')
                    ->query(fn (Builder $query): Builder => $query->where('start_time', '>=', now())),
                
                Tables\Filters\Filter::make('today_activities')
                    ->label('Actividades de Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('start_time', today())),
                
                Tables\Filters\Filter::make('long_duration')
                    ->label('Larga Duración (2h+)')
                    ->query(fn (Builder $query): Builder => $query->where('duration_minutes', '>=', 120)),
                
                Tables\Filters\Filter::make('high_capacity')
                    ->label('Alta Capacidad (500+)')
                    ->query(fn (Builder $query): Builder => $query->where('capacity', '>=', 500)),
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
                
                Tables\Actions\Action::make('activate_activity')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('complete_activity')
                    ->label('Marcar Completada')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['status' => 'completed']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'completed')
                    ->color('success'),
                
                Tables\Actions\Action::make('cancel_activity')
                    ->label('Cancelar')
                    ->icon('fas-times')
                    ->action(function ($record): void {
                        $record->update(['status' => 'cancelled']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'cancelled')
                    ->color('danger'),
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
                    
                    Tables\Actions\BulkAction::make('complete_all')
                        ->label('Marcar Todas Completadas')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'completed']);
                        })
                        ->color('success'),
                ]),
            ])
            ->defaultSort('start_time', 'asc')
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
            'index' => Pages\ListFestivalActivities::route('/'),
            'create' => Pages\CreateFestivalActivity::route('/create'),
            'view' => Pages\ViewFestivalActivity::route('/{record}'),
            'edit' => Pages\EditFestivalActivity::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
