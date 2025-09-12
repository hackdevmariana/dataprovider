<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyAnniversaryResource\Pages;
use App\Filament\Resources\DailyAnniversaryResource\RelationManagers;
use App\Models\DailyAnniversary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DailyAnniversaryResource extends Resource
{
    protected static ?string $model = DailyAnniversary::class;

    protected static ?string $navigationIcon = 'fas-calendar-day';

    protected static ?string $navigationGroup = 'Historia y Cultura';

    protected static ?string $navigationLabel = 'Aniversarios Diarios';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Aniversario Diario';

    protected static ?string $pluralModelLabel = 'Aniversarios Diarios';

    
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
                            ->label('Título del Aniversario')
                            ->placeholder('Título descriptivo del aniversario...'),
                        
                        Forms\Components\TextInput::make('anniversary_code')
                            ->maxLength(100)
                            ->label('Código del Aniversario')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada del aniversario...'),
                        
                        Forms\Components\Select::make('anniversary_type')
                            ->options([
                                'birth' => '🎂 Nacimiento',
                                'death' => '🕯️ Fallecimiento',
                                'event' => '🎪 Evento',
                                'discovery' => '🔍 Descubrimiento',
                                'invention' => '💡 Invención',
                                'publication' => '📚 Publicación',
                                'premiere' => '🎭 Estreno',
                                'exhibition' => '🖼️ Exposición',
                                'performance' => '🎵 Interpretación',
                                'speech' => '🎤 Discurso',
                                'treaty' => '📜 Tratado',
                                'declaration' => '📢 Declaración',
                                'coronation' => '👑 Coronación',
                                'inauguration' => '🏛️ Inauguración',
                                'foundation' => '🏗️ Fundación',
                                'opening' => '🚪 Apertura',
                                'closing' => '🔒 Clausura',
                                'victory' => '🏆 Victoria',
                                'defeat' => '💔 Derrota',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Aniversario'),
                        
                        Forms\Components\Select::make('category')
                            ->options([
                                'historical' => '🏛️ Histórico',
                                'cultural' => '🎭 Cultural',
                                'scientific' => '🔬 Científico',
                                'artistic' => '🎨 Artístico',
                                'literary' => '📚 Literario',
                                'musical' => '🎵 Musical',
                                'political' => '🏛️ Político',
                                'military' => '⚔️ Militar',
                                'religious' => '⛪ Religioso',
                                'sports' => '⚽ Deportivo',
                                'business' => '💼 Empresarial',
                                'educational' => '🎓 Educativo',
                                'medical' => '🏥 Médico',
                                'technological' => '⚙️ Tecnológico',
                                'environmental' => '🌱 Ambiental',
                                'social' => '🤝 Social',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría'),
                        
                        Forms\Components\Select::make('importance_level')
                            ->options([
                                'critical' => '🔴 Crítico',
                                'high' => '🟠 Alto',
                                'medium' => '🟡 Medio',
                                'low' => '🟢 Bajo',
                                'minor' => '🔵 Menor',
                                'other' => '⚫ Otro',
                            ])
                            ->required()
                            ->default('medium')
                            ->label('Nivel de Importancia'),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas y Períodos')
                    ->schema([
                        Forms\Components\DatePicker::make('anniversary_date')
                            ->required()
                            ->label('Fecha del Aniversario')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha del evento original'),
                        
                        Forms\Components\TextInput::make('original_year')
                            ->maxLength(4)
                            ->label('Año Original')
                            ->placeholder('Año del evento original...'),
                        
                        Forms\Components\TextInput::make('century')
                            ->maxLength(20)
                            ->label('Siglo')
                            ->placeholder('Siglo del evento...'),
                        
                        Forms\Components\TextInput::make('era')
                            ->maxLength(100)
                            ->label('Era')
                            ->placeholder('Era histórica...'),
                        
                        Forms\Components\TextInput::make('season')
                            ->maxLength(20)
                            ->label('Estación')
                            ->placeholder('Primavera, verano, otoño, invierno...'),
                        
                        Forms\Components\TextInput::make('day_of_week')
                            ->maxLength(20)
                            ->label('Día de la Semana')
                            ->placeholder('Lunes, martes, miércoles...'),
                        
                        Forms\Components\Toggle::make('is_leap_year')
                            ->label('Año Bisiesto')
                            ->default(false)
                            ->helperText('El evento ocurrió en año bisiesto'),
                        
                        Forms\Components\TextInput::make('lunar_date')
                            ->maxLength(100)
                            ->label('Fecha Lunar')
                            ->placeholder('Fecha en calendario lunar...'),
                    ])->columns(2),

                Forms\Components\Section::make('Personas y Entidades')
                    ->schema([
                        Forms\Components\TextInput::make('person_name')
                            ->maxLength(255)
                            ->label('Nombre de la Persona')
                            ->placeholder('Nombre de la persona principal...'),
                        
                        Forms\Components\TextInput::make('person_role')
                            ->maxLength(255)
                            ->label('Rol de la Persona')
                            ->placeholder('Rol o profesión de la persona...'),
                        
                        Forms\Components\TextInput::make('organization_name')
                            ->maxLength(255)
                            ->label('Nombre de la Organización')
                            ->placeholder('Nombre de la organización...'),
                        
                        Forms\Components\TextInput::make('organization_type')
                            ->maxLength(100)
                            ->label('Tipo de Organización')
                            ->placeholder('Empresa, gobierno, ONG...'),
                        
                        Forms\Components\TextInput::make('country')
                            ->maxLength(100)
                            ->label('País')
                            ->placeholder('País donde ocurrió el evento...'),
                        
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100)
                            ->label('Ciudad')
                            ->placeholder('Ciudad donde ocurrió el evento...'),
                        
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255)
                            ->label('Ubicación Específica')
                            ->placeholder('Lugar específico del evento...'),
                        
                        Forms\Components\KeyValue::make('other_participants')
                            ->label('Otros Participantes')
                            ->keyLabel('Nombre')
                            ->valueLabel('Rol')
                            ->addActionLabel('Agregar Participante'),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles del Evento')
                    ->schema([
                        Forms\Components\Textarea::make('event_details')
                            ->maxLength(1000)
                            ->label('Detalles del Evento')
                            ->rows(3)
                            ->placeholder('Descripción detallada de lo que ocurrió...'),
                        
                        Forms\Components\Textarea::make('background_context')
                            ->maxLength(1000)
                            ->label('Contexto de Fondo')
                            ->rows(3)
                            ->placeholder('Contexto histórico del evento...'),
                        
                        Forms\Components\Textarea::make('immediate_impact')
                            ->maxLength(500)
                            ->label('Impacto Inmediato')
                            ->rows(2)
                            ->placeholder('Consecuencias inmediatas del evento...'),
                        
                        Forms\Components\Textarea::make('long_term_impact')
                            ->maxLength(500)
                            ->label('Impacto a Largo Plazo')
                            ->rows(2)
                            ->placeholder('Consecuencias a largo plazo...'),
                        
                        Forms\Components\KeyValue::make('key_facts')
                            ->label('Datos Clave')
                            ->keyLabel('Dato')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Dato'),
                        
                        Forms\Components\Textarea::make('interesting_facts')
                            ->maxLength(500)
                            ->label('Datos Interesantes')
                            ->rows(2)
                            ->placeholder('Datos curiosos o interesantes...'),
                    ])->columns(1),

                Forms\Components\Section::make('Conmemoración y Celebración')
                    ->schema([
                        Forms\Components\TextInput::make('commemoration_type')
                            ->maxLength(100)
                            ->label('Tipo de Conmemoración')
                            ->placeholder('Celebración, recordatorio, homenaje...'),
                        
                        Forms\Components\Textarea::make('commemoration_activities')
                            ->maxLength(500)
                            ->label('Actividades de Conmemoración')
                            ->rows(2)
                            ->placeholder('Actividades realizadas para conmemorar...'),
                        
                        Forms\Components\TextInput::make('commemoration_frequency')
                            ->maxLength(100)
                            ->label('Frecuencia de Conmemoración')
                            ->placeholder('Anual, quinquenal, centenario...'),
                        
                        Forms\Components\Toggle::make('has_annual_celebration')
                            ->label('Tiene Celebración Anual')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_special_ceremony')
                            ->label('Tiene Ceremonia Especial')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_memorial')
                            ->label('Tiene Memorial')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_plaque')
                            ->label('Tiene Placa Conmemorativa')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_monument')
                            ->label('Tiene Monumento')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('commemoration_location')
                            ->maxLength(255)
                            ->label('Ubicación de la Conmemoración')
                            ->placeholder('Dónde se conmemora...'),
                    ])->columns(2),

                Forms\Components\Section::make('Medios y Documentación')
                    ->schema([
                        Forms\Components\Toggle::make('has_photographs')
                            ->label('Tiene Fotografías')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_videos')
                            ->label('Tiene Videos')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_audio')
                            ->label('Tiene Audio')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_documents')
                            ->label('Tiene Documentos')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('has_artifacts')
                            ->label('Tiene Artefactos')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('media_location')
                            ->maxLength(255)
                            ->label('Ubicación de los Medios')
                            ->placeholder('Dónde se encuentran los medios...'),
                        
                        Forms\Components\TextInput::make('documentation_quality')
                            ->maxLength(100)
                            ->label('Calidad de la Documentación')
                            ->placeholder('Excelente, buena, regular, pobre...'),
                        
                        Forms\Components\Textarea::make('media_description')
                            ->maxLength(500)
                            ->label('Descripción de los Medios')
                            ->rows(2)
                            ->placeholder('Descripción de los medios disponibles...'),
                    ])->columns(2),

                Forms\Components\Section::make('Fuentes y Referencias')
                    ->schema([
                        Forms\Components\Textarea::make('primary_sources')
                            ->maxLength(500)
                            ->label('Fuentes Primarias')
                            ->rows(2)
                            ->placeholder('Documentos originales, testimonios...'),
                        
                        Forms\Components\Textarea::make('secondary_sources')
                            ->maxLength(500)
                            ->label('Fuentes Secundarias')
                            ->rows(2)
                            ->placeholder('Libros, artículos, estudios...'),
                        
                        Forms\Components\TextInput::make('archival_references')
                            ->maxLength(255)
                            ->label('Referencias de Archivo')
                            ->placeholder('Referencias en archivos...'),
                        
                        Forms\Components\TextInput::make('museum_references')
                            ->maxLength(255)
                            ->label('Referencias de Museo')
                            ->placeholder('Referencias en museos...'),
                        
                        Forms\Components\TextInput::make('library_references')
                            ->maxLength(255)
                            ->label('Referencias de Biblioteca')
                            ->placeholder('Referencias en bibliotecas...'),
                        
                        Forms\Components\KeyValue::make('additional_references')
                            ->label('Referencias Adicionales')
                            ->keyLabel('Tipo')
                            ->valueLabel('Referencia')
                            ->addActionLabel('Agregar Referencia'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Verificación')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'verified' => '✅ Verificado',
                                'pending_verification' => '⏳ Pendiente de Verificación',
                                'under_review' => '👀 En Revisión',
                                'disputed' => '⚠️ Disputado',
                                'legendary' => '📖 Legendario',
                                'mythical' => '🐉 Mítico',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('pending_verification')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Aniversario importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_controversial')
                            ->label('Controvertido')
                            ->default(false)
                            ->helperText('El evento es objeto de controversia'),
                        
                        Forms\Components\Toggle::make('is_celebrated')
                            ->label('Se Celebra')
                            ->default(false)
                            ->helperText('Se celebra activamente'),
                        
                        Forms\Components\Toggle::make('is_remembered')
                            ->label('Se Recuerda')
                            ->default(true)
                            ->helperText('Se recuerda o conmemora'),
                        
                        Forms\Components\TextInput::make('verification_level')
                            ->maxLength(100)
                            ->label('Nivel de Verificación')
                            ->placeholder('Alto, medio, bajo...'),
                        
                        Forms\Components\TextInput::make('verification_date')
                            ->maxLength(100)
                            ->label('Fecha de Verificación')
                            ->placeholder('Cuándo fue verificado...'),
                        
                        Forms\Components\TextInput::make('verified_by')
                            ->maxLength(255)
                            ->label('Verificado por')
                            ->placeholder('Quién lo verificó...'),
                        
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
                    ->label('Aniversario')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('anniversary_type')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'birth',
                        'danger' => 'death',
                        'primary' => 'event',
                        'info' => 'discovery',
                        'warning' => 'invention',
                        'secondary' => 'publication',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'birth' => '🎂 Nacimiento',
                        'death' => '🕯️ Fallecimiento',
                        'event' => '🎪 Evento',
                        'discovery' => '🔍 Descubrimiento',
                        'invention' => '💡 Invención',
                        'publication' => '📚 Publicación',
                        'premiere' => '🎭 Estreno',
                        'exhibition' => '🖼️ Exposición',
                        'performance' => '🎵 Interpretación',
                        'speech' => '🎤 Discurso',
                        'treaty' => '📜 Tratado',
                        'declaration' => '📢 Declaración',
                        'coronation' => '👑 Coronación',
                        'inauguration' => '🏛️ Inauguración',
                        'foundation' => '🏗️ Fundación',
                        'opening' => '🚪 Apertura',
                        'closing' => '🔒 Clausura',
                        'victory' => '🏆 Victoria',
                        'defeat' => '💔 Derrota',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'historical',
                        'success' => 'cultural',
                        'info' => 'scientific',
                        'warning' => 'artistic',
                        'danger' => 'literary',
                        'secondary' => 'musical',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'historical' => '🏛️ Histórico',
                        'cultural' => '🎭 Cultural',
                        'scientific' => '🔬 Científico',
                        'artistic' => '🎨 Artístico',
                        'literary' => '📚 Literario',
                        'musical' => '🎵 Musical',
                        'political' => '🏛️ Político',
                        'military' => '⚔️ Militar',
                        'religious' => '⛪ Religioso',
                        'sports' => '⚽ Deportivo',
                        'business' => '💼 Empresarial',
                        'educational' => '🎓 Educativo',
                        'medical' => '🏥 Médico',
                        'technological' => '⚙️ Tecnológico',
                        'environmental' => '🌱 Ambiental',
                        'social' => '🤝 Social',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('anniversary_date')
                    ->label('Fecha')
                    ->date('d/m')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('original_year')
                    ->label('Año')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('person_name')
                    ->label('Persona')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\BadgeColumn::make('importance_level')
                    ->label('Importancia')
                    ->colors([
                        'danger' => 'critical',
                        'warning' => 'high',
                        'info' => 'medium',
                        'success' => 'low',
                        'secondary' => 'minor',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'critical' => '🔴 Crítico',
                        'high' => '🟠 Alto',
                        'medium' => '🟡 Medio',
                        'low' => '🟢 Bajo',
                        'minor' => '🔵 Menor',
                        'other' => '⚫ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'verified',
                        'warning' => 'pending_verification',
                        'info' => 'under_review',
                        'danger' => 'disputed',
                        'secondary' => 'legendary',
                        'dark' => 'mythical',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'verified' => '✅ Verificado',
                        'pending_verification' => '⏳ Pendiente',
                        'under_review' => '👀 En Revisión',
                        'disputed' => '⚠️ Disputado',
                        'legendary' => '📖 Legendario',
                        'mythical' => '🐉 Mítico',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_celebrated')
                    ->label('Se Celebra')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_controversial')
                    ->label('Controvertido')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('days_until_anniversary')
                    ->label('Días Restantes')
                    ->formatStateUsing(function ($record) {
                        $today = now();
                        $anniversary = $record->anniversary_date;
                        if (!$anniversary) return 'N/A';
                        
                        $nextAnniversary = $anniversary->copy()->setYear($today->year);
                        if ($nextAnniversary->isPast()) {
                            $nextAnniversary->addYear();
                        }
                        
                        $days = $today->diffInDays($nextAnniversary, false);
                        return $days > 0 ? "+{$days}" : "Hoy";
                    })
                    ->color(function ($record) {
                        $today = now();
                        $anniversary = $record->anniversary_date;
                        if (!$anniversary) return 'secondary';
                        
                        $nextAnniversary = $anniversary->copy()->setYear($today->year);
                        if ($nextAnniversary->isPast()) {
                            $nextAnniversary->addYear();
                        }
                        
                        $days = $today->diffInDays($nextAnniversary, false);
                        if ($days === 0) return 'success';
                        if ($days <= 7) return 'warning';
                        if ($days <= 30) return 'info';
                        return 'secondary';
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('anniversary_type')
                    ->options([
                        'birth' => '🎂 Nacimiento',
                        'death' => '🕯️ Fallecimiento',
                        'event' => '🎪 Evento',
                        'discovery' => '🔍 Descubrimiento',
                        'invention' => '💡 Invención',
                        'publication' => '📚 Publicación',
                        'premiere' => '🎭 Estreno',
                        'exhibition' => '🖼️ Exposición',
                        'performance' => '🎵 Interpretación',
                        'speech' => '🎤 Discurso',
                        'treaty' => '📜 Tratado',
                        'declaration' => '📢 Declaración',
                        'coronation' => '👑 Coronación',
                        'inauguration' => '🏛️ Inauguración',
                        'foundation' => '🏗️ Fundación',
                        'opening' => '🚪 Apertura',
                        'closing' => '🔒 Clausura',
                        'victory' => '🏆 Victoria',
                        'defeat' => '💔 Derrota',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Aniversario'),
                
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'historical' => '🏛️ Histórico',
                        'cultural' => '🎭 Cultural',
                        'scientific' => '🔬 Científico',
                        'artistic' => '🎨 Artístico',
                        'literary' => '📚 Literario',
                        'musical' => '🎵 Musical',
                        'political' => '🏛️ Político',
                        'military' => '⚔️ Militar',
                        'religious' => '⛪ Religioso',
                        'sports' => '⚽ Deportivo',
                        'business' => '💼 Empresarial',
                        'educational' => '🎓 Educativo',
                        'medical' => '🏥 Médico',
                        'technological' => '⚙️ Tecnológico',
                        'environmental' => '🌱 Ambiental',
                        'social' => '🤝 Social',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría'),
                
                Tables\Filters\SelectFilter::make('importance_level')
                    ->options([
                        'critical' => '🔴 Crítico',
                        'high' => '🟠 Alto',
                        'medium' => '🟡 Medio',
                        'low' => '🟢 Bajo',
                        'minor' => '🔵 Menor',
                        'other' => '⚫ Otro',
                    ])
                    ->label('Nivel de Importancia'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'verified' => '✅ Verificado',
                        'pending_verification' => '⏳ Pendiente de Verificación',
                        'under_review' => '👀 En Revisión',
                        'disputed' => '⚠️ Disputado',
                        'legendary' => '📖 Legendario',
                        'mythical' => '🐉 Mítico',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('celebrated_only')
                    ->label('Solo los que se Celebran')
                    ->query(fn (Builder $query): Builder => $query->where('is_celebrated', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificados')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'verified')),
                
                Tables\Filters\Filter::make('controversial_only')
                    ->label('Solo Controvertidos')
                    ->query(fn (Builder $query): Builder => $query->where('is_controversial', true)),
                
                Tables\Filters\Filter::make('upcoming_anniversaries')
                    ->label('Aniversarios Próximos (7 días)')
                    ->query(function (Builder $query) {
                        $today = now();
                        $nextWeek = $today->copy()->addDays(7);
                        return $query->whereRaw("DATE_FORMAT(anniversary_date, '%m-%d') BETWEEN ? AND ?", [
                            $today->format('m-d'),
                            $nextWeek->format('m-d')
                        ]);
                    }),
                
                Tables\Filters\Filter::make('today_anniversaries')
                    ->label('Aniversarios de Hoy')
                    ->query(function (Builder $query) {
                        $today = now();
                        return $query->whereRaw("DATE_FORMAT(anniversary_date, '%m-%d') = ?", [$today->format('m-d')]);
                    }),
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
                    ->label(function ($record) {
                        return $record->is_featured ? 'Quitar Destacado' : 'Destacar';
                    })
                    ->icon(function ($record) {
                        return $record->is_featured ? 'fas-star' : 'far-star';
                    })
                    ->action(function ($record) {
                        $record->update(['is_featured' => !$record->is_featured]);
                    })
                    ->color(function ($record) {
                        return $record->is_featured ? 'warning' : 'success';
                    }),
                
                Tables\Actions\Action::make('mark_verified')
                    ->label('Marcar como Verificado')
                    ->icon('fas-check-circle')
                    ->action(function ($record) {
                        $record->update(['status' => 'verified']);
                    })
                    ->visible(function ($record) {
                        return $record->status !== 'verified';
                    })
                    ->color('success'),
                
                Tables\Actions\Action::make('mark_celebrated')
                    ->label('Marcar como Celebrado')
                    ->icon('fas-birthday-cake')
                    ->action(function ($record) {
                        $record->update(['is_celebrated' => true]);
                    })
                    ->visible(function ($record) {
                        return !$record->is_celebrated;
                    })
                    ->color('success'),
                
                Tables\Actions\Action::make('view_location')
                    ->label('Ver Ubicación')
                    ->icon('fas-map-marker-alt')
                    ->url(function ($record) {
                        return "https://maps.google.com/?q={$record->location}";
                    })
                    ->openUrlInNewTab()
                    ->visible(function ($record) {
                        return !empty($record->location);
                    })
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
                            $records->each->update(['status' => 'verified']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_celebrated')
                        ->label('Marcar como Celebrados')
                        ->icon('fas-birthday-cake')
                        ->action(function ($records): void {
                            $records->each->update(['is_celebrated' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_high_importance')
                        ->label('Marcar como Alta Importancia')
                        ->icon('fas-exclamation-triangle')
                        ->action(function ($records): void {
                            $records->each->update(['importance_level' => 'high']);
                        })
                        ->color('warning'),
                ]),
            ])
            ->defaultSort('anniversary_date', 'asc')
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
            'index' => Pages\ListDailyAnniversaries::route('/'),
            'create' => Pages\CreateDailyAnniversary::route('/create'),
            'view' => Pages\ViewDailyAnniversary::route('/{record}'),
            'edit' => Pages\EditDailyAnniversary::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
