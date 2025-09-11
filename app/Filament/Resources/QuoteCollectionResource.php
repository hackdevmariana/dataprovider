<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteCollectionResource\Pages;
use App\Models\QuoteCollection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteCollectionResource extends Resource
{
    protected static ?string $model = QuoteCollection::class;

    protected static ?string $navigationIcon = 'fas-bookmark';

    protected static ?string $navigationGroup = 'Biblioteca y Literatura';

    protected static ?string $navigationLabel = 'Colecciones de Citas';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Colección de Citas';

    protected static ?string $pluralModelLabel = 'Colecciones de Citas';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('collection_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Colección')
                            ->placeholder('Nombre de la colección...'),
                        
                        Forms\Components\TextInput::make('collection_code')
                            ->maxLength(100)
                            ->label('Código de Colección')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción de la colección...'),
                        
                        Forms\Components\Select::make('collection_type')
                            ->options([
                                'personal' => '👤 Personal',
                                'thematic' => '🎯 Temática',
                                'author_based' => '✍️ Basada en Autor',
                                'period_based' => '📅 Basada en Período',
                                'genre_based' => '🎭 Basada en Género',
                                'mood_based' => '😊 Basada en Estado de Ánimo',
                                'occasion_based' => '🎉 Basada en Ocasión',
                                'learning_based' => '🎓 Basada en Aprendizaje',
                                'inspiration_based' => '💡 Basada en Inspiración',
                                'curated' => '✍️ Curada',
                                'community' => '👥 Comunidad',
                                'official' => '🏛️ Oficial',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Colección'),
                        
                        Forms\Components\Select::make('theme')
                            ->options([
                                'love' => '❤️ Amor',
                                'friendship' => '🤝 Amistad',
                                'success' => '🏆 Éxito',
                                'wisdom' => '🧠 Sabiduría',
                                'inspiration' => '💡 Inspiración',
                                'motivation' => '🚀 Motivación',
                                'philosophy' => '🤔 Filosofía',
                                'religion' => '⛪ Religión',
                                'nature' => '🌿 Naturaleza',
                                'art' => '🎭 Arte',
                                'music' => '🎵 Música',
                                'literature' => '📚 Literatura',
                                'history' => '📜 Historia',
                                'science' => '🔬 Ciencia',
                                'technology' => '💻 Tecnología',
                                'business' => '💼 Negocios',
                                'education' => '🎓 Educación',
                                'health' => '🏥 Salud',
                                'family' => '👨‍👩‍👧‍👦 Familia',
                                'travel' => '✈️ Viajes',
                                'food' => '🍕 Comida',
                                'sports' => '⚽ Deportes',
                                'humor' => '😄 Humor',
                                'poetry' => '📝 Poesía',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tema'),
                        
                        Forms\Components\Select::make('difficulty_level')
                            ->options([
                                'beginner' => '🟢 Principiante',
                                'intermediate' => '🟡 Intermedio',
                                'advanced' => '🟠 Avanzado',
                                'expert' => '🔴 Experto',
                                'all_levels' => '🌈 Todos los Niveles',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('all_levels')
                            ->label('Nivel de Dificultad'),
                        
                        Forms\Components\Select::make('target_audience')
                            ->options([
                                'all_ages' => '👥 Todas las Edades',
                                'children' => '👶 Niños',
                                'youth' => '🧑‍🎓 Jóvenes',
                                'adults' => '👨‍💼 Adultos',
                                'seniors' => '👴 Mayores',
                                'students' => '🎓 Estudiantes',
                                'professionals' => '💼 Profesionales',
                                'academics' => '🎓 Académicos',
                                'spiritual_seekers' => '🕯️ Buscadores Espirituales',
                                'writers' => '✍️ Escritores',
                                'speakers' => '🎤 Oradores',
                                'teachers' => '👨‍🏫 Profesores',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Audiencia Objetivo'),
                    ])->columns(2),

                Forms\Components\Section::make('Contenido y Organización')
                    ->schema([
                        Forms\Components\Textarea::make('purpose')
                            ->maxLength(500)
                            ->label('Propósito')
                            ->rows(2)
                            ->placeholder('Propósito de la colección...'),
                        
                        Forms\Components\Textarea::make('selection_criteria')
                            ->maxLength(500)
                            ->label('Criterios de Selección')
                            ->rows(2)
                            ->placeholder('Criterios para incluir citas...'),
                        
                        Forms\Components\Textarea::make('organization_structure')
                            ->maxLength(500)
                            ->label('Estructura de Organización')
                            ->rows(2)
                            ->placeholder('Cómo está organizada la colección...'),
                        
                        Forms\Components\TextInput::make('total_quotes')
                            ->numeric()
                            ->label('Total de Citas')
                            ->placeholder('Número total de citas...')
                            ->disabled()
                            ->helperText('Calculado automáticamente'),
                        
                        Forms\Components\TextInput::make('max_quotes')
                            ->numeric()
                            ->label('Máximo de Citas')
                            ->placeholder('Número máximo de citas...'),
                        
                        Forms\Components\Toggle::make('is_sequential')
                            ->label('Es Secuencial')
                            ->default(false)
                            ->helperText('Las citas tienen un orden específico'),
                        
                        Forms\Components\Toggle::make('has_chapters')
                            ->label('Tiene Capítulos')
                            ->default(false)
                            ->helperText('La colección está dividida en capítulos'),
                        
                        Forms\Components\TextInput::make('chapter_count')
                            ->numeric()
                            ->label('Número de Capítulos')
                            ->placeholder('Número de capítulos...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_chapters')),
                        
                        Forms\Components\Toggle::make('has_tags')
                            ->label('Tiene Etiquetas')
                            ->default(true)
                            ->helperText('La colección usa sistema de etiquetas'),
                        
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->placeholder('Agregar etiquetas...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_tags')),
                        
                        Forms\Components\Toggle::make('has_notes')
                            ->label('Tiene Notas')
                            ->default(false)
                            ->helperText('La colección incluye notas explicativas'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas sobre la colección...')
                            ->visible(fn (Forms\Get $get): bool => $get('has_notes')),
                    ])->columns(2),

                Forms\Components\Section::make('Autor y Propiedad')
                    ->schema([
                        Forms\Components\TextInput::make('author_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Autor')
                            ->placeholder('Nombre del autor de la colección...'),
                        
                        Forms\Components\TextInput::make('author_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email del Autor')
                            ->placeholder('Email del autor...'),
                        
                        Forms\Components\TextInput::make('author_website')
                            ->url()
                            ->maxLength(500)
                            ->label('Sitio Web del Autor')
                            ->placeholder('https://...'),
                        
                        Forms\Components\TextInput::make('author_bio')
                            ->maxLength(500)
                            ->label('Biografía del Autor')
                            ->placeholder('Breve biografía del autor...'),
                        
                        Forms\Components\Select::make('ownership_type')
                            ->options([
                                'personal' => '👤 Personal',
                                'shared' => '🤝 Compartida',
                                'public' => '🌍 Pública',
                                'licensed' => '📜 Con Licencia',
                                'commercial' => '💼 Comercial',
                                'educational' => '🎓 Educativa',
                                'non_profit' => '🤝 Sin Fines de Lucro',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Propiedad'),
                        
                        Forms\Components\Toggle::make('is_original')
                            ->label('Es Original')
                            ->default(true)
                            ->helperText('La colección es original del autor'),
                        
                        Forms\Components\Toggle::make('is_derivative')
                            ->label('Es Derivativa')
                            ->default(false)
                            ->helperText('La colección se basa en otras obras'),
                        
                        Forms\Components\Textarea::make('sources')
                            ->maxLength(500)
                            ->label('Fuentes')
                            ->rows(2)
                            ->placeholder('Fuentes utilizadas...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_derivative')),
                        
                        Forms\Components\Textarea::make('attributions')
                            ->maxLength(500)
                            ->label('Atribuciones')
                            ->rows(2)
                            ->placeholder('Atribuciones necesarias...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_derivative')),
                        
                        Forms\Components\Select::make('license_type')
                            ->options([
                                'all_rights_reserved' => '© Todos los Derechos Reservados',
                                'creative_commons' => '🔄 Creative Commons',
                                'public_domain' => '🌍 Dominio Público',
                                'fair_use' => '⚖️ Uso Justo',
                                'educational_use' => '🎓 Uso Educativo',
                                'commercial_use' => '💼 Uso Comercial',
                                'other' => '❓ Otro',
                            ])
                            ->label('Tipo de Licencia'),
                        
                        Forms\Components\TextInput::make('license_details')
                            ->maxLength(255)
                            ->label('Detalles de Licencia')
                            ->placeholder('Detalles específicos de la licencia...'),
                    ])->columns(2),

                Forms\Components\Section::make('Diseño y Presentación')
                    ->schema([
                        Forms\Components\TextInput::make('cover_image')
                            ->maxLength(500)
                            ->label('Imagen de Portada')
                            ->placeholder('URL de la imagen de portada...'),
                        
                        Forms\Components\ColorPicker::make('primary_color')
                            ->label('Color Primario')
                            ->helperText('Color principal de la colección'),
                        
                        Forms\Components\ColorPicker::make('secondary_color')
                            ->label('Color Secundario')
                            ->helperText('Color secundario de la colección'),
                        
                        Forms\Components\Select::make('layout_style')
                            ->options([
                                'minimal' => '✨ Minimalista',
                                'classic' => '📚 Clásico',
                                'modern' => '🚀 Moderno',
                                'elegant' => '💎 Elegante',
                                'playful' => '🎈 Juguetón',
                                'serious' => '😐 Serio',
                                'romantic' => '💕 Romántico',
                                'mystical' => '🔮 Místico',
                                'professional' => '💼 Profesional',
                                'artistic' => '🎨 Artístico',
                                'other' => '❓ Otro',
                            ])
                            ->label('Estilo de Diseño'),
                        
                        Forms\Components\Select::make('font_family')
                            ->options([
                                'serif' => '📝 Serif',
                                'sans_serif' => '📝 Sans Serif',
                                'monospace' => '📝 Monospace',
                                'cursive' => '✍️ Cursiva',
                                'fantasy' => '🧙 Fantasía',
                                'other' => '❓ Otro',
                            ])
                            ->label('Familia de Fuente'),
                        
                        Forms\Components\Select::make('font_size')
                            ->options([
                                'small' => '📏 Pequeña',
                                'medium' => '📏 Mediana',
                                'large' => '📏 Grande',
                                'extra_large' => '📏 Extra Grande',
                                'other' => '❓ Otro',
                            ])
                            ->default('medium')
                            ->label('Tamaño de Fuente'),
                        
                        Forms\Components\Toggle::make('has_illustrations')
                            ->label('Tiene Ilustraciones')
                            ->default(false)
                            ->helperText('La colección incluye ilustraciones'),
                        
                        Forms\Components\Toggle::make('has_audio')
                            ->label('Tiene Audio')
                            ->default(false)
                            ->helperText('La colección incluye versión de audio'),
                        
                        Forms\Components\Toggle::make('has_video')
                            ->label('Tiene Video')
                            ->default(false)
                            ->helperText('La colección incluye contenido de video'),
                        
                        Forms\Components\Toggle::make('is_interactive')
                            ->label('Es Interactiva')
                            ->default(false)
                            ->helperText('La colección es interactiva'),
                    ])->columns(2),

                Forms\Components\Section::make('Estadísticas y Métricas')
                    ->schema([
                        Forms\Components\TextInput::make('view_count')
                            ->numeric()
                            ->label('Contador de Vistas')
                            ->placeholder('Número de veces vista...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de veces vista'),
                        
                        Forms\Components\TextInput::make('like_count')
                            ->numeric()
                            ->label('Contador de Me Gusta')
                            ->placeholder('Número de me gusta...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de me gusta'),
                        
                        Forms\Components\TextInput::make('share_count')
                            ->numeric()
                            ->label('Contador de Compartidos')
                            ->placeholder('Número de compartidos...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de compartidos'),
                        
                        Forms\Components\TextInput::make('favorite_count')
                            ->numeric()
                            ->label('Contador de Favoritos')
                            ->placeholder('Número de favoritos...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de favoritos'),
                        
                        Forms\Components\TextInput::make('download_count')
                            ->numeric()
                            ->label('Contador de Descargas')
                            ->placeholder('Número de descargas...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de descargas'),
                        
                        Forms\Components\TextInput::make('rating_average')
                            ->numeric()
                            ->label('Calificación Promedio')
                            ->placeholder('Calificación promedio...')
                            ->disabled()
                            ->helperText('Calificación promedio de la colección'),
                        
                        Forms\Components\TextInput::make('rating_count')
                            ->numeric()
                            ->label('Número de Calificaciones')
                            ->placeholder('Número de calificaciones...')
                            ->disabled()
                            ->helperText('Número de calificaciones recibidas'),
                        
                        Forms\Components\TextInput::make('comment_count')
                            ->numeric()
                            ->label('Número de Comentarios')
                            ->placeholder('Número de comentarios...')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de comentarios'),
                        
                        Forms\Components\TextInput::make('last_activity')
                            ->label('Última Actividad')
                            ->disabled()
                            ->helperText('Fecha de la última actividad'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Calidad')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => '📝 Borrador',
                                'active' => '✅ Activa',
                                'inactive' => '❌ Inactiva',
                                'review' => '👀 En Revisión',
                                'archived' => '📦 Archivada',
                                'flagged' => '🚩 Marcada',
                                'blocked' => '🚫 Bloqueada',
                                'completed' => '✅ Completada',
                                'in_progress' => '🚧 En Progreso',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('draft')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Colección importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular')
                            ->default(false)
                            ->helperText('Colección popular entre los usuarios'),
                        
                        Forms\Components\Toggle::make('is_new')
                            ->label('Nueva')
                            ->default(false)
                            ->helperText('Colección recién creada'),
                        
                        Forms\Components\Toggle::make('is_trending')
                            ->label('Trending')
                            ->default(false)
                            ->helperText('Colección en tendencia'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('La colección ha sido verificada'),
                        
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Aprobada')
                            ->default(false)
                            ->helperText('La colección ha sido aprobada'),
                        
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
                            ->placeholder('Persona que revisó la colección...'),
                        
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
                
                Tables\Columns\TextColumn::make('collection_name')
                    ->label('Colección')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Autor')
                    ->searchable()
                    ->limit(30)
                    ->weight('medium'),
                
                Tables\Columns\BadgeColumn::make('collection_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'personal',
                        'success' => 'thematic',
                        'warning' => 'author_based',
                        'info' => 'period_based',
                        'danger' => 'genre_based',
                        'secondary' => 'mood_based',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'personal' => '👤 Personal',
                        'thematic' => '🎯 Temática',
                        'author_based' => '✍️ Basada en Autor',
                        'period_based' => '📅 Basada en Período',
                        'genre_based' => '🎭 Basada en Género',
                        'mood_based' => '😊 Basada en Estado de Ánimo',
                        'occasion_based' => '🎉 Basada en Ocasión',
                        'learning_based' => '🎓 Basada en Aprendizaje',
                        'inspiration_based' => '💡 Basada en Inspiración',
                        'curated' => '✍️ Curada',
                        'community' => '👥 Comunidad',
                        'official' => '🏛️ Oficial',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('theme')
                    ->label('Tema')
                    ->colors([
                        'danger' => 'love',
                        'success' => 'friendship',
                        'warning' => 'success',
                        'info' => 'wisdom',
                        'primary' => 'inspiration',
                        'secondary' => 'motivation',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'love' => '❤️ Amor',
                        'friendship' => '🤝 Amistad',
                        'success' => '🏆 Éxito',
                        'wisdom' => '🧠 Sabiduría',
                        'inspiration' => '💡 Inspiración',
                        'motivation' => '🚀 Motivación',
                        'philosophy' => '🤔 Filosofía',
                        'religion' => '⛪ Religión',
                        'nature' => '🌿 Naturaleza',
                        'art' => '🎭 Arte',
                        'music' => '🎵 Música',
                        'literature' => '📚 Literatura',
                        'history' => '📜 Historia',
                        'science' => '🔬 Ciencia',
                        'technology' => '💻 Tecnología',
                        'business' => '💼 Negocios',
                        'education' => '🎓 Educación',
                        'health' => '🏥 Salud',
                        'family' => '👨‍👩‍👧‍👦 Familia',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'sports' => '⚽ Deportes',
                        'humor' => '😄 Humor',
                        'poetry' => '📝 Poesía',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('difficulty_level')
                    ->label('Nivel')
                    ->colors([
                        'success' => 'beginner',
                        'warning' => 'intermediate',
                        'danger' => 'advanced',
                        'primary' => 'expert',
                        'info' => 'all_levels',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beginner' => '🟢 Principiante',
                        'intermediate' => '🟡 Intermedio',
                        'advanced' => '🟠 Avanzado',
                        'expert' => '🔴 Experto',
                        'all_levels' => '🌈 Todos los Niveles',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('total_quotes')
                    ->label('Citas')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'secondary',
                        $state <= 10 => 'success',
                        $state <= 50 => 'info',
                        $state <= 100 => 'warning',
                        $state <= 500 => 'danger',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'secondary',
                        $state <= 100 => 'success',
                        $state <= 500 => 'info',
                        $state <= 1000 => 'warning',
                        $state <= 5000 => 'danger',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('like_count')
                    ->label('Me Gusta')
                    ->numeric()
                    ->sortable()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('rating_average')
                    ->label('Calificación')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 4.0 => 'info',
                        $state >= 3.5 => 'warning',
                        $state >= 3.0 => 'secondary',
                        $state >= 2.5 => 'danger',
                        default => 'danger',
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
                
                Tables\Columns\IconColumn::make('is_new')
                    ->label('Nueva')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_trending')
                    ->label('Trending')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificada')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Aprobada')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'danger' => 'inactive',
                        'info' => 'review',
                        'warning' => 'archived',
                        'primary' => 'flagged',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'review' => '👀 En Revisión',
                        'archived' => '📦 Archivada',
                        'flagged' => '🚩 Marcada',
                        'blocked' => '🚫 Bloqueada',
                        'completed' => '✅ Completada',
                        'in_progress' => '🚧 En Progreso',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
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
                Tables\Filters\SelectFilter::make('collection_type')
                    ->options([
                        'personal' => '👤 Personal',
                        'thematic' => '🎯 Temática',
                        'author_based' => '✍️ Basada en Autor',
                        'period_based' => '📅 Basada en Período',
                        'genre_based' => '🎭 Basada en Género',
                        'mood_based' => '😊 Basada en Estado de Ánimo',
                        'occasion_based' => '🎉 Basada en Ocasión',
                        'learning_based' => '🎓 Basada en Aprendizaje',
                        'inspiration_based' => '💡 Basada en Inspiración',
                        'curated' => '✍️ Curada',
                        'community' => '👥 Comunidad',
                        'official' => '🏛️ Oficial',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Colección'),
                
                Tables\Filters\SelectFilter::make('theme')
                    ->options([
                        'love' => '❤️ Amor',
                        'friendship' => '🤝 Amistad',
                        'success' => '🏆 Éxito',
                        'wisdom' => '🧠 Sabiduría',
                        'inspiration' => '💡 Inspiración',
                        'motivation' => '🚀 Motivación',
                        'philosophy' => '🤔 Filosofía',
                        'religion' => '⛪ Religión',
                        'nature' => '🌿 Naturaleza',
                        'art' => '🎭 Arte',
                        'music' => '🎵 Música',
                        'literature' => '📚 Literatura',
                        'history' => '📜 Historia',
                        'science' => '🔬 Ciencia',
                        'technology' => '💻 Tecnología',
                        'business' => '💼 Negocios',
                        'education' => '🎓 Educación',
                        'health' => '🏥 Salud',
                        'family' => '👨‍👩‍👧‍👦 Familia',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'sports' => '⚽ Deportes',
                        'humor' => '😄 Humor',
                        'poetry' => '📝 Poesía',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tema'),
                
                Tables\Filters\SelectFilter::make('difficulty_level')
                    ->options([
                        'beginner' => '🟢 Principiante',
                        'intermediate' => '🟡 Intermedio',
                        'advanced' => '🟠 Avanzado',
                        'expert' => '🔴 Experto',
                        'all_levels' => '🌈 Todos los Niveles',
                        'other' => '❓ Otro',
                    ])
                    ->label('Nivel de Dificultad'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'review' => '👀 En Revisión',
                        'archived' => '📦 Archivada',
                        'flagged' => '🚩 Marcada',
                        'blocked' => '🚫 Bloqueada',
                        'completed' => '✅ Completada',
                        'in_progress' => '🚧 En Progreso',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('popular_only')
                    ->label('Solo Populares')
                    ->query(fn (Builder $query): Builder => $query->where('is_popular', true)),
                
                Tables\Filters\Filter::make('new_only')
                    ->label('Solo Nuevas')
                    ->query(fn (Builder $query): Builder => $query->where('is_new', true)),
                
                Tables\Filters\Filter::make('trending_only')
                    ->label('Solo Trending')
                    ->query(fn (Builder $query): Builder => $query->where('is_trending', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('approved_only')
                    ->label('Solo Aprobadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_approved', true)),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('high_quote_count')
                    ->label('Muchas Citas (50+)')
                    ->query(fn (Builder $query): Builder => $query->where('total_quotes', '>=', 50)),
                
                Tables\Filters\Filter::make('high_rating')
                    ->label('Alta Calificación (4+)')
                    ->query(fn (Builder $query): Builder => $query->where('rating_average', '>=', 4.0)),
                
                Tables\Filters\Filter::make('high_views')
                    ->label('Muchas Vistas (1000+)')
                    ->query(fn (Builder $query): Builder => $query->where('view_count', '>=', 1000)),
                
                Tables\Filters\Filter::make('personal_collections')
                    ->label('Solo Personales')
                    ->query(fn (Builder $query): Builder => $query->where('collection_type', 'personal')),
                
                Tables\Filters\Filter::make('thematic_collections')
                    ->label('Solo Temáticas')
                    ->query(fn (Builder $query): Builder => $query->where('collection_type', 'thematic')),
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
                
                Tables\Actions\Action::make('toggle_popular')
                    ->label(fn ($record): string => $record->is_popular ? 'Quitar Popular' : 'Marcar Popular')
                    ->icon(fn ($record): string => $record->is_popular ? 'fas-fire' : 'far-fire')
                    ->action(function ($record): void {
                        $record->update(['is_popular' => !$record->is_popular]);
                    })
                    ->color(fn ($record): string => $record->is_popular ? 'success' : 'secondary'),
                
                Tables\Actions\Action::make('mark_verified')
                    ->label('Marcar como Verificada')
                    ->icon('fas-check-circle')
                    ->action(function ($record): void {
                        $record->update(['is_verified' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('approve_collection')
                    ->label('Aprobar')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['is_approved' => true, 'status' => 'active']);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('activate_collection')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_collection')
                    ->label('Desactivar')
                    ->icon('fas-pause')
                    ->action(function ($record): void {
                        $record->update(['status' => 'inactive']);
                    })
                    ->visible(fn ($record): bool => $record->status === 'active')
                    ->color('warning'),
                
                Tables\Actions\Action::make('view_quotes')
                    ->label('Ver Citas')
                    ->icon('fas-quote-left')
                    ->action(function ($record): void {
                        // Aquí se implementaría la navegación a las citas
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
                    
                    Tables\Actions\BulkAction::make('mark_popular')
                        ->label('Marcar como Populares')
                        ->icon('fas-fire')
                        ->action(function ($records): void {
                            $records->each->update(['is_popular' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_verified')
                        ->label('Marcar como Verificadas')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_verified' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('approve_all')
                        ->label('Aprobar Todas')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_approved' => true, 'status' => 'active']);
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
            'index' => Pages\ListQuoteCollections::route('/'),
            'create' => Pages\CreateQuoteCollection::route('/create'),
            'view' => Pages\ViewQuoteCollection::route('/{record}'),
            'edit' => Pages\EditQuoteCollection::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
