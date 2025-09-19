<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteCategoryResource\Pages;
use App\Models\QuoteCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteCategoryResource extends Resource
{
    protected static ?string $model = QuoteCategory::class;

    protected static ?string $navigationIcon = 'fas-tags';

    protected static ?string $navigationGroup = 'Contenido y Medios';

    protected static ?string $navigationLabel = 'Categorías de Citas';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Categoría de Citas';

    protected static ?string $pluralModelLabel = 'Categorías de Citas';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('category_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Categoría')
                            ->placeholder('Nombre de la categoría...'),
                        
                        Forms\Components\TextInput::make('category_code')
                            ->maxLength(100)
                            ->label('Código de Categoría')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción de la categoría...'),
                        
                        Forms\Components\Select::make('parent_category_id')
                            ->relationship('parentCategory', 'category_name')
                            ->searchable()
                            ->preload()
                            ->label('Categoría Padre')
                            ->placeholder('Selecciona categoría padre...'),
                        
                        Forms\Components\Select::make('category_type')
                            ->options([
                                'theme' => '🎨 Tema',
                                'emotion' => '😊 Emoción',
                                'philosophy' => '🤔 Filosofía',
                                'religion' => '⛪ Religión',
                                'politics' => '🏛️ Política',
                                'business' => '💼 Negocios',
                                'education' => '🎓 Educación',
                                'inspiration' => '💡 Inspiración',
                                'love' => '❤️ Amor',
                                'friendship' => '🤝 Amistad',
                                'success' => '🏆 Éxito',
                                'failure' => '💔 Fracaso',
                                'wisdom' => '🧠 Sabiduría',
                                'humor' => '😄 Humor',
                                'nature' => '🌿 Naturaleza',
                                'art' => '🎭 Arte',
                                'music' => '🎵 Música',
                                'literature' => '📚 Literatura',
                                'history' => '📜 Historia',
                                'science' => '🔬 Ciencia',
                                'technology' => '💻 Tecnología',
                                'health' => '🏥 Salud',
                                'family' => '👨‍👩‍👧‍👦 Familia',
                                'travel' => '✈️ Viajes',
                                'food' => '🍕 Comida',
                                'sports' => '⚽ Deportes',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Categoría'),
                        
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
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Audiencia Objetivo'),
                    ])->columns(2),

                Forms\Components\Section::make('Características y Metadatos')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->maxLength(100)
                            ->label('Icono')
                            ->placeholder('Nombre del icono...')
                            ->helperText('Nombre del icono FontAwesome o emoji'),
                        
                        Forms\Components\ColorPicker::make('color')
                            ->label('Color')
                            ->helperText('Color representativo de la categoría'),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->label('Orden de Clasificación')
                            ->placeholder('Orden para mostrar...')
                            ->default(0)
                            ->helperText('Orden para mostrar en listas'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Categoría importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular')
                            ->default(false)
                            ->helperText('Categoría popular entre los usuarios'),
                        
                        Forms\Components\Toggle::make('is_new')
                            ->label('Nueva')
                            ->default(false)
                            ->helperText('Categoría recién creada'),
                        
                        Forms\Components\Toggle::make('is_trending')
                            ->label('Trending')
                            ->default(false)
                            ->helperText('Categoría en tendencia'),
                        
                        Forms\Components\Toggle::make('is_educational')
                            ->label('Educativa')
                            ->default(false)
                            ->helperText('Categoría con valor educativo'),
                        
                        Forms\Components\Toggle::make('is_inspirational')
                            ->label('Inspiracional')
                            ->default(false)
                            ->helperText('Categoría que inspira'),
                        
                        Forms\Components\Toggle::make('is_controversial')
                            ->label('Controvertida')
                            ->default(false)
                            ->helperText('Categoría que puede generar debate'),
                    ])->columns(2),

                Forms\Components\Section::make('Contenido y Ejemplos')
                    ->schema([
                        Forms\Components\Textarea::make('example_quotes')
                            ->maxLength(1000)
                            ->label('Citas de Ejemplo')
                            ->rows(3)
                            ->placeholder('Algunas citas representativas...'),
                        
                        Forms\Components\Textarea::make('key_themes')
                            ->maxLength(500)
                            ->label('Temas Clave')
                            ->rows(2)
                            ->placeholder('Temas principales de esta categoría...'),
                        
                        Forms\Components\Textarea::make('famous_authors')
                            ->maxLength(500)
                            ->label('Autores Famosos')
                            ->rows(2)
                            ->placeholder('Autores conocidos de esta categoría...'),
                        
                        Forms\Components\Textarea::make('historical_context')
                            ->maxLength(500)
                            ->label('Contexto Histórico')
                            ->rows(2)
                            ->placeholder('Contexto histórico de la categoría...'),
                        
                        Forms\Components\Textarea::make('cultural_significance')
                            ->maxLength(500)
                            ->label('Significado Cultural')
                            ->rows(2)
                            ->placeholder('Significado cultural de la categoría...'),
                        
                        Forms\Components\Textarea::make('modern_relevance')
                            ->maxLength(500)
                            ->label('Relevancia Moderna')
                            ->rows(2)
                            ->placeholder('Relevancia en la actualidad...'),
                        
                        Forms\Components\Textarea::make('usage_guidelines')
                            ->maxLength(500)
                            ->label('Pautas de Uso')
                            ->rows(2)
                            ->placeholder('Pautas para usar citas de esta categoría...'),
                        
                        Forms\Components\Textarea::make('misconceptions')
                            ->maxLength(500)
                            ->label('Conceptos Erróneos')
                            ->rows(2)
                            ->placeholder('Conceptos erróneos sobre esta categoría...'),
                    ])->columns(1),

                Forms\Components\Section::make('Estadísticas y Métricas')
                    ->schema([
                        Forms\Components\TextInput::make('quote_count')
                            ->numeric()
                            ->label('Número de Citas')
                            ->placeholder('Número de citas en esta categoría...')
                            ->disabled()
                            ->helperText('Calculado automáticamente'),
                        
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
                            ->label('Contador de Compartidos')
                            ->numeric()
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
                        
                        Forms\Components\TextInput::make('rating_average')
                            ->numeric()
                            ->label('Calificación Promedio')
                            ->placeholder('Calificación promedio...')
                            ->disabled()
                            ->helperText('Calificación promedio de la categoría'),
                        
                        Forms\Components\TextInput::make('rating_count')
                            ->numeric()
                            ->label('Número de Calificaciones')
                            ->placeholder('Número de calificaciones...')
                            ->disabled()
                            ->helperText('Número de calificaciones recibidas'),
                        
                        Forms\Components\TextInput::make('last_activity')
                            ->label('Última Actividad')
                            ->disabled()
                            ->helperText('Fecha de la última actividad'),
                    ])->columns(2),

                Forms\Components\Section::make('SEO y Metadatos')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->maxLength(255)
                            ->label('Meta Título')
                            ->placeholder('Título para SEO...'),
                        
                        Forms\Components\Textarea::make('meta_description')
                            ->maxLength(500)
                            ->label('Meta Descripción')
                            ->rows(2)
                            ->placeholder('Descripción para SEO...'),
                        
                        Forms\Components\TextInput::make('slug')
                            ->maxLength(255)
                            ->label('Slug')
                            ->placeholder('URL amigable...'),
                        
                        Forms\Components\TagsInput::make('keywords')
                            ->label('Palabras Clave')
                            ->placeholder('Agregar palabras clave...'),
                        
                        Forms\Components\TextInput::make('canonical_url')
                            ->maxLength(500)
                            ->label('URL Canónica')
                            ->placeholder('URL canónica...'),
                        
                        Forms\Components\Select::make('language')
                            ->options([
                                'es' => '🇪🇸 Español',
                                'en' => '🇺🇸 Inglés',
                                'fr' => '🇫🇷 Francés',
                                'de' => '🇩🇪 Alemán',
                                'it' => '🇮🇹 Italiano',
                                'pt' => '🇵🇹 Portugués',
                                'ca' => '🇪🇸 Catalán',
                                'eu' => '🇪🇸 Euskera',
                                'gl' => '🇪🇸 Gallego',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('es')
                            ->label('Idioma'),
                        
                        Forms\Components\Toggle::make('is_translated')
                            ->label('Es Traducción')
                            ->default(false)
                            ->helperText('La categoría es una traducción'),
                        
                        Forms\Components\TextInput::make('original_language')
                            ->maxLength(10)
                            ->label('Idioma Original')
                            ->placeholder('Idioma original si es traducción...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_translated')),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Moderación')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '✅ Activa',
                                'inactive' => '❌ Inactiva',
                                'pending' => '⏳ Pendiente',
                                'review' => '👀 En Revisión',
                                'archived' => '📦 Archivada',
                                'flagged' => '🚩 Marcada',
                                'blocked' => '🚫 Bloqueada',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('active')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('La categoría ha sido verificada'),
                        
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Aprobada')
                            ->default(false)
                            ->helperText('La categoría ha sido aprobada'),
                        
                        Forms\Components\Toggle::make('is_flagged')
                            ->label('Marcada')
                            ->default(false)
                            ->helperText('La categoría ha sido marcada'),
                        
                        Forms\Components\TextInput::make('flag_reason')
                            ->maxLength(255)
                            ->label('Razón de la Marca')
                            ->placeholder('Razón por la que fue marcada...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_flagged')),
                        
                        Forms\Components\Select::make('moderator_id')
                            ->relationship('moderator', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Moderador')
                            ->placeholder('Moderador asignado...'),
                        
                        Forms\Components\DatePicker::make('moderation_date')
                            ->label('Fecha de Moderación')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\Textarea::make('moderation_notes')
                            ->maxLength(500)
                            ->label('Notas de Moderación')
                            ->rows(2)
                            ->placeholder('Notas del moderador...'),
                        
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
                            ->placeholder('Persona que revisó la categoría...'),
                        
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
                
                Tables\Columns\TextColumn::make('category_name')
                    ->label('Categoría')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('parentCategory.category_name')
                    ->label('Categoría Padre')
                    ->searchable()
                    ->limit(30)
                    ->weight('medium'),
                
                Tables\Columns\BadgeColumn::make('category_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'theme',
                        'success' => 'emotion',
                        'warning' => 'philosophy',
                        'info' => 'religion',
                        'danger' => 'politics',
                        'secondary' => 'business',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'theme' => '🎨 Tema',
                        'emotion' => '😊 Emoción',
                        'philosophy' => '🤔 Filosofía',
                        'religion' => '⛪ Religión',
                        'politics' => '🏛️ Política',
                        'business' => '💼 Negocios',
                        'education' => '🎓 Educación',
                        'inspiration' => '💡 Inspiración',
                        'love' => '❤️ Amor',
                        'friendship' => '🤝 Amistad',
                        'success' => '🏆 Éxito',
                        'failure' => '💔 Fracaso',
                        'wisdom' => '🧠 Sabiduría',
                        'humor' => '😄 Humor',
                        'nature' => '🌿 Naturaleza',
                        'art' => '🎭 Arte',
                        'music' => '🎵 Música',
                        'literature' => '📚 Literatura',
                        'history' => '📜 Historia',
                        'science' => '🔬 Ciencia',
                        'technology' => '💻 Tecnología',
                        'health' => '🏥 Salud',
                        'family' => '👨‍👩‍👧‍👦 Familia',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'sports' => '⚽ Deportes',
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
                
                Tables\Columns\BadgeColumn::make('target_audience')
                    ->label('Audiencia')
                    ->colors([
                        'success' => 'all_ages',
                        'info' => 'children',
                        'warning' => 'youth',
                        'primary' => 'adults',
                        'secondary' => 'students',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all_ages' => '👥 Todas las Edades',
                        'children' => '👶 Niños',
                        'youth' => '🧑‍🎓 Jóvenes',
                        'adults' => '👨‍💼 Adultos',
                        'seniors' => '👴 Mayores',
                        'students' => '🎓 Estudiantes',
                        'professionals' => '💼 Profesionales',
                        'academics' => '🎓 Académicos',
                        'spiritual_seekers' => '🕯️ Buscadores Espirituales',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icono')
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('quote_count')
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
                
                Tables\Columns\IconColumn::make('is_educational')
                    ->label('Educativa')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_inspirational')
                    ->label('Inspiracional')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_controversial')
                    ->label('Controvertida')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'info' => 'pending',
                        'warning' => 'review',
                        'dark' => 'archived',
                        'primary' => 'flagged',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'pending' => '⏳ Pendiente',
                        'review' => '👀 En Revisión',
                        'archived' => '📦 Archivada',
                        'flagged' => '🚩 Marcada',
                        'blocked' => '🚫 Bloqueada',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
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
                Tables\Filters\SelectFilter::make('category_type')
                    ->options([
                        'theme' => '🎨 Tema',
                        'emotion' => '😊 Emoción',
                        'philosophy' => '🤔 Filosofía',
                        'religion' => '⛪ Religión',
                        'politics' => '🏛️ Política',
                        'business' => '💼 Negocios',
                        'education' => '🎓 Educación',
                        'inspiration' => '💡 Inspiración',
                        'love' => '❤️ Amor',
                        'friendship' => '🤝 Amistad',
                        'success' => '🏆 Éxito',
                        'failure' => '💔 Fracaso',
                        'wisdom' => '🧠 Sabiduría',
                        'humor' => '😄 Humor',
                        'nature' => '🌿 Naturaleza',
                        'art' => '🎭 Arte',
                        'music' => '🎵 Música',
                        'literature' => '📚 Literatura',
                        'history' => '📜 Historia',
                        'science' => '🔬 Ciencia',
                        'technology' => '💻 Tecnología',
                        'health' => '🏥 Salud',
                        'family' => '👨‍👩‍👧‍👦 Familia',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'sports' => '⚽ Deportes',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Categoría'),
                
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
                
                Tables\Filters\SelectFilter::make('target_audience')
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
                        'other' => '❓ Otro',
                    ])
                    ->label('Audiencia Objetivo'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'pending' => '⏳ Pendiente',
                        'review' => '👀 En Revisión',
                        'archived' => '📦 Archivada',
                        'flagged' => '🚩 Marcada',
                        'blocked' => '🚫 Bloqueada',
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
                
                Tables\Filters\Filter::make('educational_only')
                    ->label('Solo Educativas')
                    ->query(fn (Builder $query): Builder => $query->where('is_educational', true)),
                
                Tables\Filters\Filter::make('inspirational_only')
                    ->label('Solo Inspiracionales')
                    ->query(fn (Builder $query): Builder => $query->where('is_inspirational', true)),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('parent_categories')
                    ->label('Solo Categorías Padre')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_category_id')),
                
                Tables\Filters\Filter::make('sub_categories')
                    ->label('Solo Subcategorías')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('parent_category_id')),
                
                Tables\Filters\Filter::make('high_quote_count')
                    ->label('Muchas Citas (50+)')
                    ->query(fn (Builder $query): Builder => $query->where('quote_count', '>=', 50)),
                
                Tables\Filters\Filter::make('high_rating')
                    ->label('Alta Calificación (4+)')
                    ->query(fn (Builder $query): Builder => $query->where('rating_average', '>=', 4.0)),
                
                Tables\Filters\Filter::make('high_views')
                    ->label('Muchas Vistas (1000+)')
                    ->query(fn (Builder $query): Builder => $query->where('view_count', '>=', 1000)),
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
                
                Tables\Actions\Action::make('approve_category')
                    ->label('Aprobar')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['is_approved' => true, 'status' => 'active']);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('flag_category')
                    ->label('Marcar')
                    ->icon('fas-flag')
                    ->action(function ($record): void {
                        $record->update(['is_flagged' => true, 'status' => 'review']);
                    })
                    ->visible(fn ($record): bool => !$record->is_flagged)
                    ->color('warning'),
                
                Tables\Actions\Action::make('activate_category')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_category')
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
            ->defaultSort('sort_order', 'asc')
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
            'index' => Pages\ListQuoteCategories::route('/'),
            'create' => Pages\CreateQuoteCategory::route('/create'),
            'view' => Pages\ViewQuoteCategory::route('/{record}'),
            'edit' => Pages\EditQuoteCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
