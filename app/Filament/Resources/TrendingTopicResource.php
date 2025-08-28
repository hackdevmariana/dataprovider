<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrendingTopicResource\Pages;
use App\Filament\Resources\TrendingTopicResource\RelationManagers;
use App\Models\TrendingTopic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrendingTopicResource extends Resource
{
    protected static ?string $model = TrendingTopic::class;

    protected static ?string $navigationIcon = 'fas-fire';

    protected static ?string $navigationGroup = 'Noticias y Tendencias';

    protected static ?string $navigationLabel = 'Temas Trending';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Tema Trending';

    protected static ?string $pluralModelLabel = 'Temas Trending';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('topic_title')
                            ->required()
                            ->maxLength(255)
                            ->label('Título del Tema')
                            ->placeholder('Título del tema trending...'),
                        
                        Forms\Components\TextInput::make('topic_keyword')
                            ->required()
                            ->maxLength(100)
                            ->label('Palabra Clave')
                            ->placeholder('Palabra clave principal...'),
                        
                        Forms\Components\TextInput::make('topic_hashtag')
                            ->maxLength(100)
                            ->label('Hashtag')
                            ->placeholder('#hashtag...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción del tema trending...'),
                        
                        Forms\Components\Select::make('topic_category')
                            ->options([
                                'politics' => '🏛️ Política',
                                'technology' => '💻 Tecnología',
                                'entertainment' => '🎬 Entretenimiento',
                                'sports' => '⚽ Deportes',
                                'business' => '💼 Negocios',
                                'health' => '🏥 Salud',
                                'science' => '🔬 Ciencia',
                                'environment' => '🌍 Medio Ambiente',
                                'education' => '🎓 Educación',
                                'culture' => '🎨 Cultura',
                                'social_issues' => '🤝 Temas Sociales',
                                'crime' => '🚨 Crimen',
                                'weather' => '🌤️ Clima',
                                'travel' => '✈️ Viajes',
                                'food' => '🍕 Comida',
                                'fashion' => '👗 Moda',
                                'automotive' => '🚗 Automóviles',
                                'finance' => '💰 Finanzas',
                                'real_estate' => '🏠 Bienes Raíces',
                                'energy' => '⚡ Energía',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría del Tema'),
                        
                        Forms\Components\Select::make('topic_type')
                            ->options([
                                'news' => '📰 Noticia',
                                'event' => '📅 Evento',
                                'person' => '👤 Persona',
                                'place' => '📍 Lugar',
                                'product' => '🛍️ Producto',
                                'service' => '🔧 Servicio',
                                'organization' => '🏢 Organización',
                                'movement' => '🔄 Movimiento',
                                'trend' => '📈 Tendencia',
                                'controversy' => '⚖️ Controversia',
                                'achievement' => '🏆 Logro',
                                'discovery' => '🔍 Descubrimiento',
                                'announcement' => '📢 Anuncio',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Tema'),
                        
                        Forms\Components\Select::make('sentiment')
                            ->options([
                                'positive' => '😊 Positivo',
                                'negative' => '😞 Negativo',
                                'neutral' => '😐 Neutral',
                                'mixed' => '🤔 Mixto',
                                'controversial' => '⚖️ Controvertido',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Sentimiento'),
                        
                        Forms\Components\Select::make('urgency_level')
                            ->options([
                                'low' => '🟢 Baja',
                                'medium' => '🟡 Media',
                                'high' => '🟠 Alta',
                                'critical' => '🔴 Crítica',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('medium')
                            ->label('Nivel de Urgencia'),
                    ])->columns(2),

                Forms\Components\Section::make('Métricas y Popularidad')
                    ->schema([
                        Forms\Components\TextInput::make('search_volume')
                            ->numeric()
                            ->label('Volumen de Búsqueda')
                            ->placeholder('Número de búsquedas...'),
                        
                        Forms\Components\TextInput::make('social_mentions')
                            ->numeric()
                            ->label('Menciones en Redes Sociales')
                            ->placeholder('Número de menciones...'),
                        
                        Forms\Components\TextInput::make('news_mentions')
                            ->numeric()
                            ->label('Menciones en Noticias')
                            ->placeholder('Número de menciones en noticias...'),
                        
                        Forms\Components\TextInput::make('trending_score')
                            ->numeric()
                            ->label('Puntuación Trending')
                            ->placeholder('Puntuación de trending...')
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('engagement_rate')
                            ->numeric()
                            ->label('Tasa de Engagement (%)')
                            ->placeholder('Porcentaje de engagement...')
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('reach_estimate')
                            ->numeric()
                            ->label('Estimación de Alcance')
                            ->placeholder('Estimación del alcance...'),
                        
                        Forms\Components\TextInput::make('impression_count')
                            ->numeric()
                            ->label('Contador de Impresiones')
                            ->placeholder('Número de impresiones...'),
                        
                        Forms\Components\TextInput::make('click_count')
                            ->numeric()
                            ->label('Contador de Clics')
                            ->placeholder('Número de clics...'),
                        
                        Forms\Components\TextInput::make('share_count')
                            ->numeric()
                            ->label('Contador de Compartidos')
                            ->placeholder('Número de compartidos...'),
                        
                        Forms\Components\TextInput::make('comment_count')
                            ->numeric()
                            ->label('Contador de Comentarios')
                            ->placeholder('Número de comentarios...'),
                        
                        Forms\Components\TextInput::make('like_count')
                            ->numeric()
                            ->label('Contador de Me Gusta')
                            ->placeholder('Número de me gusta...'),
                        
                        Forms\Components\TextInput::make('retweet_count')
                            ->numeric()
                            ->label('Contador de Retweets')
                            ->placeholder('Número de retweets...'),
                    ])->columns(2),

                Forms\Components\Section::make('Período y Duración')
                    ->schema([
                        Forms\Components\DateTimePicker::make('trending_start')
                            ->required()
                            ->label('Inicio del Trending')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Cuándo comenzó a ser trending'),
                        
                        Forms\Components\DateTimePicker::make('trending_peak')
                            ->label('Pico del Trending')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Cuándo alcanzó su pico'),
                        
                        Forms\Components\DateTimePicker::make('trending_end')
                            ->label('Fin del Trending')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Cuándo dejó de ser trending'),
                        
                        Forms\Components\TextInput::make('trending_duration_hours')
                            ->numeric()
                            ->label('Duración en Horas')
                            ->placeholder('Duración total en horas...'),
                        
                        Forms\Components\Select::make('trending_status')
                            ->options([
                                'rising' => '📈 En Aumento',
                                'peak' => '🔥 En Pico',
                                'declining' => '📉 En Declive',
                                'stable' => '➡️ Estable',
                                'ended' => '⏹️ Terminado',
                                'resurging' => '🔄 Resurgiendo',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('rising')
                            ->label('Estado del Trending'),
                        
                        Forms\Components\TextInput::make('peak_position')
                            ->numeric()
                            ->label('Posición Pico')
                            ->placeholder('Posición máxima alcanzada...'),
                        
                        Forms\Components\TextInput::make('current_position')
                            ->numeric()
                            ->label('Posición Actual')
                            ->placeholder('Posición actual...'),
                        
                        Forms\Components\Toggle::make('is_still_trending')
                            ->label('Sigue Trending')
                            ->default(true)
                            ->helperText('El tema sigue siendo trending'),
                        
                        Forms\Components\Toggle::make('has_peaked')
                            ->label('Ya Alcanzó su Pico')
                            ->default(false)
                            ->helperText('El tema ya alcanzó su pico'),
                    ])->columns(2),

                Forms\Components\Section::make('Plataformas y Fuentes')
                    ->schema([
                        Forms\Components\Toggle::make('trending_on_twitter')
                            ->label('Trending en Twitter')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_facebook')
                            ->label('Trending en Facebook')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_instagram')
                            ->label('Trending en Instagram')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_tiktok')
                            ->label('Trending en TikTok')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_youtube')
                            ->label('Trending en YouTube')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_google')
                            ->label('Trending en Google')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_reddit')
                            ->label('Trending en Reddit')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_linkedin')
                            ->label('Trending en LinkedIn')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_pinterest')
                            ->label('Trending en Pinterest')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_snapchat')
                            ->label('Trending en Snapchat')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_twitch')
                            ->label('Trending en Twitch')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('trending_on_discord')
                            ->label('Trending en Discord')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('primary_platform')
                            ->maxLength(100)
                            ->label('Plataforma Principal')
                            ->placeholder('Plataforma donde más trending...'),
                        
                        Forms\Components\TextInput::make('secondary_platforms')
                            ->maxLength(500)
                            ->label('Plataformas Secundarias')
                            ->placeholder('Otras plataformas donde es trending...'),
                    ])->columns(2),

                Forms\Components\Section::make('Contexto y Relaciones')
                    ->schema([
                        Forms\Components\Textarea::make('context')
                            ->maxLength(1000)
                            ->label('Contexto')
                            ->rows(3)
                            ->placeholder('Contexto del tema trending...'),
                        
                        Forms\Components\Textarea::make('related_topics')
                            ->maxLength(500)
                            ->label('Temas Relacionados')
                            ->rows(2)
                            ->placeholder('Temas relacionados...'),
                        
                        Forms\Components\Textarea::make('key_players')
                            ->maxLength(500)
                            ->label('Actores Clave')
                            ->rows(2)
                            ->placeholder('Personas u organizaciones clave...'),
                        
                        Forms\Components\Textarea::make('geographic_relevance')
                            ->maxLength(500)
                            ->label('Relevancia Geográfica')
                            ->rows(2)
                            ->placeholder('Regiones o países relevantes...'),
                        
                        Forms\Components\Textarea::make('demographic_focus')
                            ->maxLength(500)
                            ->label('Enfoque Demográfico')
                            ->rows(2)
                            ->placeholder('Grupos demográficos objetivo...'),
                        
                        Forms\Components\Textarea::make('industry_impact')
                            ->maxLength(500)
                            ->label('Impacto en la Industria')
                            ->rows(2)
                            ->placeholder('Impacto en industrias específicas...'),
                        
                        Forms\Components\Textarea::make('market_implications')
                            ->maxLength(500)
                            ->label('Implicaciones de Mercado')
                            ->rows(2)
                            ->placeholder('Implicaciones para el mercado...'),
                        
                        Forms\Components\Textarea::make('social_impact')
                            ->maxLength(500)
                            ->label('Impacto Social')
                            ->rows(2)
                            ->placeholder('Impacto en la sociedad...'),
                        
                        Forms\Components\Textarea::make('political_implications')
                            ->maxLength(500)
                            ->label('Implicaciones Políticas')
                            ->rows(2)
                            ->placeholder('Implicaciones políticas...'),
                        
                        Forms\Components\Textarea::make('economic_implications')
                            ->maxLength(500)
                            ->label('Implicaciones Económicas')
                            ->rows(2)
                            ->placeholder('Implicaciones económicas...'),
                    ])->columns(1),

                Forms\Components\Section::make('Análisis y Predicciones')
                    ->schema([
                        Forms\Components\Textarea::make('trend_analysis')
                            ->maxLength(1000)
                            ->label('Análisis de la Tendencia')
                            ->rows(3)
                            ->placeholder('Análisis de por qué es trending...'),
                        
                        Forms\Components\Textarea::make('growth_factors')
                            ->maxLength(500)
                            ->label('Factores de Crecimiento')
                            ->rows(2)
                            ->placeholder('Factores que contribuyen al crecimiento...'),
                        
                        Forms\Components\Textarea::make('decline_factors')
                            ->maxLength(500)
                            ->label('Factores de Declive')
                            ->rows(2)
                            ->placeholder('Factores que pueden causar declive...'),
                        
                        Forms\Components\Textarea::make('future_outlook')
                            ->maxLength(500)
                            ->label('Perspectiva Futura')
                            ->rows(2)
                            ->placeholder('Perspectiva futura del tema...'),
                        
                        Forms\Components\Textarea::make('predictions')
                            ->maxLength(500)
                            ->label('Predicciones')
                            ->rows(2)
                            ->placeholder('Predicciones sobre el tema...'),
                        
                        Forms\Components\Textarea::make('recommendations')
                            ->maxLength(500)
                            ->label('Recomendaciones')
                            ->rows(2)
                            ->placeholder('Recomendaciones relacionadas...'),
                        
                        Forms\Components\Textarea::make('risk_assessment')
                            ->maxLength(500)
                            ->label('Evaluación de Riesgos')
                            ->rows(2)
                            ->placeholder('Evaluación de riesgos...'),
                        
                        Forms\Components\Textarea::make('opportunity_analysis')
                            ->maxLength(500)
                            ->label('Análisis de Oportunidades')
                            ->rows(2)
                            ->placeholder('Análisis de oportunidades...'),
                    ])->columns(1),

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
                            ->helperText('El tema es una traducción'),
                        
                        Forms\Components\TextInput::make('original_language')
                            ->maxLength(10)
                            ->label('Idioma Original')
                            ->placeholder('Idioma original si es traducción...'),
                        
                        Forms\Components\TextInput::make('canonical_url')
                            ->maxLength(500)
                            ->label('URL Canónica')
                            ->placeholder('URL canónica...'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Moderación')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '✅ Activo',
                                'inactive' => '❌ Inactivo',
                                'pending' => '⏳ Pendiente',
                                'review' => '👀 En Revisión',
                                'archived' => '📦 Archivado',
                                'flagged' => '🚩 Marcado',
                                'blocked' => '🚫 Bloqueado',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('active')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Tema importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado')
                            ->default(false)
                            ->helperText('El tema ha sido verificado'),
                        
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Aprobado')
                            ->default(false)
                            ->helperText('El tema ha sido aprobado'),
                        
                        Forms\Components\Toggle::make('is_flagged')
                            ->label('Marcado')
                            ->default(false)
                            ->helperText('El tema ha sido marcado'),
                        
                        Forms\Components\TextInput::make('flag_reason')
                            ->maxLength(255)
                            ->label('Razón de la Marca')
                            ->placeholder('Razón por la que fue marcado...')
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
                        
                        Forms\Components\TextInput::make('view_count')
                            ->numeric()
                            ->label('Contador de Vistas')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de veces visto'),
                        
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
                
                Tables\Columns\TextColumn::make('topic_title')
                    ->label('Tema')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('topic_keyword')
                    ->label('Palabra Clave')
                    ->searchable()
                    ->limit(25)
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('topic_hashtag')
                    ->label('Hashtag')
                    ->searchable()
                    ->limit(20)
                    ->color('primary'),
                
                Tables\Columns\BadgeColumn::make('topic_category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'politics',
                        'success' => 'technology',
                        'warning' => 'entertainment',
                        'info' => 'sports',
                        'danger' => 'business',
                        'secondary' => 'health',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'politics' => '🏛️ Política',
                        'technology' => '💻 Tecnología',
                        'entertainment' => '🎬 Entretenimiento',
                        'sports' => '⚽ Deportes',
                        'business' => '💼 Negocios',
                        'health' => '🏥 Salud',
                        'science' => '🔬 Ciencia',
                        'environment' => '🌍 Medio Ambiente',
                        'education' => '🎓 Educación',
                        'culture' => '🎨 Cultura',
                        'social_issues' => '🤝 Temas Sociales',
                        'crime' => '🚨 Crimen',
                        'weather' => '🌤️ Clima',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'fashion' => '👗 Moda',
                        'automotive' => '🚗 Automóviles',
                        'finance' => '💰 Finanzas',
                        'real_estate' => '🏠 Bienes Raíces',
                        'energy' => '⚡ Energía',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('topic_type')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'news',
                        'info' => 'event',
                        'warning' => 'person',
                        'primary' => 'place',
                        'danger' => 'product',
                        'secondary' => 'service',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'news' => '📰 Noticia',
                        'event' => '📅 Evento',
                        'person' => '👤 Persona',
                        'place' => '📍 Lugar',
                        'product' => '🛍️ Producto',
                        'service' => '🔧 Servicio',
                        'organization' => '🏢 Organización',
                        'movement' => '🔄 Movimiento',
                        'trend' => '📈 Tendencia',
                        'controversy' => '⚖️ Controversia',
                        'achievement' => '🏆 Logro',
                        'discovery' => '🔍 Descubrimiento',
                        'announcement' => '📢 Anuncio',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('sentiment')
                    ->label('Sentimiento')
                    ->colors([
                        'success' => 'positive',
                        'danger' => 'negative',
                        'secondary' => 'neutral',
                        'warning' => 'mixed',
                        'primary' => 'controversial',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'positive' => '😊 Positivo',
                        'negative' => '😞 Negativo',
                        'neutral' => '😐 Neutral',
                        'mixed' => '🤔 Mixto',
                        'controversial' => '⚖️ Controvertido',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('urgency_level')
                    ->label('Urgencia')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                        'primary' => 'critical',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => '🟢 Baja',
                        'medium' => '🟡 Media',
                        'high' => '🟠 Alta',
                        'critical' => '🔴 Crítica',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('trending_score')
                    ->label('Puntuación')
                    ->numeric()
                    ->sortable()
                    ->suffix('/100')
                    ->color(fn (float $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'info',
                        $state >= 40 => 'warning',
                        $state >= 20 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('search_volume')
                    ->label('Búsquedas')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 10000 => 'success',
                        $state >= 5000 => 'info',
                        $state >= 1000 => 'warning',
                        $state >= 100 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('social_mentions')
                    ->label('Menciones Sociales')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 10000 => 'success',
                        $state >= 5000 => 'info',
                        $state >= 1000 => 'warning',
                        $state >= 100 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\BadgeColumn::make('trending_status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'rising',
                        'danger' => 'peak',
                        'warning' => 'declining',
                        'info' => 'stable',
                        'secondary' => 'ended',
                        'primary' => 'resurging',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'rising' => '📈 En Aumento',
                        'peak' => '🔥 En Pico',
                        'declining' => '📉 En Declive',
                        'stable' => '➡️ Estable',
                        'ended' => '⏹️ Terminado',
                        'resurging' => '🔄 Resurgiendo',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('trending_start')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('trending_peak')
                    ->label('Pico')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('trending_duration_hours')
                    ->label('Duración (h)')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 72 => 'success',
                        $state >= 48 => 'info',
                        $state >= 24 => 'warning',
                        $state >= 12 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\IconColumn::make('is_still_trending')
                    ->label('Sigue Trending')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('trending_on_twitter')
                    ->label('Twitter')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('trending_on_google')
                    ->label('Google')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),
                
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
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'pending' => '⏳ Pendiente',
                        'review' => '👀 En Revisión',
                        'archived' => '📦 Archivado',
                        'flagged' => '🚩 Marcado',
                        'blocked' => '🚫 Bloqueado',
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
                Tables\Filters\SelectFilter::make('topic_category')
                    ->options([
                        'politics' => '🏛️ Política',
                        'technology' => '💻 Tecnología',
                        'entertainment' => '🎬 Entretenimiento',
                        'sports' => '⚽ Deportes',
                        'business' => '💼 Negocios',
                        'health' => '🏥 Salud',
                        'science' => '🔬 Ciencia',
                        'environment' => '🌍 Medio Ambiente',
                        'education' => '🎓 Educación',
                        'culture' => '🎨 Cultura',
                        'social_issues' => '🤝 Temas Sociales',
                        'crime' => '🚨 Crimen',
                        'weather' => '🌤️ Clima',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'fashion' => '👗 Moda',
                        'automotive' => '🚗 Automóviles',
                        'finance' => '💰 Finanzas',
                        'real_estate' => '🏠 Bienes Raíces',
                        'energy' => '⚡ Energía',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría del Tema'),
                
                Tables\Filters\SelectFilter::make('topic_type')
                    ->options([
                        'news' => '📰 Noticia',
                        'event' => '📅 Evento',
                        'person' => '👤 Persona',
                        'place' => '📍 Lugar',
                        'product' => '🛍️ Producto',
                        'service' => '🔧 Servicio',
                        'organization' => '🏢 Organización',
                        'movement' => '🔄 Movimiento',
                        'trend' => '📈 Tendencia',
                        'controversy' => '⚖️ Controversia',
                        'achievement' => '🏆 Logro',
                        'discovery' => '🔍 Descubrimiento',
                        'announcement' => '📢 Anuncio',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Tema'),
                
                Tables\Filters\SelectFilter::make('sentiment')
                    ->options([
                        'positive' => '😊 Positivo',
                        'negative' => '😞 Negativo',
                        'neutral' => '😐 Neutral',
                        'mixed' => '🤔 Mixto',
                        'controversial' => '⚖️ Controvertido',
                        'other' => '❓ Otro',
                    ])
                    ->label('Sentimiento'),
                
                Tables\Filters\SelectFilter::make('urgency_level')
                    ->options([
                        'low' => '🟢 Baja',
                        'medium' => '🟡 Media',
                        'high' => '🟠 Alta',
                        'critical' => '🔴 Crítica',
                        'other' => '❓ Otro',
                    ])
                    ->label('Nivel de Urgencia'),
                
                Tables\Filters\SelectFilter::make('trending_status')
                    ->options([
                        'rising' => '📈 En Aumento',
                        'peak' => '🔥 En Pico',
                        'declining' => '📉 En Declive',
                        'stable' => '➡️ Estable',
                        'ended' => '⏹️ Terminado',
                        'resurging' => '🔄 Resurgiendo',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado del Trending'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'pending' => '⏳ Pendiente',
                        'review' => '👀 En Revisión',
                        'archived' => '📦 Archivado',
                        'flagged' => '🚩 Marcado',
                        'blocked' => '🚫 Bloqueado',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificados')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('still_trending')
                    ->label('Solo Trending')
                    ->query(fn (Builder $query): Builder => $query->where('is_still_trending', true)),
                
                Tables\Filters\Filter::make('high_score')
                    ->label('Alta Puntuación (80+)')
                    ->query(fn (Builder $query): Builder => $query->where('trending_score', '>=', 80)),
                
                Tables\Filters\Filter::make('high_volume')
                    ->label('Alto Volumen (5000+)')
                    ->query(fn (Builder $query): Builder => $query->where('search_volume', '>=', 5000)),
                
                Tables\Filters\Filter::make('long_duration')
                    ->label('Larga Duración (48h+)')
                    ->query(fn (Builder $query): Builder => $query->where('trending_duration_hours', '>=', 48)),
                
                Tables\Filters\Filter::make('twitter_trending')
                    ->label('Trending en Twitter')
                    ->query(fn (Builder $query): Builder => $query->where('trending_on_twitter', true)),
                
                Tables\Filters\Filter::make('google_trending')
                    ->label('Trending en Google')
                    ->query(fn (Builder $query): Builder => $query->where('trending_on_google', true)),
                
                Tables\Filters\Filter::make('positive_sentiment')
                    ->label('Sentimiento Positivo')
                    ->query(fn (Builder $query): Builder => $query->where('sentiment', 'positive')),
                
                Tables\Filters\Filter::make('controversial_topics')
                    ->label('Temas Controvertidos')
                    ->query(fn (Builder $query): Builder => $query->where('sentiment', 'controversial')),
                
                Tables\Filters\Filter::make('recent_trends')
                    ->label('Tendencias Recientes (24h)')
                    ->query(fn (Builder $query): Builder => $query->where('trending_start', '>=', now()->subDay())),
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
                
                Tables\Actions\Action::make('approve_topic')
                    ->label('Aprobar')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['is_approved' => true, 'status' => 'active']);
                    })
                    ->visible(fn ($record): bool => !$record->is_approved)
                    ->color('success'),
                
                Tables\Actions\Action::make('flag_topic')
                    ->label('Marcar')
                    ->icon('fas-flag')
                    ->action(function ($record): void {
                        $record->update(['is_flagged' => true, 'status' => 'review']);
                    })
                    ->visible(fn ($record): bool => !$record->is_flagged)
                    ->color('warning'),
                
                Tables\Actions\Action::make('activate_topic')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_topic')
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
            ->defaultSort('trending_start', 'desc')
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
            'index' => Pages\ListTrendingTopics::route('/'),
            'create' => Pages\CreateTrendingTopic::route('/create'),
            'view' => Pages\ViewTrendingTopic::route('/{record}'),
            'edit' => Pages\EditTrendingTopic::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
