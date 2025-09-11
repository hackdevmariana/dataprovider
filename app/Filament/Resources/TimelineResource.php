<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimelineResource\Pages;
use App\Filament\Resources\TimelineResource\RelationManagers;
use App\Models\Timeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimelineResource extends Resource
{
    protected static ?string $model = Timeline::class;

    protected static ?string $navigationIcon = 'fas-clock';

    protected static ?string $navigationGroup = 'Historia y Cultura';

    protected static ?string $navigationLabel = 'Líneas de Tiempo';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Línea de Tiempo';

    protected static ?string $pluralModelLabel = 'Líneas de Tiempo';

    
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
                            ->maxLength(255)
                            ->label('Título de la Línea de Tiempo')
                            ->placeholder('Título descriptivo de la línea de tiempo...'),
                        
                        Forms\Components\TextInput::make('timeline_code')
                            ->maxLength(100)
                            ->label('Código de la Línea de Tiempo')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada de la línea de tiempo...'),
                        
                        Forms\Components\Select::make('timeline_type')
                            ->options([
                                'historical' => '🏛️ Histórica',
                                'biographical' => '👤 Biográfica',
                                'cultural' => '🎭 Cultural',
                                'scientific' => '🔬 Científica',
                                'technological' => '⚙️ Tecnológica',
                                'artistic' => '🎨 Artística',
                                'literary' => '📚 Literaria',
                                'musical' => '🎵 Musical',
                                'political' => '🏛️ Política',
                                'economic' => '💰 Económica',
                                'social' => '🤝 Social',
                                'religious' => '⛪ Religiosa',
                                'military' => '⚔️ Militar',
                                'sports' => '⚽ Deportiva',
                                'educational' => '🎓 Educativa',
                                'medical' => '🏥 Médica',
                                'environmental' => '🌱 Ambiental',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Línea de Tiempo'),
                        
                        Forms\Components\Select::make('category')
                            ->options([
                                'world_history' => '🌍 Historia Mundial',
                                'national_history' => '🏳️ Historia Nacional',
                                'regional_history' => '🏘️ Historia Regional',
                                'local_history' => '🏠 Historia Local',
                                'family_history' => '👨‍👩‍👧‍👦 Historia Familiar',
                                'personal_history' => '👤 Historia Personal',
                                'institutional_history' => '🏢 Historia Institucional',
                                'field_history' => '🔬 Historia de Campo',
                                'movement_history' => '🔄 Historia de Movimiento',
                                'era_history' => '⏰ Historia de Época',
                                'dynasty_history' => '👑 Historia de Dinastía',
                                'war_history' => '⚔️ Historia de Guerra',
                                'peace_history' => '🕊️ Historia de Paz',
                                'revolution_history' => '🔥 Historia de Revolución',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría'),
                    ])->columns(2),

                Forms\Components\Section::make('Período Temporal')
                    ->schema([
                        Forms\Components\TextInput::make('start_date')
                            ->maxLength(100)
                            ->label('Fecha de Inicio')
                            ->placeholder('Año, siglo o período de inicio...'),
                        
                        Forms\Components\TextInput::make('end_date')
                            ->maxLength(100)
                            ->label('Fecha de Fin')
                            ->placeholder('Año, siglo o período de fin...'),
                        
                        Forms\Components\Select::make('time_period')
                            ->options([
                                'ancient' => '🏺 Antigua',
                                'medieval' => '⚔️ Medieval',
                                'renaissance' => '🎨 Renacimiento',
                                'early_modern' => '⚓ Moderna Temprana',
                                'modern' => '🏭 Moderna',
                                'contemporary' => '🌆 Contemporánea',
                                'prehistoric' => '🦕 Prehistórica',
                                'classical' => '🏛️ Clásica',
                                'byzantine' => '⛪ Bizantina',
                                'islamic_golden_age' => '☪️ Edad de Oro Islámica',
                                'age_of_exploration' => '🧭 Era de la Exploración',
                                'industrial_revolution' => '🏭 Revolución Industrial',
                                'information_age' => '💻 Era de la Información',
                                'other' => '❓ Otro',
                            ])
                            ->label('Período Histórico'),
                        
                        Forms\Components\TextInput::make('duration')
                            ->maxLength(100)
                            ->label('Duración')
                            ->placeholder('100 años, 5 siglos, 2 milenios...'),
                        
                        Forms\Components\Toggle::make('is_ongoing')
                            ->label('En Curso')
                            ->default(false)
                            ->helperText('La línea de tiempo continúa hasta el presente'),
                        
                        Forms\Components\Toggle::make('is_cyclical')
                            ->label('Cíclica')
                            ->default(false)
                            ->helperText('Los eventos se repiten en ciclos'),
                    ])->columns(2),

                Forms\Components\Section::make('Alcance y Cobertura')
                    ->schema([
                        Forms\Components\Select::make('geographic_scope')
                            ->options([
                                'global' => '🌍 Global',
                                'continental' => '🌎 Continental',
                                'regional' => '🏘️ Regional',
                                'national' => '🏳️ Nacional',
                                'state_province' => '🏛️ Estado/Provincia',
                                'city' => '🏙️ Ciudad',
                                'local' => '🏠 Local',
                                'institutional' => '🏢 Institucional',
                                'personal' => '👤 Personal',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Alcance Geográfico'),
                        
                        Forms\Components\TextInput::make('geographic_focus')
                            ->maxLength(255)
                            ->label('Enfoque Geográfico')
                            ->placeholder('Países, regiones o lugares específicos...'),
                        
                        Forms\Components\Select::make('population_scope')
                            ->options([
                                'all_humanity' => '👥 Toda la Humanidad',
                                'specific_culture' => '🏺 Cultura Específica',
                                'ethnic_group' => '👨‍👩‍👧‍👦 Grupo Étnico',
                                'social_class' => '👑 Clase Social',
                                'professional_group' => '💼 Grupo Profesional',
                                'age_group' => '👶 Grupo de Edad',
                                'gender_group' => '🚻 Grupo de Género',
                                'religious_group' => '⛪ Grupo Religioso',
                                'political_group' => '🏛️ Grupo Político',
                                'other' => '❓ Otro',
                            ])
                            ->label('Alcance Poblacional'),
                        
                        Forms\Components\TextInput::make('population_focus')
                            ->maxLength(255)
                            ->label('Enfoque Poblacional')
                            ->placeholder('Grupos específicos de personas...'),
                        
                        Forms\Components\Select::make('subject_scope')
                            ->options([
                                'comprehensive' => '📚 Integral',
                                'specialized' => '🎯 Especializado',
                                'thematic' => '🎭 Temático',
                                'chronological' => '⏰ Cronológico',
                                'biographical' => '👤 Biográfico',
                                'institutional' => '🏢 Institucional',
                                'field_specific' => '🔬 Campo Específico',
                                'event_specific' => '🎪 Evento Específico',
                                'other' => '❓ Otro',
                            ])
                            ->label('Alcance Temático'),
                    ])->columns(2),

                Forms\Components\Section::make('Estructura y Organización')
                    ->schema([
                        Forms\Components\Select::make('organization_method')
                            ->options([
                                'chronological' => '⏰ Cronológica',
                                'thematic' => '🎭 Temática',
                                'geographic' => '🌍 Geográfica',
                                'biographical' => '👤 Biográfica',
                                'causal' => '🔗 Causal',
                                'hierarchical' => '📊 Jerárquica',
                                'narrative' => '📖 Narrativa',
                                'analytical' => '🔍 Analítica',
                                'comparative' => '⚖️ Comparativa',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Método de Organización'),
                        
                        Forms\Components\TextInput::make('main_theme')
                            ->maxLength(255)
                            ->label('Tema Principal')
                            ->placeholder('Tema central de la línea de tiempo...'),
                        
                        Forms\Components\KeyValue::make('sub_themes')
                            ->label('Subtemas')
                            ->keyLabel('Subtema')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Subtema'),
                        
                        Forms\Components\TextInput::make('narrative_style')
                            ->maxLength(100)
                            ->label('Estilo Narrativo')
                            ->placeholder('Objetivo, subjetivo, dramático...'),
                        
                        Forms\Components\Toggle::make('has_milestones')
                            ->label('Tiene Hitos')
                            ->default(true)
                            ->helperText('Eventos importantes marcados'),
                        
                        Forms\Components\Toggle::make('has_periods')
                            ->label('Tiene Períodos')
                            ->default(true)
                            ->helperText('Períodos o eras definidas'),
                        
                        Forms\Components\Toggle::make('has_eras')
                            ->label('Tiene Eras')
                            ->default(false)
                            ->helperText('Eras históricas definidas'),
                    ])->columns(2),

                Forms\Components\Section::make('Contenido y Eventos')
                    ->schema([
                        Forms\Components\TextInput::make('total_events')
                            ->numeric()
                            ->label('Total de Eventos')
                            ->helperText('Número total de eventos incluidos'),
                        
                        Forms\Components\TextInput::make('key_events_count')
                            ->numeric()
                            ->label('Eventos Clave')
                            ->helperText('Número de eventos importantes'),
                        
                        Forms\Components\TextInput::make('periods_count')
                            ->numeric()
                            ->label('Número de Períodos')
                            ->helperText('Períodos o eras definidas'),
                        
                        Forms\Components\Textarea::make('event_criteria')
                            ->maxLength(500)
                            ->label('Criterios de Selección')
                            ->rows(2)
                            ->placeholder('Criterios para incluir eventos...'),
                        
                        Forms\Components\Textarea::make('exclusion_criteria')
                            ->maxLength(500)
                            ->label('Criterios de Exclusión')
                            ->rows(2)
                            ->placeholder('Criterios para excluir eventos...'),
                        
                        Forms\Components\KeyValue::make('event_types')
                            ->label('Tipos de Eventos')
                            ->keyLabel('Tipo')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Tipo'),
                    ])->columns(2),

                Forms\Components\Section::make('Fuentes y Referencias')
                    ->schema([
                        Forms\Components\Textarea::make('primary_sources')
                            ->maxLength(1000)
                            ->label('Fuentes Primarias')
                            ->rows(3)
                            ->placeholder('Documentos, artefactos, testimonios...'),
                        
                        Forms\Components\Textarea::make('secondary_sources')
                            ->maxLength(1000)
                            ->label('Fuentes Secundarias')
                            ->rows(3)
                            ->placeholder('Libros, artículos, estudios...'),
                        
                        Forms\Components\Textarea::make('archival_sources')
                            ->maxLength(500)
                            ->label('Fuentes de Archivo')
                            ->rows(2)
                            ->placeholder('Archivos, bibliotecas, museos...'),
                        
                        Forms\Components\TextInput::make('expert_consultants')
                            ->maxLength(500)
                            ->label('Consultores Expertos')
                            ->placeholder('Historiadores, especialistas...'),
                        
                        Forms\Components\TextInput::make('research_methodology')
                            ->maxLength(255)
                            ->label('Metodología de Investigación')
                            ->placeholder('Métodos utilizados...'),
                        
                        Forms\Components\Toggle::make('is_peer_reviewed')
                            ->label('Revisado por Pares')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_academic_approval')
                            ->label('Aprobación Académica')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Presentación y Visualización')
                    ->schema([
                        Forms\Components\Select::make('visual_style')
                            ->options([
                                'traditional' => '📜 Tradicional',
                                'modern' => '💻 Moderna',
                                'interactive' => '🖱️ Interactiva',
                                'multimedia' => '🎬 Multimedia',
                                'infographic' => '📊 Infografía',
                                'storyboard' => '🎭 Storyboard',
                                'flowchart' => '🔀 Diagrama de Flujo',
                                'mind_map' => '🧠 Mapa Mental',
                                'other' => '❓ Otro',
                            ])
                            ->label('Estilo Visual'),
                        
                        Forms\Components\Toggle::make('has_images')
                            ->label('Incluye Imágenes')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_videos')
                            ->label('Incluye Videos')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_audio')
                            ->label('Incluye Audio')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_maps')
                            ->label('Incluye Mapas')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_charts')
                            ->label('Incluye Gráficos')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_interactive')
                            ->label('Es Interactiva')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_responsive')
                            ->label('Es Responsiva')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('color_scheme')
                            ->maxLength(100)
                            ->label('Esquema de Colores')
                            ->placeholder('Colores principales utilizados...'),
                    ])->columns(2),

                Forms\Components\Section::make('Uso y Aplicación')
                    ->schema([
                        Forms\Components\Select::make('primary_audience')
                            ->options([
                                'academic' => '🎓 Académico',
                                'educational' => '📚 Educativo',
                                'general_public' => '👥 Público General',
                                'students' => '👨‍🎓 Estudiantes',
                                'researchers' => '🔬 Investigadores',
                                'professionals' => '💼 Profesionales',
                                'children' => '👶 Niños',
                                'tourists' => '🧳 Turistas',
                                'policy_makers' => '🏛️ Políticos',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Audiencia Principal'),
                        
                        Forms\Components\TextInput::make('educational_level')
                            ->maxLength(100)
                            ->label('Nivel Educativo')
                            ->placeholder('Primaria, secundaria, universidad...'),
                        
                        Forms\Components\TextInput::make('use_cases')
                            ->maxLength(500)
                            ->label('Casos de Uso')
                            ->placeholder('Cómo se utiliza la línea de tiempo...'),
                        
                        Forms\Components\Toggle::make('is_curriculum_aligned')
                            ->label('Alineada con Currículo')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_museum_ready')
                            ->label('Lista para Museo')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_exhibition_ready')
                            ->label('Lista para Exposición')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_publication_ready')
                            ->label('Lista para Publicación')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('licensing')
                            ->maxLength(100)
                            ->label('Licencia')
                            ->placeholder('Creative Commons, Copyright...'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Mantenimiento')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => '📝 Borrador',
                                'in_progress' => '🔄 En Progreso',
                                'review' => '👀 En Revisión',
                                'completed' => '✅ Completada',
                                'published' => '📢 Publicada',
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
                            ->helperText('Línea de tiempo importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Pública')
                            ->default(false)
                            ->helperText('Accesible al público general'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('Contenido verificado por expertos'),
                        
                        Forms\Components\TextInput::make('last_updated')
                            ->maxLength(100)
                            ->label('Última Actualización')
                            ->placeholder('Fecha de la última actualización...'),
                        
                        Forms\Components\TextInput::make('update_frequency')
                            ->maxLength(100)
                            ->label('Frecuencia de Actualización')
                            ->placeholder('Mensual, trimestral, anual...'),
                        
                        Forms\Components\Textarea::make('maintenance_notes')
                            ->maxLength(500)
                            ->label('Notas de Mantenimiento')
                            ->rows(2)
                            ->placeholder('Notas sobre mantenimiento...'),
                        
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
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('timeline_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'historical',
                        'success' => 'biographical',
                        'warning' => 'cultural',
                        'info' => 'scientific',
                        'danger' => 'technological',
                        'secondary' => 'artistic',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'historical' => '🏛️ Histórica',
                        'biographical' => '👤 Biográfica',
                        'cultural' => '🎭 Cultural',
                        'scientific' => '🔬 Científica',
                        'technological' => '⚙️ Tecnológica',
                        'artistic' => '🎨 Artística',
                        'literary' => '📚 Literaria',
                        'musical' => '🎵 Musical',
                        'political' => '🏛️ Política',
                        'economic' => '💰 Económica',
                        'social' => '🤝 Social',
                        'religious' => '⛪ Religiosa',
                        'military' => '⚔️ Militar',
                        'sports' => '⚽ Deportiva',
                        'educational' => '🎓 Educativa',
                        'medical' => '🏥 Médica',
                        'environmental' => '🌱 Ambiental',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'world_history',
                        'success' => 'national_history',
                        'warning' => 'regional_history',
                        'info' => 'local_history',
                        'danger' => 'family_history',
                        'secondary' => 'personal_history',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'world_history' => '🌍 Historia Mundial',
                        'national_history' => '🏳️ Historia Nacional',
                        'regional_history' => '🏘️ Historia Regional',
                        'local_history' => '🏠 Historia Local',
                        'family_history' => '👨‍👩‍👧‍👦 Historia Familiar',
                        'personal_history' => '👤 Historia Personal',
                        'institutional_history' => '🏢 Historia Institucional',
                        'field_history' => '🔬 Historia de Campo',
                        'movement_history' => '🔄 Historia de Movimiento',
                        'era_history' => '⏰ Historia de Época',
                        'dynasty_history' => '👑 Historia de Dinastía',
                        'war_history' => '⚔️ Historia de Guerra',
                        'peace_history' => '🕊️ Historia de Paz',
                        'revolution_history' => '🔥 Historia de Revolución',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\BadgeColumn::make('time_period')
                    ->label('Período')
                    ->colors([
                        'primary' => 'ancient',
                        'success' => 'medieval',
                        'warning' => 'renaissance',
                        'info' => 'early_modern',
                        'danger' => 'modern',
                        'secondary' => 'contemporary',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ancient' => '🏺 Antigua',
                        'medieval' => '⚔️ Medieval',
                        'renaissance' => '🎨 Renacimiento',
                        'early_modern' => '⚓ Moderna Temprana',
                        'modern' => '🏭 Moderna',
                        'contemporary' => '🌆 Contemporánea',
                        'prehistoric' => '🦕 Prehistórica',
                        'classical' => '🏛️ Clásica',
                        'byzantine' => '⛪ Bizantina',
                        'islamic_golden_age' => '☪️ Edad de Oro Islámica',
                        'age_of_exploration' => '🧭 Era de la Exploración',
                        'industrial_revolution' => '🏭 Revolución Industrial',
                        'information_age' => '💻 Era de la Información',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('geographic_scope')
                    ->label('Alcance')
                    ->colors([
                        'danger' => 'global',
                        'warning' => 'continental',
                        'info' => 'regional',
                        'success' => 'national',
                        'primary' => 'state_province',
                        'secondary' => 'city',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'global' => '🌍 Global',
                        'continental' => '🌎 Continental',
                        'regional' => '🏘️ Regional',
                        'national' => '🏳️ Nacional',
                        'state_province' => '🏛️ Estado/Provincia',
                        'city' => '🏙️ Ciudad',
                        'local' => '🏠 Local',
                        'institutional' => '🏢 Institucional',
                        'personal' => '👤 Personal',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('total_events')
                    ->label('Eventos')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'info',
                        $state >= 20 => 'warning',
                        $state >= 10 => 'danger',
                        default => 'secondary',
                    }),
                
                Tables\Columns\BadgeColumn::make('organization_method')
                    ->label('Organización')
                    ->colors([
                        'primary' => 'chronological',
                        'success' => 'thematic',
                        'warning' => 'geographic',
                        'info' => 'biographical',
                        'danger' => 'causal',
                        'secondary' => 'hierarchical',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'chronological' => '⏰ Cronológica',
                        'thematic' => '🎭 Temática',
                        'geographic' => '🌍 Geográfica',
                        'biographical' => '👤 Biográfica',
                        'causal' => '🔗 Causal',
                        'hierarchical' => '📊 Jerárquica',
                        'narrative' => '📖 Narrativa',
                        'analytical' => '🔍 Analítica',
                        'comparative' => '⚖️ Comparativa',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('primary_audience')
                    ->label('Audiencia')
                    ->colors([
                        'primary' => 'academic',
                        'success' => 'educational',
                        'warning' => 'general_public',
                        'info' => 'students',
                        'danger' => 'researchers',
                        'secondary' => 'professionals',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'academic' => '🎓 Académico',
                        'educational' => '📚 Educativo',
                        'general_public' => '👥 Público General',
                        'students' => '👨‍🎓 Estudiantes',
                        'researchers' => '🔬 Investigadores',
                        'professionals' => '💼 Profesionales',
                        'children' => '👶 Niños',
                        'tourists' => '🧳 Turistas',
                        'policy_makers' => '🏛️ Políticos',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'in_progress',
                        'info' => 'review',
                        'success' => 'completed',
                        'primary' => 'published',
                        'dark' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'in_progress' => '🔄 En Progreso',
                        'review' => '👀 En Revisión',
                        'completed' => '✅ Completada',
                        'published' => '📢 Publicada',
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
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Pública')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificada')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_interactive')
                    ->label('Interactiva')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('timeline_type')
                    ->options([
                        'historical' => '🏛️ Histórica',
                        'biographical' => '👤 Biográfica',
                        'cultural' => '🎭 Cultural',
                        'scientific' => '🔬 Científica',
                        'technological' => '⚙️ Tecnológica',
                        'artistic' => '🎨 Artística',
                        'literary' => '📚 Literaria',
                        'musical' => '🎵 Musical',
                        'political' => '🏛️ Política',
                        'economic' => '💰 Económica',
                        'social' => '🤝 Social',
                        'religious' => '⛪ Religiosa',
                        'military' => '⚔️ Militar',
                        'sports' => '⚽ Deportiva',
                        'educational' => '🎓 Educativa',
                        'medical' => '🏥 Médica',
                        'environmental' => '🌱 Ambiental',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Línea de Tiempo'),
                
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'world_history' => '🌍 Historia Mundial',
                        'national_history' => '🏳️ Historia Nacional',
                        'regional_history' => '🏘️ Historia Regional',
                        'local_history' => '🏠 Historia Local',
                        'family_history' => '👨‍👩‍👧‍👦 Historia Familiar',
                        'personal_history' => '👤 Historia Personal',
                        'institutional_history' => '🏢 Historia Institucional',
                        'field_history' => '🔬 Historia de Campo',
                        'movement_history' => '🔄 Historia de Movimiento',
                        'era_history' => '⏰ Historia de Época',
                        'dynasty_history' => '👑 Historia de Dinastía',
                        'war_history' => '⚔️ Historia de Guerra',
                        'peace_history' => '🕊️ Historia de Paz',
                        'revolution_history' => '🔥 Historia de Revolución',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría'),
                
                Tables\Filters\SelectFilter::make('time_period')
                    ->options([
                        'ancient' => '🏺 Antigua',
                        'medieval' => '⚔️ Medieval',
                        'renaissance' => '🎨 Renacimiento',
                        'early_modern' => '⚓ Moderna Temprana',
                        'modern' => '🏭 Moderna',
                        'contemporary' => '🌆 Contemporánea',
                        'prehistoric' => '🦕 Prehistórica',
                        'classical' => '🏛️ Clásica',
                        'byzantine' => '⛪ Bizantina',
                        'islamic_golden_age' => '☪️ Edad de Oro Islámica',
                        'age_of_exploration' => '🧭 Era de la Exploración',
                        'industrial_revolution' => '🏭 Revolución Industrial',
                        'information_age' => '💻 Era de la Información',
                        'other' => '❓ Otro',
                    ])
                    ->label('Período Histórico'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => '📝 Borrador',
                        'in_progress' => '🔄 En Progreso',
                        'review' => '👀 En Revisión',
                        'completed' => '✅ Completada',
                        'published' => '📢 Publicada',
                        'archived' => '📦 Archivada',
                        'deprecated' => '⚠️ Deprecada',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\SelectFilter::make('geographic_scope')
                    ->options([
                        'global' => '🌍 Global',
                        'continental' => '🌎 Continental',
                        'regional' => '🏘️ Regional',
                        'national' => '🏳️ Nacional',
                        'state_province' => '🏛️ Estado/Provincia',
                        'city' => '🏙️ Ciudad',
                        'local' => '🏠 Local',
                        'institutional' => '🏢 Institucional',
                        'personal' => '👤 Personal',
                        'other' => '❓ Otro',
                    ])
                    ->label('Alcance Geográfico'),
                
                Tables\Filters\SelectFilter::make('primary_audience')
                    ->options([
                        'academic' => '🎓 Académico',
                        'educational' => '📚 Educativo',
                        'general_public' => '👥 Público General',
                        'students' => '👨‍🎓 Estudiantes',
                        'researchers' => '🔬 Investigadores',
                        'professionals' => '💼 Profesionales',
                        'children' => '👶 Niños',
                        'tourists' => '🧳 Turistas',
                        'policy_makers' => '🏛️ Políticos',
                        'other' => '❓ Otro',
                    ])
                    ->label('Audiencia Principal'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('public_only')
                    ->label('Solo Públicas')
                    ->query(fn (Builder $query): Builder => $query->where('is_public', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('interactive_only')
                    ->label('Solo Interactivas')
                    ->query(fn (Builder $query): Builder => $query->where('is_interactive', true)),
                
                Tables\Filters\Filter::make('ongoing_only')
                    ->label('Solo En Curso')
                    ->query(fn (Builder $query): Builder => $query->where('is_ongoing', true)),
                
                Tables\Filters\Filter::make('many_events')
                    ->label('Muchos Eventos (50+)')
                    ->query(fn (Builder $query): Builder => $query->where('total_events', '>=', 50)),
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
                
                Tables\Actions\Action::make('toggle_public')
                    ->label(fn ($record): string => $record->is_public ? 'Hacer Privada' : 'Hacer Pública')
                    ->icon(fn ($record): string => $record->is_public ? 'fas-eye-slash' : 'fas-eye')
                    ->action(function ($record): void {
                        $record->update(['is_public' => !$record->is_public]);
                    })
                    ->color(fn ($record): string => $record->is_public ? 'success' : 'secondary'),
                
                Tables\Actions\Action::make('mark_verified')
                    ->label('Marcar como Verificada')
                    ->icon('fas-check-circle')
                    ->action(function ($record): void {
                        $record->update(['is_verified' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('publish_timeline')
                    ->label('Publicar')
                    ->icon('fas-upload')
                    ->action(function ($record): void {
                        $record->update(['status' => 'published']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'published')
                    ->color('primary'),
                
                Tables\Actions\Action::make('preview_timeline')
                    ->label('Vista Previa')
                    ->icon('fas-eye')
                    ->url(fn ($record): string => "/timeline/preview/{$record->id}")
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => $record->status === 'published')
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
                    
                    Tables\Actions\BulkAction::make('mark_public')
                        ->label('Marcar como Públicas')
                        ->icon('fas-eye')
                        ->action(function ($records): void {
                            $records->each->update(['is_public' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_verified')
                        ->label('Marcar como Verificadas')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_verified' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('publish_all')
                        ->label('Publicar Todas')
                        ->icon('fas-upload')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'published']);
                        })
                        ->color('primary'),
                    
                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Marcar como Completadas')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'completed']);
                        })
                        ->color('success'),
                ]),
            ])
            ->defaultSort('title', 'asc')
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
            'index' => Pages\ListTimelines::route('/'),
            'create' => Pages\CreateTimeline::route('/create'),
            'view' => Pages\ViewTimeline::route('/{record}'),
            'edit' => Pages\EditTimeline::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
