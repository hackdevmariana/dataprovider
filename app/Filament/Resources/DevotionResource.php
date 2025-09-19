<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DevotionResource\Pages;
use App\Filament\Resources\DevotionResource\RelationManagers;
use App\Models\Devotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DevotionResource extends Resource
{
    protected static ?string $model = Devotion::class;

    protected static ?string $navigationIcon = 'fas-pray';

    protected static ?string $navigationGroup = 'Religión y Espiritualidad';

    protected static ?string $navigationLabel = 'Devociones Religiosas';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Devoción Religiosa';

    protected static ?string $pluralModelLabel = 'Devociones Religiosas';

    
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
                            ->label('Nombre de la Devoción')
                            ->placeholder('Nombre oficial de la devoción...'),
                        
                        Forms\Components\TextInput::make('devotion_code')
                            ->maxLength(100)
                            ->label('Código de Devoción')
                            ->placeholder('Código único de la devoción...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada de la devoción...'),
                        
                        Forms\Components\Select::make('devotion_type')
                            ->options([
                                'prayer' => '🙏 Oración',
                                'novena' => '🕯️ Novena',
                                'rosary' => '📿 Rosario',
                                'litany' => '📜 Letanía',
                                'hymn' => '🎵 Himno',
                                'meditation' => '🧘 Meditación',
                                'fasting' => '🍽️ Ayuno',
                                'pilgrimage' => '🦅 Peregrinación',
                                'veneration' => '🕯️ Veneración',
                                'consecration' => '💎 Consagración',
                                'dedication' => '🏛️ Dedicación',
                                'celebration' => '🎉 Celebración',
                                'ritual' => '🔮 Ritual',
                                'ceremony' => '🎭 Ceremonia',
                                'tradition' => '📚 Tradición',
                                'custom' => '🏺 Costumbre',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Devoción'),
                        
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
                            ->required()
                            ->label('Religión'),
                        
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

                Forms\Components\Section::make('Origen e Historia')
                    ->schema([
                        Forms\Components\TextInput::make('origin_date')
                            ->maxLength(100)
                            ->label('Fecha de Origen')
                            ->placeholder('Año o período de origen...'),
                        
                        Forms\Components\TextInput::make('origin_location')
                            ->maxLength(255)
                            ->label('Lugar de Origen')
                            ->placeholder('Ciudad, país o región de origen...'),
                        
                        Forms\Components\TextInput::make('founder')
                            ->maxLength(255)
                            ->label('Fundador')
                            ->placeholder('Persona o entidad que fundó la devoción...'),
                        
                        Forms\Components\Textarea::make('historical_background')
                            ->maxLength(1000)
                            ->label('Antecedentes Históricos')
                            ->rows(3)
                            ->placeholder('Contexto histórico de la devoción...'),
                        
                        Forms\Components\Textarea::make('development_history')
                            ->maxLength(1000)
                            ->label('Historia del Desarrollo')
                            ->rows(3)
                            ->placeholder('Cómo evolucionó la devoción a lo largo del tiempo...'),
                        
                        Forms\Components\KeyValue::make('historical_milestones')
                            ->label('Hitos Históricos')
                            ->keyLabel('Fecha')
                            ->valueLabel('Evento')
                            ->addActionLabel('Agregar Hito'),
                    ])->columns(1),

                Forms\Components\Section::make('Práctica y Ritual')
                    ->schema([
                        Forms\Components\Textarea::make('practice_description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción de la Práctica')
                            ->rows(3)
                            ->placeholder('Cómo se practica esta devoción...'),
                        
                        Forms\Components\TextInput::make('practice_frequency')
                            ->maxLength(100)
                            ->label('Frecuencia de Práctica')
                            ->placeholder('Diaria, semanal, mensual, anual...'),
                        
                        Forms\Components\TextInput::make('practice_duration')
                            ->maxLength(100)
                            ->label('Duración de la Práctica')
                            ->placeholder('5 minutos, 1 hora, todo el día...'),
                        
                        Forms\Components\TextInput::make('best_practice_time')
                            ->maxLength(100)
                            ->label('Mejor Momento para Practicar')
                            ->placeholder('Mañana, tarde, noche, momento específico...'),
                        
                        Forms\Components\Textarea::make('ritual_steps')
                            ->maxLength(1000)
                            ->label('Pasos del Ritual')
                            ->rows(3)
                            ->placeholder('Pasos específicos para realizar la devoción...'),
                        
                        Forms\Components\KeyValue::make('required_materials')
                            ->label('Materiales Requeridos')
                            ->keyLabel('Material')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Material'),
                        
                        Forms\Components\KeyValue::make('optional_materials')
                            ->label('Materiales Opcionales')
                            ->keyLabel('Material')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Material'),
                    ])->columns(1),

                Forms\Components\Section::make('Oraciones y Textos')
                    ->schema([
                        Forms\Components\Textarea::make('main_prayer')
                            ->maxLength(2000)
                            ->label('Oración Principal')
                            ->rows(5)
                            ->placeholder('Texto de la oración principal...'),
                        
                        Forms\Components\TextInput::make('prayer_language')
                            ->maxLength(100)
                            ->label('Idioma de la Oración')
                            ->placeholder('Latín, español, inglés, árabe...'),
                        
                        Forms\Components\Textarea::make('prayer_translation')
                            ->maxLength(2000)
                            ->label('Traducción de la Oración')
                            ->rows(5)
                            ->placeholder('Traducción al español...'),
                        
                        Forms\Components\KeyValue::make('additional_prayers')
                            ->label('Oraciones Adicionales')
                            ->keyLabel('Título')
                            ->valueLabel('Texto')
                            ->addActionLabel('Agregar Oración'),
                        
                        Forms\Components\Textarea::make('scriptural_references')
                            ->maxLength(500)
                            ->label('Referencias Bíblicas')
                            ->rows(2)
                            ->placeholder('Pasajes bíblicos relacionados...'),
                        
                        Forms\Components\Textarea::make('theological_basis')
                            ->maxLength(1000)
                            ->label('Base Teológica')
                            ->rows(3)
                            ->placeholder('Fundamento teológico de la devoción...'),
                    ])->columns(1),

                Forms\Components\Section::make('Fechas y Celebraciones')
                    ->schema([
                        Forms\Components\DatePicker::make('feast_day')
                            ->label('Día de Fiesta')
                            ->displayFormat('d/m/Y')
                            ->helperText('Día principal de celebración'),
                        
                        Forms\Components\TextInput::make('celebration_period')
                            ->maxLength(100)
                            ->label('Período de Celebración')
                            ->placeholder('1 día, 9 días, 1 mes...'),
                        
                        Forms\Components\KeyValue::make('special_dates')
                            ->label('Fechas Especiales')
                            ->keyLabel('Fecha')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Fecha'),
                        
                        Forms\Components\TextInput::make('liturgical_season')
                            ->maxLength(100)
                            ->label('Temporada Litúrgica')
                            ->placeholder('Adviento, Cuaresma, Pascua...'),
                        
                        Forms\Components\TextInput::make('calendar_position')
                            ->maxLength(100)
                            ->label('Posición en el Calendario')
                            ->placeholder('Primer domingo, tercer miércoles...'),
                        
                        Forms\Components\Toggle::make('is_movable_feast')
                            ->label('Fiesta Móvil')
                            ->default(false)
                            ->helperText('La fecha cambia cada año'),
                    ])->columns(2),

                Forms\Components\Section::make('Santos y Figuras')
                    ->schema([
                        Forms\Components\TextInput::make('patron_saint')
                            ->maxLength(255)
                            ->label('Santo Patrón')
                            ->placeholder('Santo principal asociado...'),
                        
                        Forms\Components\TextInput::make('associated_saints')
                            ->maxLength(500)
                            ->label('Santos Asociados')
                            ->placeholder('Otros santos relacionados...'),
                        
                        Forms\Components\TextInput::make('blessed_figures')
                            ->maxLength(500)
                            ->label('Figuras Beatificadas')
                            ->placeholder('Beatos o venerables relacionados...'),
                        
                        Forms\Components\TextInput::make('mystics')
                            ->maxLength(500)
                            ->label('Místicos')
                            ->placeholder('Místicos asociados...'),
                        
                        Forms\Components\TextInput::make('visionaries')
                            ->maxLength(500)
                            ->label('Videntes')
                            ->placeholder('Videntes o profetas relacionados...'),
                        
                        Forms\Components\KeyValue::make('holy_figures')
                            ->label('Figuras Santas')
                            ->keyLabel('Nombre')
                            ->valueLabel('Relación')
                            ->addActionLabel('Agregar Figura'),
                    ])->columns(2),

                Forms\Components\Section::make('Lugares y Santuarios')
                    ->schema([
                        Forms\Components\TextInput::make('primary_shrine')
                            ->maxLength(255)
                            ->label('Santuario Principal')
                            ->placeholder('Santuario principal de la devoción...'),
                        
                        Forms\Components\TextInput::make('shrine_location')
                            ->maxLength(255)
                            ->label('Ubicación del Santuario')
                            ->placeholder('Ciudad y país del santuario...'),
                        
                        Forms\Components\KeyValue::make('other_shrines')
                            ->label('Otros Santuarios')
                            ->keyLabel('Santuario')
                            ->valueLabel('Ubicación')
                            ->addActionLabel('Agregar Santuario'),
                        
                        Forms\Components\TextInput::make('pilgrimage_destinations')
                            ->maxLength(500)
                            ->label('Destinos de Peregrinación')
                            ->placeholder('Lugares de peregrinación relacionados...'),
                        
                        Forms\Components\Toggle::make('has_miraculous_image')
                            ->label('Tiene Imagen Milagrosa')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('miraculous_image_location')
                            ->maxLength(255)
                            ->label('Ubicación de la Imagen')
                            ->placeholder('Dónde se encuentra la imagen...'),
                    ])->columns(2),

                Forms\Components\Section::make('Beneficios y Promesas')
                    ->schema([
                        Forms\Components\Textarea::make('spiritual_benefits')
                            ->maxLength(1000)
                            ->label('Beneficios Espirituales')
                            ->rows(3)
                            ->placeholder('Beneficios espirituales de la devoción...'),
                        
                        Forms\Components\Textarea::make('promises')
                            ->maxLength(1000)
                            ->label('Promesas')
                            ->rows(3)
                            ->placeholder('Promesas asociadas a la devoción...'),
                        
                        Forms\Components\Textarea::make('graces')
                            ->maxLength(1000)
                            ->label('Gracias')
                            ->rows(3)
                            ->placeholder('Gracias especiales otorgadas...'),
                        
                        Forms\Components\Textarea::make('indulgences')
                            ->maxLength(500)
                            ->label('Indulgencias')
                            ->placeholder('Indulgencias asociadas...'),
                        
                        Forms\Components\KeyValue::make('special_graces')
                            ->label('Gracias Especiales')
                            ->keyLabel('Gracia')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Gracia'),
                    ])->columns(1),

                Forms\Components\Section::make('Popularidad y Difusión')
                    ->schema([
                        Forms\Components\Select::make('popularity_level')
                            ->options([
                                'very_popular' => '🔥 Muy Popular',
                                'popular' => '⭐ Popular',
                                'moderate' => '🟡 Moderada',
                                'less_known' => '🟠 Menos Conocida',
                                'rare' => '🟣 Rara',
                                'obscure' => '⚫ Obscura',
                            ])
                            ->label('Nivel de Popularidad'),
                        
                        Forms\Components\TextInput::make('geographic_spread')
                            ->maxLength(255)
                            ->label('Difusión Geográfica')
                            ->placeholder('Países o regiones donde se practica...'),
                        
                        Forms\Components\TextInput::make('practitioners_count')
                            ->maxLength(100)
                            ->label('Número de Practicantes')
                            ->placeholder('Estimación del número de practicantes...'),
                        
                        Forms\Components\Toggle::make('is_international')
                            ->label('Internacional')
                            ->default(false)
                            ->helperText('Se practica en múltiples países'),
                        
                        Forms\Components\Toggle::make('is_ecumenical')
                            ->label('Ecuménica')
                            ->default(false)
                            ->helperText('Aceptada por múltiples denominaciones'),
                        
                        Forms\Components\TextInput::make('modern_adaptations')
                            ->maxLength(500)
                            ->label('Adaptaciones Modernas')
                            ->placeholder('Versiones modernas o adaptadas...'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Aprobación')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'approved' => '✅ Aprobada',
                                'pending' => '⏳ Pendiente',
                                'under_review' => '👀 En Revisión',
                                'conditionally_approved' => '🟣 Aprobada Condicionalmente',
                                'not_approved' => '❌ No Aprobada',
                                'deprecated' => '⚠️ Deprecada',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('approved')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Devoción importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_traditional')
                            ->label('Tradicional')
                            ->default(false)
                            ->helperText('Devoción con larga tradición'),
                        
                        Forms\Components\Toggle::make('is_modern')
                            ->label('Moderna')
                            ->default(false)
                            ->helperText('Devoción de origen reciente'),
                        
                        Forms\Components\TextInput::make('approval_date')
                            ->maxLength(100)
                            ->label('Fecha de Aprobación')
                            ->placeholder('Cuándo fue aprobada oficialmente...'),
                        
                        Forms\Components\TextInput::make('approving_authority')
                            ->maxLength(255)
                            ->label('Autoridad Aprobadora')
                            ->placeholder('Quién la aprobó oficialmente...'),
                        
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
                    ->label('Devoción')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('devotion_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'prayer',
                        'success' => 'novena',
                        'warning' => 'rosary',
                        'info' => 'litany',
                        'danger' => 'hymn',
                        'secondary' => 'meditation',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'prayer' => '🙏 Oración',
                        'novena' => '🕯️ Novena',
                        'rosary' => '📿 Rosario',
                        'litany' => '📜 Letanía',
                        'hymn' => '🎵 Himno',
                        'meditation' => '🧘 Meditación',
                        'fasting' => '🍽️ Ayuno',
                        'pilgrimage' => '🦅 Peregrinación',
                        'veneration' => '🕯️ Veneración',
                        'consecration' => '💎 Consagración',
                        'dedication' => '🏛️ Dedicación',
                        'celebration' => '🎉 Celebración',
                        'ritual' => '🔮 Ritual',
                        'ceremony' => '🎭 Ceremonia',
                        'tradition' => '📚 Tradición',
                        'custom' => '🏺 Costumbre',
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
                
                Tables\Columns\TextColumn::make('origin_date')
                    ->label('Origen')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('origin_location')
                    ->label('Lugar de Origen')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('patron_saint')
                    ->label('Santo Patrón')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('primary_shrine')
                    ->label('Santuario Principal')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\BadgeColumn::make('popularity_level')
                    ->label('Popularidad')
                    ->colors([
                        'danger' => 'very_popular',
                        'warning' => 'popular',
                        'info' => 'moderate',
                        'secondary' => 'less_known',
                        'dark' => 'rare',
                        'gray' => 'obscure',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'very_popular' => '🔥 Muy Popular',
                        'popular' => '⭐ Popular',
                        'moderate' => '🟡 Moderada',
                        'less_known' => '🟠 Menos Conocida',
                        'rare' => '🟣 Rara',
                        'obscure' => '⚫ Obscura',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'approved',
                        'warning' => 'pending',
                        'info' => 'under_review',
                        'secondary' => 'conditionally_approved',
                        'danger' => 'not_approved',
                        'dark' => 'deprecated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => '✅ Aprobada',
                        'pending' => '⏳ Pendiente',
                        'under_review' => '👀 En Revisión',
                        'conditionally_approved' => '🟣 Condicional',
                        'not_approved' => '❌ No Aprobada',
                        'deprecated' => '⚠️ Deprecada',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_traditional')
                    ->label('Tradicional')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_modern')
                    ->label('Moderna')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_international')
                    ->label('Internacional')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_ecumenical')
                    ->label('Ecuménica')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('devotion_type')
                    ->options([
                        'prayer' => '🙏 Oración',
                        'novena' => '🕯️ Novena',
                        'rosary' => '📿 Rosario',
                        'litany' => '📜 Letanía',
                        'hymn' => '🎵 Himno',
                        'meditation' => '🧘 Meditación',
                        'fasting' => '🍽️ Ayuno',
                        'pilgrimage' => '🦅 Peregrinación',
                        'veneration' => '🕯️ Veneración',
                        'consecration' => '💎 Consagración',
                        'dedication' => '🏛️ Dedicación',
                        'celebration' => '🎉 Celebración',
                        'ritual' => '🔮 Ritual',
                        'ceremony' => '🎭 Ceremonia',
                        'tradition' => '📚 Tradición',
                        'custom' => '🏺 Costumbre',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Devoción'),
                
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
                        'approved' => '✅ Aprobada',
                        'pending' => '⏳ Pendiente',
                        'under_review' => '👀 En Revisión',
                        'conditionally_approved' => '🟣 Aprobada Condicionalmente',
                        'not_approved' => '❌ No Aprobada',
                        'deprecated' => '⚠️ Deprecada',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\SelectFilter::make('popularity_level')
                    ->options([
                        'very_popular' => '🔥 Muy Popular',
                        'popular' => '⭐ Popular',
                        'moderate' => '🟡 Moderada',
                        'less_known' => '🟠 Menos Conocida',
                        'rare' => '🟣 Rara',
                        'obscure' => '⚫ Obscura',
                    ])
                    ->label('Nivel de Popularidad'),
                
                Tables\Filters\Filter::make('approved_only')
                    ->label('Solo Aprobadas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'approved')),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('traditional_only')
                    ->label('Solo Tradicionales')
                    ->query(fn (Builder $query): Builder => $query->where('is_traditional', true)),
                
                Tables\Filters\Filter::make('modern_only')
                    ->label('Solo Modernas')
                    ->query(fn (Builder $query): Builder => $query->where('is_modern', true)),
                
                Tables\Filters\Filter::make('international_only')
                    ->label('Solo Internacionales')
                    ->query(fn (Builder $query): Builder => $query->where('is_international', true)),
                
                Tables\Filters\Filter::make('ecumenical_only')
                    ->label('Solo Ecuménicas')
                    ->query(fn (Builder $query): Builder => $query->where('is_ecumenical', true)),
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
                
                Tables\Actions\Action::make('mark_traditional')
                    ->label('Marcar como Tradicional')
                    ->icon('fas-landmark')
                    ->action(function ($record): void {
                        $record->update(['is_traditional' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_traditional)
                    ->color('success'),
                
                Tables\Actions\Action::make('mark_modern')
                    ->label('Marcar como Moderna')
                    ->icon('fas-clock')
                    ->action(function ($record): void {
                        $record->update(['is_modern' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_modern)
                    ->color('info'),
                
                Tables\Actions\Action::make('approve_devotion')
                    ->label('Aprobar Devoción')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['status' => 'approved']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'approved')
                    ->color('success'),
                
                Tables\Actions\Action::make('view_shrine')
                    ->label('Ver Santuario')
                    ->icon('fas-mosque')
                    ->url(fn ($record): string => "https://maps.google.com/?q={$record->primary_shrine}")
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => !empty($record->primary_shrine))
                    ->color('primary'),
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
                    
                    Tables\Actions\BulkAction::make('mark_traditional')
                        ->label('Marcar como Tradicionales')
                        ->icon('fas-landmark')
                        ->action(function ($records): void {
                            $records->each->update(['is_traditional' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_modern')
                        ->label('Marcar como Modernas')
                        ->icon('fas-clock')
                        ->action(function ($records): void {
                            $records->each->update(['is_modern' => true]);
                        })
                        ->color('info'),
                    
                    Tables\Actions\BulkAction::make('approve_all')
                        ->label('Aprobar Todas')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'approved']);
                        })
                        ->color('success'),
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
            'index' => Pages\ListDevotions::route('/'),
            'create' => Pages\CreateDevotion::route('/create'),
            'view' => Pages\ViewDevotion::route('/{record}'),
            'edit' => Pages\EditDevotion::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
