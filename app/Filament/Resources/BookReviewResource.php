<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookReviewResource\Pages;
use App\Filament\Resources\BookReviewResource\RelationManagers;
use App\Models\BookReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookReviewResource extends Resource
{
    protected static ?string $model = BookReview::class;

    protected static ?string $navigationIcon = 'fas-star';

    protected static ?string $navigationGroup = 'Biblioteca y Literatura';

    protected static ?string $navigationLabel = 'Reseñas de Libros';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Reseña de Libro';

    protected static ?string $pluralModelLabel = 'Reseñas de Libros';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('review_title')
                            ->required()
                            ->maxLength(255)
                            ->label('Título de la Reseña')
                            ->placeholder('Título descriptivo de la reseña...'),
                        
                        Forms\Components\Select::make('book_id')
                            ->relationship('book', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Libro')
                            ->placeholder('Selecciona el libro...'),
                        
                        Forms\Components\Select::make('reviewer_id')
                            ->relationship('reviewer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Reseñador')
                            ->placeholder('Selecciona el reseñador...'),
                        
                        Forms\Components\Textarea::make('summary')
                            ->required()
                            ->maxLength(500)
                            ->label('Resumen')
                            ->rows(3)
                            ->placeholder('Resumen breve de la reseña...'),
                        
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->maxLength(5000)
                            ->label('Contenido de la Reseña')
                            ->rows(8)
                            ->placeholder('Contenido completo de la reseña...'),
                        
                        Forms\Components\Select::make('review_type')
                            ->options([
                                'professional' => '👨‍💼 Profesional',
                                'academic' => '🎓 Académica',
                                'reader' => '👤 Lector',
                                'critic' => '📝 Crítica',
                                'blog' => '📱 Blog',
                                'social_media' => '📱 Redes Sociales',
                                'newspaper' => '📰 Periódico',
                                'magazine' => '📖 Revista',
                                'website' => '🌐 Sitio Web',
                                'podcast' => '🎧 Podcast',
                                'video' => '🎥 Video',
                                'radio' => '📻 Radio',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Reseña'),
                        
                        Forms\Components\Select::make('audience')
                            ->options([
                                'general' => '👥 General',
                                'children' => '👶 Infantil',
                                'young_adult' => '🧑‍🎓 Joven Adulto',
                                'adult' => '👨‍💼 Adulto',
                                'academic' => '🎓 Académico',
                                'professional' => '💼 Profesional',
                                'specialist' => '🔬 Especialista',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Audiencia Objetivo'),
                    ])->columns(2),

                Forms\Components\Section::make('Evaluación y Calificación')
                    ->schema([
                        Forms\Components\Select::make('overall_rating')
                            ->options([
                                1 => '⭐ 1 - Muy Pobre',
                                2 => '⭐⭐ 2 - Pobre',
                                3 => '⭐⭐⭐ 3 - Regular',
                                4 => '⭐⭐⭐⭐ 4 - Buena',
                                5 => '⭐⭐⭐⭐⭐ 5 - Excelente',
                            ])
                            ->required()
                            ->label('Calificación General')
                            ->default(3),
                        
                        Forms\Components\Select::make('plot_rating')
                            ->options([
                                1 => '⭐ 1 - Muy Pobre',
                                2 => '⭐⭐ 2 - Pobre',
                                3 => '⭐⭐⭐ 3 - Regular',
                                4 => '⭐⭐⭐⭐ 4 - Buena',
                                5 => '⭐⭐⭐⭐⭐ 5 - Excelente',
                            ])
                            ->label('Calificación de Trama'),
                        
                        Forms\Components\Select::make('character_rating')
                            ->options([
                                1 => '⭐ 1 - Muy Pobre',
                                2 => '⭐⭐ 2 - Pobre',
                                3 => '⭐⭐⭐ 3 - Regular',
                                4 => '⭐⭐⭐⭐ 4 - Buena',
                                5 => '⭐⭐⭐⭐⭐ 5 - Excelente',
                            ])
                            ->label('Calificación de Personajes'),
                        
                        Forms\Components\Select::make('writing_style_rating')
                            ->options([
                                1 => '⭐ 1 - Muy Pobre',
                                2 => '⭐⭐ 2 - Pobre',
                                3 => '⭐⭐⭐ 3 - Regular',
                                4 => '⭐⭐⭐⭐ 4 - Buena',
                                5 => '⭐⭐⭐⭐⭐ 5 - Excelente',
                            ])
                            ->label('Calificación de Estilo de Escritura'),
                        
                        Forms\Components\Select::make('pacing_rating')
                            ->options([
                                1 => '⭐ 1 - Muy Pobre',
                                2 => '⭐⭐ 2 - Pobre',
                                3 => '⭐⭐⭐ 3 - Regular',
                                4 => '⭐⭐⭐⭐ 4 - Buena',
                                5 => '⭐⭐⭐⭐⭐ 5 - Excelente',
                            ])
                            ->label('Calificación de Ritmo'),
                        
                        Forms\Components\Select::make('originality_rating')
                            ->options([
                                1 => '⭐ 1 - Muy Pobre',
                                2 => '⭐⭐ 2 - Pobre',
                                3 => '⭐⭐⭐ 3 - Regular',
                                4 => '⭐⭐⭐⭐ 4 - Buena',
                                5 => '⭐⭐⭐⭐⭐ 5 - Excelente',
                            ])
                            ->label('Calificación de Originalidad'),
                        
                        Forms\Components\TextInput::make('average_rating')
                            ->numeric()
                            ->label('Calificación Promedio')
                            ->disabled()
                            ->helperText('Calculada automáticamente'),
                        
                        Forms\Components\TextInput::make('rating_count')
                            ->numeric()
                            ->label('Número de Calificaciones')
                            ->disabled()
                            ->helperText('Calculado automáticamente'),
                    ])->columns(2),

                Forms\Components\Section::make('Aspectos Específicos')
                    ->schema([
                        Forms\Components\Textarea::make('plot_summary')
                            ->maxLength(1000)
                            ->label('Resumen de la Trama')
                            ->rows(3)
                            ->placeholder('Resumen de la trama del libro...'),
                        
                        Forms\Components\Textarea::make('character_analysis')
                            ->maxLength(1000)
                            ->label('Análisis de Personajes')
                            ->rows(3)
                            ->placeholder('Análisis de los personajes principales...'),
                        
                        Forms\Components\Textarea::make('themes')
                            ->maxLength(500)
                            ->label('Temas Principales')
                            ->rows(2)
                            ->placeholder('Temas principales explorados en el libro...'),
                        
                        Forms\Components\Textarea::make('writing_style')
                            ->maxLength(500)
                            ->label('Estilo de Escritura')
                            ->rows(2)
                            ->placeholder('Descripción del estilo de escritura...'),
                        
                        Forms\Components\Textarea::make('strengths')
                            ->maxLength(500)
                            ->label('Fortalezas')
                            ->rows(2)
                            ->placeholder('Aspectos positivos del libro...'),
                        
                        Forms\Components\Textarea::make('weaknesses')
                            ->maxLength(500)
                            ->label('Debilidades')
                            ->rows(2)
                            ->placeholder('Aspectos que podrían mejorarse...'),
                        
                        Forms\Components\Textarea::make('recommendations')
                            ->maxLength(500)
                            ->label('Recomendaciones')
                            ->rows(2)
                            ->placeholder('Para quién recomendarías este libro...'),
                        
                        Forms\Components\Textarea::make('comparisons')
                            ->maxLength(500)
                            ->label('Comparaciones')
                            ->rows(2)
                            ->placeholder('Comparaciones con otros libros...'),
                    ])->columns(1),

                Forms\Components\Section::make('Información de Publicación')
                    ->schema([
                        Forms\Components\DatePicker::make('review_date')
                            ->required()
                            ->label('Fecha de la Reseña')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha cuando se escribió la reseña'),
                        
                        Forms\Components\TextInput::make('publication_source')
                            ->maxLength(255)
                            ->label('Fuente de Publicación')
                            ->placeholder('Revista, periódico, sitio web...'),
                        
                        Forms\Components\UrlInput::make('publication_url')
                            ->label('URL de Publicación')
                            ->placeholder('https://...'),
                        
                        Forms\Components\TextInput::make('issue_number')
                            ->maxLength(100)
                            ->label('Número de Edición')
                            ->placeholder('Número de revista o edición...'),
                        
                        Forms\Components\TextInput::make('page_numbers')
                            ->maxLength(100)
                            ->label('Números de Página')
                            ->placeholder('Páginas donde aparece la reseña...'),
                        
                        Forms\Components\TextInput::make('isbn_reference')
                            ->maxLength(100)
                            ->label('Referencia ISBN')
                            ->placeholder('ISBN del libro reseñado...'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Reseña importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('La reseña ha sido verificada'),
                        
                        Forms\Components\Toggle::make('is_anonymous')
                            ->label('Anónima')
                            ->default(false)
                            ->helperText('Reseña publicada de forma anónima'),
                    ])->columns(2),

                Forms\Components\Section::make('Metadatos y SEO')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->placeholder('Agregar etiquetas...'),
                        
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
                            ->helperText('La reseña es una traducción'),
                        
                        Forms\Components\TextInput::make('original_language')
                            ->maxLength(10)
                            ->label('Idioma Original')
                            ->placeholder('Idioma original si es traducción...'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Moderación')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => '📝 Borrador',
                                'pending' => '⏳ Pendiente',
                                'published' => '✅ Publicada',
                                'rejected' => '❌ Rechazada',
                                'archived' => '📦 Archivada',
                                'under_review' => '👀 En Revisión',
                                'needs_revision' => '✏️ Necesita Revisión',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('draft')
                            ->label('Estado'),
                        
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
                        
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Aprobada')
                            ->default(false)
                            ->helperText('La reseña ha sido aprobada'),
                        
                        Forms\Components\Toggle::make('is_flagged')
                            ->label('Marcada')
                            ->default(false)
                            ->helperText('La reseña ha sido marcada para revisión'),
                        
                        Forms\Components\TextInput::make('flag_reason')
                            ->maxLength(255)
                            ->label('Razón de la Marca')
                            ->placeholder('Razón por la que fue marcada...'),
                        
                        Forms\Components\TextInput::make('view_count')
                            ->numeric()
                            ->label('Contador de Vistas')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de veces vista'),
                        
                        Forms\Components\TextInput::make('like_count')
                            ->numeric()
                            ->label('Contador de Me Gusta')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de me gusta'),
                        
                        Forms\Components\TextInput::make('dislike_count')
                            ->numeric()
                            ->label('Contador de No Me Gusta')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de no me gusta'),
                        
                        Forms\Components\TextInput::make('comment_count')
                            ->numeric()
                            ->label('Contador de Comentarios')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de comentarios'),
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
                
                Tables\Columns\TextColumn::make('review_title')
                    ->label('Reseña')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Libro')
                    ->searchable()
                    ->limit(30)
                    ->weight('medium')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Reseñador')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\BadgeColumn::make('review_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'professional',
                        'success' => 'academic',
                        'warning' => 'reader',
                        'info' => 'critic',
                        'danger' => 'blog',
                        'secondary' => 'social_media',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'professional' => '👨‍💼 Profesional',
                        'academic' => '🎓 Académica',
                        'reader' => '👤 Lector',
                        'critic' => '📝 Crítica',
                        'blog' => '📱 Blog',
                        'social_media' => '📱 Redes Sociales',
                        'newspaper' => '📰 Periódico',
                        'magazine' => '📖 Revista',
                        'website' => '🌐 Sitio Web',
                        'podcast' => '🎧 Podcast',
                        'video' => '🎥 Video',
                        'radio' => '📻 Radio',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('overall_rating')
                    ->label('Calificación')
                    ->colors([
                        'danger' => 1,
                        'warning' => 2,
                        'secondary' => 3,
                        'info' => 4,
                        'success' => 5,
                    ])
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state) . ' ' . $state . '/5'),
                
                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Promedio')
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
                
                Tables\Columns\TextColumn::make('rating_count')
                    ->label('Calificaciones')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'info',
                        $state >= 20 => 'warning',
                        $state >= 10 => 'secondary',
                        $state >= 1 => 'danger',
                        default => 'secondary',
                    }),
                
                Tables\Columns\BadgeColumn::make('audience')
                    ->label('Audiencia')
                    ->colors([
                        'success' => 'general',
                        'info' => 'children',
                        'warning' => 'young_adult',
                        'primary' => 'adult',
                        'secondary' => 'academic',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'general' => '👥 General',
                        'children' => '👶 Infantil',
                        'young_adult' => '🧑‍🎓 Joven Adulto',
                        'adult' => '👨‍💼 Adulto',
                        'academic' => '🎓 Académico',
                        'professional' => '💼 Profesional',
                        'specialist' => '🔬 Especialista',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('review_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('publication_source')
                    ->label('Fuente')
                    ->limit(20)
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'info' => 'pending',
                        'success' => 'published',
                        'danger' => 'rejected',
                        'dark' => 'archived',
                        'warning' => 'under_review',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'pending' => '⏳ Pendiente',
                        'published' => '✅ Publicada',
                        'rejected' => '❌ Rechazada',
                        'archived' => '📦 Archivada',
                        'under_review' => '👀 En Revisión',
                        'needs_revision' => '✏️ Necesita Revisión',
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
                
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Aprobada')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_flagged')
                    ->label('Marcada')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 1000 => 'success',
                        $state >= 500 => 'info',
                        $state >= 100 => 'warning',
                        $state >= 50 => 'secondary',
                        $state >= 10 => 'danger',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('like_count')
                    ->label('Me Gusta')
                    ->numeric()
                    ->sortable()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('dislike_count')
                    ->label('No Me Gusta')
                    ->numeric()
                    ->sortable()
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('comment_count')
                    ->label('Comentarios')
                    ->numeric()
                    ->sortable()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('review_type')
                    ->options([
                        'professional' => '👨‍💼 Profesional',
                        'academic' => '🎓 Académica',
                        'reader' => '👤 Lector',
                        'critic' => '📝 Crítica',
                        'blog' => '📱 Blog',
                        'social_media' => '📱 Redes Sociales',
                        'newspaper' => '📰 Periódico',
                        'magazine' => '📖 Revista',
                        'website' => '🌐 Sitio Web',
                        'podcast' => '🎧 Podcast',
                        'video' => '🎥 Video',
                        'radio' => '📻 Radio',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Reseña'),
                
                Tables\Filters\SelectFilter::make('audience')
                    ->options([
                        'general' => '👥 General',
                        'children' => '👶 Infantil',
                        'young_adult' => '🧑‍🎓 Joven Adulto',
                        'adult' => '👨‍💼 Adulto',
                        'academic' => '🎓 Académico',
                        'professional' => '💼 Profesional',
                        'specialist' => '🔬 Especialista',
                        'other' => '❓ Otro',
                    ])
                    ->label('Audiencia Objetivo'),
                
                Tables\Filters\SelectFilter::make('overall_rating')
                    ->options([
                        1 => '⭐ 1 - Muy Pobre',
                        2 => '⭐⭐ 2 - Pobre',
                        3 => '⭐⭐⭐ 3 - Regular',
                        4 => '⭐⭐⭐⭐ 4 - Buena',
                        5 => '⭐⭐⭐⭐⭐ 5 - Excelente',
                    ])
                    ->label('Calificación General'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => '📝 Borrador',
                        'pending' => '⏳ Pendiente',
                        'published' => '✅ Publicada',
                        'rejected' => '❌ Rechazada',
                        'archived' => '📦 Archivada',
                        'under_review' => '👀 En Revisión',
                        'needs_revision' => '✏️ Necesita Revisión',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('approved_only')
                    ->label('Solo Aprobadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_approved', true)),
                
                Tables\Filters\Filter::make('flagged_only')
                    ->label('Solo Marcadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_flagged', true)),
                
                Tables\Filters\Filter::make('high_rated')
                    ->label('Alta Calificación (4-5)')
                    ->query(fn (Builder $query): Builder => $query->where('overall_rating', '>=', 4)),
                
                Tables\Filters\Filter::make('low_rated')
                    ->label('Baja Calificación (1-2)')
                    ->query(fn (Builder $query): Builder => $query->where('overall_rating', '<=', 2)),
                
                Tables\Filters\Filter::make('recent_reviews')
                    ->label('Reseñas Recientes (30 días)')
                    ->query(fn (Builder $query): Builder => $query->where('review_date', '>=', now()->subDays(30))),
                
                Tables\Filters\Filter::make('popular_reviews')
                    ->label('Reseñas Populares (100+ vistas)')
                    ->query(fn (Builder $query): Builder => $query->where('view_count', '>=', 100)),
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
                
                Tables\Actions\Action::make('approve_review')
                    ->label('Aprobar')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['is_approved' => true, 'status' => 'published']);
                    })
                    ->visible(fn ($record): bool => !$record->is_approved)
                    ->color('success'),
                
                Tables\Actions\Action::make('reject_review')
                    ->label('Rechazar')
                    ->icon('fas-times')
                    ->action(function ($record): void {
                        $record->update(['status' => 'rejected']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'rejected')
                    ->color('danger'),
                
                Tables\Actions\Action::make('flag_review')
                    ->label('Marcar para Revisión')
                    ->icon('fas-flag')
                    ->action(function ($record): void {
                        $record->update(['is_flagged' => true, 'status' => 'under_review']);
                    })
                    ->visible(fn ($record): bool => !$record->is_flagged)
                    ->color('warning'),
                
                Tables\Actions\Action::make('publish_review')
                    ->label('Publicar')
                    ->icon('fas-globe')
                    ->action(function ($record): void {
                        $record->update(['status' => 'published']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'published')
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
                            $records->each->update(['is_approved' => true, 'status' => 'published']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('publish_all')
                        ->label('Publicar Todas')
                        ->icon('fas-globe')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'published']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('flag_all')
                        ->label('Marcar Todas para Revisión')
                        ->icon('fas-flag')
                        ->action(function ($records): void {
                            $records->each->update(['is_flagged' => true, 'status' => 'under_review']);
                        })
                        ->color('warning'),
                ]),
            ])
            ->defaultSort('review_date', 'desc')
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
            'index' => Pages\ListBookReviews::route('/'),
            'create' => Pages\CreateBookReview::route('/create'),
            'view' => Pages\ViewBookReview::route('/{record}'),
            'edit' => Pages\EditBookReview::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
