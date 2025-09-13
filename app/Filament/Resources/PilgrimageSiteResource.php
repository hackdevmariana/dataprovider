<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PilgrimageSiteResource\Pages;
use App\Filament\Resources\PilgrimageSiteResource\RelationManagers;
use App\Models\PilgrimageSite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PilgrimageSiteResource extends Resource
{
    protected static ?string $model = PilgrimageSite::class;

    protected static ?string $navigationIcon = 'fas-mosque';

    protected static ?string $navigationGroup = 'Historia y Cultura';

    protected static ?string $navigationLabel = 'Sitios de Peregrinación';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Sitio de Peregrinación';

    protected static ?string $pluralModelLabel = 'Sitios de Peregrinación';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Sitio')
                            ->placeholder('Nombre oficial del sitio de peregrinación...'),
                        
                        Forms\Components\TextInput::make('site_code')
                            ->maxLength(100)
                            ->label('Código del Sitio')
                            ->placeholder('Código único del sitio...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada del sitio...'),
                        
                        Forms\Components\Select::make('site_type')
                            ->options([
                                'religious' => '⛪ Religioso',
                                'spiritual' => '🧘 Espiritual',
                                'historical' => '🏛️ Histórico',
                                'cultural' => '🎭 Cultural',
                                'natural' => '🌿 Natural',
                                'archaeological' => '🏺 Arqueológico',
                                'pilgrimage' => '🦅 Peregrinación',
                                'shrine' => '🕯️ Santuario',
                                'temple' => '🕍 Templo',
                                'monastery' => '🏰 Monasterio',
                                'cathedral' => '⛪ Catedral',
                                'basilica' => '🏛️ Basílica',
                                'chapel' => '⛪ Capilla',
                                'mosque' => '🕌 Mezquita',
                                'synagogue' => '🕍 Sinagoga',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Sitio'),
                        
                        Forms\Components\Select::make('religion')
                            ->options([
                                'christianity' => '✝️ Cristianismo',
                                'islam' => '☪️ Islam',
                                'judaism' => '✡️ Judaísmo',
                                'buddhism' => '☸️ Budismo',
                                'hinduism' => '🕉️ Hinduismo',
                                'sikhism' => '☬ Sikhismo',
                                'taoism' => '☯️ Taoísmo',
                                'shinto' => '⛩️ Sintoísmo',
                                'zoroastrianism' => '🔥 Zoroastrismo',
                                'jainism' => '🕉️ Jainismo',
                                'bahaism' => '⭐ Fe Bahá\'í',
                                'pagan' => '🌙 Pagano',
                                'indigenous' => '🌍 Indígena',
                                'new_age' => '✨ Nueva Era',
                                'secular' => '🌐 Secular',
                                'other' => '❓ Otro',
                            ])
                            ->label('Religión Principal'),
                        
                        Forms\Components\Select::make('denomination')
                            ->options([
                                'catholic' => '⛪ Católica',
                                'orthodox' => '☦️ Ortodoxa',
                                'protestant' => '✝️ Protestante',
                                'anglican' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿 Anglicana',
                                'lutheran' => '✝️ Luterana',
                                'methodist' => '✝️ Metodista',
                                'baptist' => '✝️ Bautista',
                                'presbyterian' => '✝️ Presbiteriana',
                                'pentecostal' => '✝️ Pentecostal',
                                'evangelical' => '✝️ Evangélica',
                                'sunni' => '☪️ Suní',
                                'shiite' => '☪️ Chiíta',
                                'sufi' => '☪️ Sufí',
                                'theravada' => '☸️ Theravada',
                                'mahayana' => '☸️ Mahayana',
                                'vajrayana' => '☸️ Vajrayana',
                                'vaishnavism' => '🕉️ Vaishnavismo',
                                'shaivism' => '🕉️ Shaivismo',
                                'shaktism' => '🕉️ Shaktismo',
                                'other' => '❓ Otra',
                            ])
                            ->label('Denominación'),
                    ])->columns(2),

                Forms\Components\Section::make('Ubicación y Acceso')
                    ->schema([
                        Forms\Components\TextInput::make('country')
                            ->required()
                            ->maxLength(100)
                            ->label('País')
                            ->placeholder('País donde se encuentra el sitio...'),
                        
                        Forms\Components\TextInput::make('region')
                            ->required()
                            ->maxLength(100)
                            ->label('Región/Estado')
                            ->placeholder('Región, provincia o estado...'),
                        
                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(100)
                            ->label('Ciudad')
                            ->placeholder('Ciudad o pueblo más cercano...'),
                        
                        Forms\Components\TextInput::make('address')
                            ->maxLength(500)
                            ->label('Dirección')
                            ->placeholder('Dirección completa del sitio...'),
                        
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20)
                            ->label('Código Postal'),
                        
                        Forms\Components\TextInput::make('coordinates')
                            ->maxLength(100)
                            ->label('Coordenadas GPS')
                            ->placeholder('Latitud, Longitud...'),
                        
                        Forms\Components\TextInput::make('elevation')
                            ->numeric()
                            ->suffix('m')
                            ->label('Elevación')
                            ->helperText('Elevación sobre el nivel del mar'),
                        
                        Forms\Components\Select::make('accessibility')
                            ->options([
                                'easy' => '🟢 Fácil Acceso',
                                'moderate' => '🟡 Acceso Moderado',
                                'difficult' => '🟠 Acceso Difícil',
                                'challenging' => '🔴 Acceso Desafiante',
                                'restricted' => '⚫ Acceso Restringido',
                                'seasonal' => '🔄 Acceso Estacional',
                            ])
                            ->label('Nivel de Accesibilidad'),
                    ])->columns(2),

                Forms\Components\Section::make('Historia y Significado')
                    ->schema([
                        Forms\Components\TextInput::make('founding_date')
                            ->maxLength(100)
                            ->label('Fecha de Fundación')
                            ->placeholder('Año o período de fundación...'),
                        
                        Forms\Components\TextInput::make('historical_period')
                            ->maxLength(100)
                            ->label('Período Histórico')
                            ->placeholder('Antigüedad, Edad Media, Renacimiento...'),
                        
                        Forms\Components\Textarea::make('historical_significance')
                            ->maxLength(1000)
                            ->label('Significado Histórico')
                            ->rows(3)
                            ->placeholder('Importancia histórica del sitio...'),
                        
                        Forms\Components\Textarea::make('religious_significance')
                            ->maxLength(1000)
                            ->label('Significado Religioso')
                            ->rows(3)
                            ->placeholder('Importancia religiosa y espiritual...'),
                        
                        Forms\Components\Textarea::make('miracles_legends')
                            ->maxLength(1000)
                            ->label('Milagros y Leyendas')
                            ->rows(3)
                            ->placeholder('Milagros, apariciones o leyendas asociadas...'),
                        
                        Forms\Components\KeyValue::make('historical_events')
                            ->label('Eventos Históricos')
                            ->keyLabel('Fecha')
                            ->valueLabel('Evento')
                            ->addActionLabel('Agregar Evento'),
                    ])->columns(1),

                Forms\Components\Section::make('Arquitectura y Construcción')
                    ->schema([
                        Forms\Components\Select::make('architectural_style')
                            ->options([
                                'romanesque' => '🏛️ Románico',
                                'gothic' => '⛪ Gótico',
                                'renaissance' => '🎨 Renacentista',
                                'baroque' => '🎭 Barroco',
                                'neoclassical' => '🏛️ Neoclásico',
                                'romantic' => '💕 Romántico',
                                'modern' => '🏢 Moderno',
                                'contemporary' => '🌆 Contemporáneo',
                                'byzantine' => '⛪ Bizantino',
                                'islamic' => '🕌 Islámico',
                                'hindu' => '🕉️ Hindú',
                                'buddhist' => '☸️ Budista',
                                'traditional' => '🏘️ Tradicional',
                                'vernacular' => '🏠 Vernáculo',
                                'other' => '❓ Otro',
                            ])
                            ->label('Estilo Arquitectónico'),
                        
                        Forms\Components\TextInput::make('construction_materials')
                            ->maxLength(255)
                            ->label('Materiales de Construcción')
                            ->placeholder('Piedra, madera, ladrillo, mármol...'),
                        
                        Forms\Components\TextInput::make('construction_period')
                            ->maxLength(100)
                            ->label('Período de Construcción')
                            ->placeholder('Años o siglos de construcción...'),
                        
                        Forms\Components\TextInput::make('architect')
                            ->maxLength(255)
                            ->label('Arquitecto')
                            ->placeholder('Nombre del arquitecto principal...'),
                        
                        Forms\Components\TextInput::make('dimensions')
                            ->maxLength(100)
                            ->label('Dimensiones')
                            ->placeholder('Altura, ancho, profundidad...'),
                        
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->label('Capacidad')
                            ->helperText('Número máximo de visitantes'),
                        
                        Forms\Components\Toggle::make('is_unesco_heritage')
                            ->label('Patrimonio UNESCO')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('unesco_year')
                            ->maxLength(4)
                            ->label('Año UNESCO')
                            ->placeholder('Año de declaración...'),
                    ])->columns(2),

                Forms\Components\Section::make('Actividades y Servicios')
                    ->schema([
                        Forms\Components\KeyValue::make('religious_services')
                            ->label('Servicios Religiosos')
                            ->keyLabel('Servicio')
                            ->valueLabel('Horario/Frecuencia')
                            ->addActionLabel('Agregar Servicio'),
                        
                        Forms\Components\KeyValue::make('pilgrimage_routes')
                            ->label('Rutas de Peregrinación')
                            ->keyLabel('Ruta')
                            ->valueLabel('Distancia/Duración')
                            ->addActionLabel('Agregar Ruta'),
                        
                        Forms\Components\Textarea::make('special_events')
                            ->maxLength(500)
                            ->label('Eventos Especiales')
                            ->rows(2)
                            ->placeholder('Festivales, celebraciones, peregrinaciones...'),
                        
                        Forms\Components\TextInput::make('pilgrimage_season')
                            ->maxLength(100)
                            ->label('Temporada de Peregrinación')
                            ->placeholder('Meses o estaciones principales...'),
                        
                        Forms\Components\Toggle::make('has_accommodation')
                            ->label('Alojamiento Disponible')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_restaurant')
                            ->label('Restaurante/Cafetería')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_gift_shop')
                            ->label('Tienda de Regalos')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_guided_tours')
                            ->label('Visitas Guiadas')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_museum')
                            ->label('Museo')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_library')
                            ->label('Biblioteca')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Información de Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50)
                            ->label('Teléfono'),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email'),
                        
                        Forms\Components\TextInput::make('website')
                            ->maxLength(500)
                            ->label('Sitio Web')
                            ->url()
                            ->placeholder('https://www.ejemplo.com'),
                        
                        Forms\Components\TextInput::make('social_media')
                            ->maxLength(500)
                            ->label('Redes Sociales')
                            ->placeholder('Facebook, Instagram, Twitter...'),
                        
                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(255)
                            ->label('Persona de Contacto')
                            ->placeholder('Nombre del responsable...'),
                        
                        Forms\Components\TextInput::make('emergency_contact')
                            ->maxLength(100)
                            ->label('Contacto de Emergencia')
                            ->placeholder('Número de emergencia...'),
                    ])->columns(2),

                Forms\Components\Section::make('Horarios y Visitas')
                    ->schema([
                        Forms\Components\TextInput::make('opening_hours')
                            ->maxLength(255)
                            ->label('Horario de Apertura')
                            ->placeholder('L-V 9:00-18:00, S-D 10:00-17:00...'),
                        
                        Forms\Components\Toggle::make('is_open_24_7')
                            ->label('Abierto 24/7')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('best_visit_time')
                            ->maxLength(100)
                            ->label('Mejor Momento para Visitar')
                            ->placeholder('Mañana, tarde, temporada específica...'),
                        
                        Forms\Components\TextInput::make('visit_duration')
                            ->maxLength(100)
                            ->label('Duración de la Visita')
                            ->placeholder('1 hora, medio día, día completo...'),
                        
                        Forms\Components\Toggle::make('requires_reservation')
                            ->label('Requiere Reserva')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_free_entry')
                            ->label('Entrada Gratuita')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('entrance_fee')
                            ->numeric()
                            ->prefix('€')
                            ->label('Precio de Entrada')
                            ->visible(fn (callable $get): bool => !$get('is_free_entry')),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Mantenimiento')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'open' => '🟢 Abierto',
                                'closed' => '🔴 Cerrado',
                                'maintenance' => '🔧 En Mantenimiento',
                                'restoration' => '🏗️ En Restauración',
                                'renovation' => '🔨 En Renovación',
                                'seasonal' => '🔄 Cerrado Estacionalmente',
                                'temporary' => '⏳ Cerrado Temporalmente',
                                'permanent' => '🚫 Cerrado Permanentemente',
                            ])
                            ->required()
                            ->default('open')
                            ->label('Estado del Sitio'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Sitio importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular')
                            ->default(false)
                            ->helperText('Sitio muy visitado'),
                        
                        Forms\Components\Toggle::make('is_accessible_disabled')
                            ->label('Accesible para Discapacitados')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('maintenance_schedule')
                            ->maxLength(255)
                            ->label('Horario de Mantenimiento')
                            ->placeholder('Días y horarios de mantenimiento...'),
                        
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
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Sitio')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('site_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'religious',
                        'success' => 'spiritual',
                        'warning' => 'historical',
                        'info' => 'cultural',
                        'danger' => 'natural',
                        'secondary' => 'archaeological',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'religious' => '⛪ Religioso',
                        'spiritual' => '🧘 Espiritual',
                        'historical' => '🏛️ Histórico',
                        'cultural' => '🎭 Cultural',
                        'natural' => '🌿 Natural',
                        'archaeological' => '🏺 Arqueológico',
                        'pilgrimage' => '🦅 Peregrinación',
                        'shrine' => '🕯️ Santuario',
                        'temple' => '🕍 Templo',
                        'monastery' => '🏰 Monasterio',
                        'cathedral' => '⛪ Catedral',
                        'basilica' => '🏛️ Basílica',
                        'chapel' => '⛪ Capilla',
                        'mosque' => '🕌 Mezquita',
                        'synagogue' => '🕍 Sinagoga',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('religion')
                    ->label('Religión')
                    ->colors([
                        'primary' => 'christianity',
                        'success' => 'islam',
                        'warning' => 'judaism',
                        'info' => 'buddhism',
                        'danger' => 'hinduism',
                        'secondary' => 'sikhism',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'christianity' => '✝️ Cristianismo',
                        'islam' => '☪️ Islam',
                        'judaism' => '✡️ Judaísmo',
                        'buddhism' => '☸️ Budismo',
                        'hinduism' => '🕉️ Hinduismo',
                        'sikhism' => '☬ Sikhismo',
                        'taoism' => '☯️ Taoísmo',
                        'shinto' => '⛩️ Sintoísmo',
                        'zoroastrianism' => '🔥 Zoroastrismo',
                        'jainism' => '🕉️ Jainismo',
                        'bahaism' => '⭐ Fe Bahá\'í',
                        'pagan' => '🌙 Pagano',
                        'indigenous' => '🌍 Indígena',
                        'new_age' => '✨ Nueva Era',
                        'secular' => '🌐 Secular',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\BadgeColumn::make('accessibility')
                    ->label('Accesibilidad')
                    ->colors([
                        'success' => 'easy',
                        'warning' => 'moderate',
                        'danger' => 'difficult',
                        'secondary' => 'challenging',
                        'dark' => 'restricted',
                        'info' => 'seasonal',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'easy' => '🟢 Fácil',
                        'moderate' => '🟡 Moderado',
                        'difficult' => '🟠 Difícil',
                        'challenging' => '🔴 Desafiante',
                        'restricted' => '⚫ Restringido',
                        'seasonal' => '🔄 Estacional',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_unesco_heritage')
                    ->label('UNESCO')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('has_accommodation')
                    ->label('Alojamiento')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('has_guided_tours')
                    ->label('Visitas Guiadas')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_free_entry')
                    ->label('Entrada Gratuita')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'open',
                        'danger' => 'closed',
                        'warning' => 'maintenance',
                        'info' => 'restoration',
                        'secondary' => 'renovation',
                        'dark' => 'seasonal',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => '🟢 Abierto',
                        'closed' => '🔴 Cerrado',
                        'maintenance' => '🔧 Mantenimiento',
                        'restoration' => '🏗️ Restauración',
                        'renovation' => '🔨 Renovación',
                        'seasonal' => '🔄 Estacional',
                        'temporary' => '⏳ Temporal',
                        'permanent' => '🚫 Permanente',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('site_type')
                    ->options([
                        'religious' => '⛪ Religioso',
                        'spiritual' => '🧘 Espiritual',
                        'historical' => '🏛️ Histórico',
                        'cultural' => '🎭 Cultural',
                        'natural' => '🌿 Natural',
                        'archaeological' => '🏺 Arqueológico',
                        'pilgrimage' => '🦅 Peregrinación',
                        'shrine' => '🕯️ Santuario',
                        'temple' => '🕍 Templo',
                        'monastery' => '🏰 Monasterio',
                        'cathedral' => '⛪ Catedral',
                        'basilica' => '🏛️ Basílica',
                        'chapel' => '⛪ Capilla',
                        'mosque' => '🕌 Mezquita',
                        'synagogue' => '🕍 Sinagoga',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Sitio'),
                
                Tables\Filters\SelectFilter::make('religion')
                    ->options([
                        'christianity' => '✝️ Cristianismo',
                        'islam' => '☪️ Islam',
                        'judaism' => '✡️ Judaísmo',
                        'buddhism' => '☸️ Budismo',
                        'hinduism' => '🕉️ Hinduismo',
                        'sikhism' => '☬ Sikhismo',
                        'taoism' => '☯️ Taoísmo',
                        'shinto' => '⛩️ Sintoísmo',
                        'zoroastrianism' => '🔥 Zoroastrismo',
                        'jainism' => '🕉️ Jainismo',
                        'bahaism' => '⭐ Fe Bahá\'í',
                        'pagan' => '🌙 Pagano',
                        'indigenous' => '🌍 Indígena',
                        'new_age' => '✨ Nueva Era',
                        'secular' => '🌐 Secular',
                        'other' => '❓ Otro',
                    ])
                    ->label('Religión'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => '🟢 Abierto',
                        'closed' => '🔴 Cerrado',
                        'maintenance' => '🔧 En Mantenimiento',
                        'restoration' => '🏗️ En Restauración',
                        'renovation' => '🔨 En Renovación',
                        'seasonal' => '🔄 Cerrado Estacionalmente',
                        'temporary' => '⏳ Cerrado Temporalmente',
                        'permanent' => '🚫 Cerrado Permanentemente',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\SelectFilter::make('accessibility')
                    ->options([
                        'easy' => '🟢 Fácil Acceso',
                        'moderate' => '🟡 Acceso Moderado',
                        'difficult' => '🟠 Acceso Difícil',
                        'challenging' => '🔴 Acceso Desafiante',
                        'restricted' => '⚫ Acceso Restringido',
                        'seasonal' => '🔄 Acceso Estacional',
                    ])
                    ->label('Accesibilidad'),
                
                Tables\Filters\Filter::make('open_only')
                    ->label('Solo Abiertos')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'open')),
                
                Tables\Filters\Filter::make('unesco_heritage')
                    ->label('Patrimonio UNESCO')
                    ->query(fn (Builder $query): Builder => $query->where('is_unesco_heritage', true)),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('popular_only')
                    ->label('Solo Populares')
                    ->query(fn (Builder $query): Builder => $query->where('is_popular', true)),
                
                Tables\Filters\Filter::make('free_entry_only')
                    ->label('Solo Entrada Gratuita')
                    ->query(fn (Builder $query): Builder => $query->where('is_free_entry', true)),
                
                Tables\Filters\Filter::make('has_accommodation')
                    ->label('Con Alojamiento')
                    ->query(fn (Builder $query): Builder => $query->where('has_accommodation', true)),
                
                Tables\Filters\Filter::make('has_guided_tours')
                    ->label('Con Visitas Guiadas')
                    ->query(fn (Builder $query): Builder => $query->where('has_guided_tours', true)),
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
                
                Tables\Actions\Action::make('toggle_popular')
                    ->label(fn ($record): string => $record->is_popular ? 'Quitar Popular' : 'Marcar Popular')
                    ->icon(fn ($record): string => $record->is_popular ? 'fas-fire' : 'far-fire')
                    ->action(function ($record): void {
                        $record->update(['is_popular' => !$record->is_popular]);
                    })
                    ->color(fn ($record): string => $record->is_popular ? 'primary' : 'secondary'),
                
                Tables\Actions\Action::make('visit_website')
                    ->label('Visitar Web')
                    ->icon('fas-external-link-alt')
                    ->url(fn ($record): string => $record->website)
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => !empty($record->website))
                    ->color('primary'),
                
                Tables\Actions\Action::make('contact_site')
                    ->label('Contactar')
                    ->icon('fas-phone')
                    ->url(fn ($record): string => "tel:{$record->phone}")
                    ->visible(fn ($record): bool => !empty($record->phone))
                    ->color('success'),
                
                Tables\Actions\Action::make('get_directions')
                    ->label('Cómo Llegar')
                    ->icon('fas-map-marker-alt')
                    ->url(fn ($record): string => "https://maps.google.com/?q={$record->coordinates}")
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => !empty($record->coordinates))
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
                    
                    Tables\Actions\BulkAction::make('mark_popular')
                        ->label('Marcar como Populares')
                        ->icon('fas-fire')
                        ->action(function ($records): void {
                            $records->each->update(['is_popular' => true]);
                        })
                        ->color('primary'),
                    
                    Tables\Actions\BulkAction::make('mark_open')
                        ->label('Marcar como Abiertos')
                        ->icon('fas-door-open')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'open']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_closed')
                        ->label('Marcar como Cerrados')
                        ->icon('fas-door-closed')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'closed']);
                        })
                        ->color('danger'),
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
            'index' => Pages\ListPilgrimageSites::route('/'),
            'create' => Pages\CreatePilgrimageSite::route('/create'),
            'view' => Pages\ViewPilgrimageSite::route('/{record}'),
            'edit' => Pages\EditPilgrimageSite::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
