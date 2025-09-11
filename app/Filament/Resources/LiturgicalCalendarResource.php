<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiturgicalCalendarResource\Pages;
use App\Filament\Resources\LiturgicalCalendarResource\RelationManagers;
use App\Models\LiturgicalCalendar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LiturgicalCalendarResource extends Resource
{
    protected static ?string $model = LiturgicalCalendar::class;

    protected static ?string $navigationIcon = 'fas-calendar-alt';

    protected static ?string $navigationGroup = 'Religión y Espiritualidad';

    protected static ?string $navigationLabel = 'Calendario Litúrgico';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Calendario Litúrgico';

    protected static ?string $pluralModelLabel = 'Calendarios Litúrgicos';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('calendar_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Calendario')
                            ->placeholder('Nombre del calendario litúrgico...'),
                        
                        Forms\Components\TextInput::make('calendar_code')
                            ->maxLength(100)
                            ->label('Código del Calendario')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción del calendario litúrgico...'),
                        
                        Forms\Components\Select::make('calendar_type')
                            ->options([
                                'roman_catholic' => '⛪ Católico Romano',
                                'eastern_orthodox' => '☦️ Ortodoxo Oriental',
                                'anglican' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿 Anglicano',
                                'lutheran' => '✝️ Luterano',
                                'methodist' => '⛪ Metodista',
                                'baptist' => '🕊️ Bautista',
                                'presbyterian' => '⛪ Presbiteriano',
                                'coptic' => '☦️ Copto',
                                'syriac' => '☦️ Siriaco',
                                'armenian' => '☦️ Armenio',
                                'ethiopian' => '☦️ Etíope',
                                'maronite' => '☦️ Maronita',
                                'melkite' => '☦️ Melquita',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Calendario'),
                        
                        Forms\Components\Select::make('rite')
                            ->options([
                                'latin' => '🇻🇦 Rito Latino',
                                'byzantine' => '☦️ Rito Bizantino',
                                'alexandrian' => '☦️ Rito Alejandrino',
                                'antiochian' => '☦️ Rito Antioqueno',
                                'armenian' => '☦️ Rito Armenio',
                                'chaldean' => '☦️ Rito Caldeo',
                                'syriac' => '☦️ Rito Siriaco',
                                'maronite' => '☦️ Rito Maronita',
                                'melkite' => '☦️ Rito Melquita',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Rito'),
                        
                        Forms\Components\Select::make('language')
                            ->options([
                                'es' => '🇪🇸 Español',
                                'la' => '🇻🇦 Latín',
                                'en' => '🇺🇸 Inglés',
                                'fr' => '🇫🇷 Francés',
                                'de' => '🇩🇪 Alemán',
                                'it' => '🇮🇹 Italiano',
                                'pt' => '🇵🇹 Portugués',
                                'ca' => '🇪🇸 Catalán',
                                'eu' => '🇪🇸 Euskera',
                                'gl' => '🇪🇸 Gallego',
                                'ar' => '🇸🇾 Árabe',
                                'el' => '🇬🇷 Griego',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('es')
                            ->label('Idioma'),
                        
                        Forms\Components\Select::make('region')
                            ->options([
                                'spain' => '🇪🇸 España',
                                'italy' => '🇮🇹 Italia',
                                'france' => '🇫🇷 Francia',
                                'germany' => '🇩🇪 Alemania',
                                'poland' => '🇵🇱 Polonia',
                                'ireland' => '🇮🇪 Irlanda',
                                'portugal' => '🇵🇹 Portugal',
                                'mexico' => '🇲🇽 México',
                                'brazil' => '🇧🇷 Brasil',
                                'argentina' => '🇦🇷 Argentina',
                                'colombia' => '🇨🇴 Colombia',
                                'peru' => '🇵🇪 Perú',
                                'chile' => '🇨🇱 Chile',
                                'venezuela' => '🇻🇪 Venezuela',
                                'ecuador' => '🇪🇨 Ecuador',
                                'bolivia' => '🇧🇴 Bolivia',
                                'paraguay' => '🇵🇾 Paraguay',
                                'uruguay' => '🇺🇾 Uruguay',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Región'),
                        
                        Forms\Components\TextInput::make('diocese')
                            ->maxLength(255)
                            ->label('Diócesis')
                            ->placeholder('Diócesis específica...'),
                        
                        Forms\Components\TextInput::make('parish')
                            ->maxLength(255)
                            ->label('Parroquia')
                            ->placeholder('Parroquia específica...'),
                    ])->columns(2),

                Forms\Components\Section::make('Período y Cobertura')
                    ->schema([
                        Forms\Components\Select::make('liturgical_year')
                            ->options([
                                '2024' => '2024',
                                '2025' => '2025',
                                '2026' => '2026',
                                '2027' => '2027',
                                '2028' => '2028',
                                '2029' => '2029',
                                '2030' => '2030',
                                'other' => 'Otro',
                            ])
                            ->required()
                            ->default('2025')
                            ->label('Año Litúrgico'),
                        
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->label('Fecha de Inicio')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha de inicio del año litúrgico'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->label('Fecha de Fin')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha de fin del año litúrgico'),
                        
                        Forms\Components\Select::make('advent_start')
                            ->options([
                                'first_sunday_advent' => 'Primer Domingo de Adviento',
                                'second_sunday_advent' => 'Segundo Domingo de Adviento',
                                'third_sunday_advent' => 'Tercer Domingo de Adviento',
                                'fourth_sunday_advent' => 'Cuarto Domingo de Adviento',
                                'other' => 'Otro',
                            ])
                            ->label('Inicio del Adviento'),
                        
                        Forms\Components\DatePicker::make('christmas_date')
                            ->label('Fecha de Navidad')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\DatePicker::make('epiphany_date')
                            ->label('Fecha de Epifanía')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\DatePicker::make('ash_wednesday_date')
                            ->label('Fecha de Miércoles de Ceniza')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\DatePicker::make('easter_date')
                            ->label('Fecha de Pascua')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\DatePicker::make('pentecost_date')
                            ->label('Fecha de Pentecostés')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\DatePicker::make('trinity_sunday_date')
                            ->label('Fecha de Domingo de la Santísima Trinidad')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\DatePicker::make('christ_king_date')
                            ->label('Fecha de Cristo Rey')
                            ->displayFormat('d/m/Y'),
                    ])->columns(2),

                Forms\Components\Section::make('Temporadas Litúrgicas')
                    ->schema([
                        Forms\Components\Toggle::make('includes_advent')
                            ->label('Incluye Adviento')
                            ->default(true)
                            ->helperText('El calendario incluye la temporada de Adviento'),
                        
                        Forms\Components\Toggle::make('includes_christmas')
                            ->label('Incluye Navidad')
                            ->default(true)
                            ->helperText('El calendario incluye la temporada de Navidad'),
                        
                        Forms\Components\Toggle::make('includes_ordinary_time_1')
                            ->label('Incluye Tiempo Ordinario I')
                            ->default(true)
                            ->helperText('El calendario incluye el primer tiempo ordinario'),
                        
                        Forms\Components\Toggle::make('includes_lent')
                            ->label('Incluye Cuaresma')
                            ->default(true)
                            ->helperText('El calendario incluye la temporada de Cuaresma'),
                        
                        Forms\Components\Toggle::make('includes_easter')
                            ->label('Incluye Pascua')
                            ->default(true)
                            ->helperText('El calendario incluye la temporada de Pascua'),
                        
                        Forms\Components\Toggle::make('includes_ordinary_time_2')
                            ->label('Incluye Tiempo Ordinario II')
                            ->default(true)
                            ->helperText('El calendario incluye el segundo tiempo ordinario'),
                        
                        Forms\Components\Toggle::make('includes_saints')
                            ->label('Incluye Santos')
                            ->default(true)
                            ->helperText('El calendario incluye festividades de santos'),
                        
                        Forms\Components\Toggle::make('includes_marian_feasts')
                            ->label('Incluye Fiestas Marianas')
                            ->default(true)
                            ->helperText('El calendario incluye fiestas marianas'),
                        
                        Forms\Components\Toggle::make('includes_solemnities')
                            ->label('Incluye Solemnidades')
                            ->default(true)
                            ->helperText('El calendario incluye solemnidades'),
                        
                        Forms\Components\Toggle::make('includes_feasts')
                            ->label('Incluye Fiestas')
                            ->default(true)
                            ->helperText('El calendario incluye fiestas'),
                        
                        Forms\Components\Toggle::make('includes_memorials')
                            ->label('Incluye Memorias')
                            ->default(true)
                            ->helperText('El calendario incluye memorias'),
                        
                        Forms\Components\Toggle::make('includes_optional_memorials')
                            ->label('Incluye Memorias Opcionales')
                            ->default(true)
                            ->helperText('El calendario incluye memorias opcionales'),
                    ])->columns(2),

                Forms\Components\Section::make('Características Especiales')
                    ->schema([
                        Forms\Components\Toggle::make('has_vigils')
                            ->label('Tiene Vigilias')
                            ->default(false)
                            ->helperText('El calendario incluye vigilias'),
                        
                        Forms\Components\Toggle::make('has_octaves')
                            ->label('Tiene Octavas')
                            ->default(false)
                            ->helperText('El calendario incluye octavas'),
                        
                        Forms\Components\Toggle::make('has_ferias')
                            ->label('Tiene Ferias')
                            ->default(true)
                            ->helperText('El calendario incluye ferias'),
                        
                        Forms\Components\Toggle::make('has_ember_days')
                            ->label('Tiene Témporas')
                            ->default(false)
                            ->helperText('El calendario incluye témporas'),
                        
                        Forms\Components\Toggle::make('has_rogation_days')
                            ->label('Tiene Rogativas')
                            ->default(false)
                            ->helperText('El calendario incluye rogativas'),
                        
                        Forms\Components\Toggle::make('has_stations')
                            ->label('Tiene Estaciones')
                            ->default(false)
                            ->helperText('El calendario incluye estaciones'),
                        
                        Forms\Components\Toggle::make('has_processions')
                            ->label('Tiene Procesiones')
                            ->default(false)
                            ->helperText('El calendario incluye procesiones'),
                        
                        Forms\Components\Toggle::make('has_blessings')
                            ->label('Tiene Bendiciones')
                            ->default(false)
                            ->helperText('El calendario incluye bendiciones'),
                        
                        Forms\Components\Toggle::make('has_exorcisms')
                            ->label('Tiene Exorcismos')
                            ->default(false)
                            ->helperText('El calendario incluye exorcismos'),
                        
                        Forms\Components\Toggle::make('has_consecrations')
                            ->label('Tiene Consagraciones')
                            ->default(false)
                            ->helperText('El calendario incluye consagraciones'),
                    ])->columns(2),

                Forms\Components\Section::make('Colores Litúrgicos')
                    ->schema([
                        Forms\Components\Toggle::make('uses_white')
                            ->label('Usa Blanco')
                            ->default(true)
                            ->helperText('El calendario usa el color blanco'),
                        
                        Forms\Components\Toggle::make('uses_red')
                            ->label('Usa Rojo')
                            ->default(true)
                            ->helperText('El calendario usa el color rojo'),
                        
                        Forms\Components\Toggle::make('uses_green')
                            ->label('Usa Verde')
                            ->default(true)
                            ->helperText('El calendario usa el color verde'),
                        
                        Forms\Components\Toggle::make('uses_purple')
                            ->label('Usa Morado')
                            ->default(true)
                            ->helperText('El calendario usa el color morado'),
                        
                        Forms\Components\Toggle::make('uses_pink')
                            ->label('Usa Rosa')
                            ->default(true)
                            ->helperText('El calendario usa el color rosa'),
                        
                        Forms\Components\Toggle::make('uses_black')
                            ->label('Usa Negro')
                            ->default(false)
                            ->helperText('El calendario usa el color negro'),
                        
                        Forms\Components\Toggle::make('uses_gold')
                            ->label('Usa Dorado')
                            ->default(false)
                            ->helperText('El calendario usa el color dorado'),
                        
                        Forms\Components\Toggle::make('uses_blue')
                            ->label('Usa Azul')
                            ->default(false)
                            ->helperText('El calendario usa el color azul'),
                        
                        Forms\Components\Textarea::make('color_notes')
                            ->maxLength(500)
                            ->label('Notas sobre Colores')
                            ->rows(2)
                            ->placeholder('Notas especiales sobre el uso de colores...'),
                    ])->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('special_instructions')
                            ->maxLength(1000)
                            ->label('Instrucciones Especiales')
                            ->rows(3)
                            ->placeholder('Instrucciones especiales para el uso del calendario...'),
                        
                        Forms\Components\Textarea::make('variations')
                            ->maxLength(500)
                            ->label('Variaciones')
                            ->rows(2)
                            ->placeholder('Variaciones específicas de este calendario...'),
                        
                        Forms\Components\Textarea::make('exceptions')
                            ->maxLength(500)
                            ->label('Excepciones')
                            ->rows(2)
                            ->placeholder('Excepciones a las reglas generales...'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas adicionales...'),
                        
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Campo'),
                    ])->columns(1),

                Forms\Components\Section::make('Estado y Calidad')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => '📝 Borrador',
                                'active' => '✅ Activo',
                                'inactive' => '❌ Inactivo',
                                'under_review' => '👀 En Revisión',
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
                            ->helperText('Calendario importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado')
                            ->default(false)
                            ->helperText('El calendario ha sido verificado'),
                        
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Aprobado')
                            ->default(false)
                            ->helperText('El calendario ha sido aprobado'),
                        
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
                            ->placeholder('Persona que revisó el calendario...'),
                        
                        Forms\Components\DatePicker::make('review_date')
                            ->label('Fecha de Revisión')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\TextInput::make('update_frequency')
                            ->maxLength(100)
                            ->label('Frecuencia de Actualización')
                            ->placeholder('Anual, cuando sea necesario...'),
                        
                        Forms\Components\DatePicker::make('last_updated')
                            ->label('Última Actualización')
                            ->displayFormat('d/m/Y'),
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
                
                Tables\Columns\TextColumn::make('calendar_name')
                    ->label('Calendario')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('calendar_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'roman_catholic',
                        'success' => 'eastern_orthodox',
                        'warning' => 'anglican',
                        'info' => 'lutheran',
                        'danger' => 'methodist',
                        'secondary' => 'baptist',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'roman_catholic' => '⛪ Católico Romano',
                        'eastern_orthodox' => '☦️ Ortodoxo Oriental',
                        'anglican' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿 Anglicano',
                        'lutheran' => '✝️ Luterano',
                        'methodist' => '⛪ Metodista',
                        'baptist' => '🕊️ Bautista',
                        'presbyterian' => '⛪ Presbiteriano',
                        'coptic' => '☦️ Copto',
                        'syriac' => '☦️ Siriaco',
                        'armenian' => '☦️ Armenio',
                        'ethiopian' => '☦️ Etíope',
                        'maronite' => '☦️ Maronita',
                        'melkite' => '☦️ Melquita',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('rite')
                    ->label('Rito')
                    ->colors([
                        'danger' => 'latin',
                        'info' => 'byzantine',
                        'warning' => 'alexandrian',
                        'success' => 'antiochian',
                        'primary' => 'armenian',
                        'secondary' => 'chaldean',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'latin' => '🇻🇦 Rito Latino',
                        'byzantine' => '☦️ Rito Bizantino',
                        'alexandrian' => '☦️ Rito Alejandrino',
                        'antiochian' => '☦️ Rito Antioqueno',
                        'armenian' => '☦️ Rito Armenio',
                        'chaldean' => '☦️ Rito Caldeo',
                        'syriac' => '☦️ Rito Siriaco',
                        'maronite' => '☦️ Rito Maronita',
                        'melkite' => '☦️ Rito Melquita',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('language')
                    ->label('Idioma')
                    ->colors([
                        'success' => 'es',
                        'primary' => 'la',
                        'info' => 'en',
                        'warning' => 'fr',
                        'danger' => 'de',
                        'secondary' => 'it',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'es' => '🇪🇸 Español',
                        'la' => '🇻🇦 Latín',
                        'en' => '🇺🇸 Inglés',
                        'fr' => '🇫🇷 Francés',
                        'de' => '🇩🇪 Alemán',
                        'it' => '🇮🇹 Italiano',
                        'pt' => '🇵🇹 Portugués',
                        'ca' => '🇪🇸 Catalán',
                        'eu' => '🇪🇸 Euskera',
                        'gl' => '🇪🇸 Gallego',
                        'ar' => '🇸🇾 Árabe',
                        'el' => '🇬🇷 Griego',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('region')
                    ->label('Región')
                    ->colors([
                        'success' => 'spain',
                        'warning' => 'italy',
                        'info' => 'france',
                        'danger' => 'germany',
                        'primary' => 'poland',
                        'secondary' => 'ireland',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'spain' => '🇪🇸 España',
                        'italy' => '🇮🇹 Italia',
                        'france' => '🇫🇷 Francia',
                        'germany' => '🇩🇪 Alemania',
                        'poland' => '🇵🇱 Polonia',
                        'ireland' => '🇮🇪 Irlanda',
                        'portugal' => '🇵🇹 Portugal',
                        'mexico' => '🇲🇽 México',
                        'brazil' => '🇧🇷 Brasil',
                        'argentina' => '🇦🇷 Argentina',
                        'colombia' => '🇨🇴 Colombia',
                        'peru' => '🇵🇪 Perú',
                        'chile' => '🇨🇱 Chile',
                        'venezuela' => '🇻🇪 Venezuela',
                        'ecuador' => '🇪🇨 Ecuador',
                        'bolivia' => '🇧🇴 Bolivia',
                        'paraguay' => '🇵🇾 Paraguay',
                        'uruguay' => '🇺🇾 Uruguay',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('liturgical_year')
                    ->label('Año')
                    ->searchable()
                    ->sortable()
                    ->color('primary'),
                
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
                        ($record->end_date && $record->end_date->diffInDays(now()) <= 30 ? 'warning' : 'success')
                    ),
                
                Tables\Columns\TextColumn::make('diocese')
                    ->label('Diócesis')
                    ->limit(25)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('parish')
                    ->label('Parroquia')
                    ->limit(25)
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('includes_advent')
                    ->label('Adviento')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('includes_christmas')
                    ->label('Navidad')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('includes_lent')
                    ->label('Cuaresma')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('includes_easter')
                    ->label('Pascua')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('uses_white')
                    ->label('Blanco')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('uses_red')
                    ->label('Rojo')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('uses_green')
                    ->label('Verde')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('uses_purple')
                    ->label('Morado')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'danger' => 'inactive',
                        'info' => 'under_review',
                        'dark' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'under_review' => '👀 En Revisión',
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
                Tables\Filters\SelectFilter::make('calendar_type')
                    ->options([
                        'roman_catholic' => '⛪ Católico Romano',
                        'eastern_orthodox' => '☦️ Ortodoxo Oriental',
                        'anglican' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿 Anglicano',
                        'lutheran' => '✝️ Luterano',
                        'methodist' => '⛪ Metodista',
                        'baptist' => '🕊️ Bautista',
                        'presbyterian' => '⛪ Presbiteriano',
                        'coptic' => '☦️ Copto',
                        'syriac' => '☦️ Siriaco',
                        'armenian' => '☦️ Armenio',
                        'ethiopian' => '☦️ Etíope',
                        'maronite' => '☦️ Maronita',
                        'melkite' => '☦️ Melquita',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Calendario'),
                
                Tables\Filters\SelectFilter::make('rite')
                    ->options([
                        'latin' => '🇻🇦 Rito Latino',
                        'byzantine' => '☦️ Rito Bizantino',
                        'alexandrian' => '☦️ Rito Alejandrino',
                        'antiochian' => '☦️ Rito Antioqueno',
                        'armenian' => '☦️ Rito Armenio',
                        'chaldean' => '☦️ Rito Caldeo',
                        'syriac' => '☦️ Rito Siriaco',
                        'maronite' => '☦️ Rito Maronita',
                        'melkite' => '☦️ Rito Melquita',
                        'other' => '❓ Otro',
                    ])
                    ->label('Rito'),
                
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'es' => '🇪🇸 Español',
                        'la' => '🇻🇦 Latín',
                        'en' => '🇺🇸 Inglés',
                        'fr' => '🇫🇷 Francés',
                        'de' => '🇩🇪 Alemán',
                        'it' => '🇮🇹 Italiano',
                        'pt' => '🇵🇹 Portugués',
                        'ca' => '🇪🇸 Catalán',
                        'eu' => '🇪🇸 Euskera',
                        'gl' => '🇪🇸 Gallego',
                        'ar' => '🇸🇾 Árabe',
                        'el' => '🇬🇷 Griego',
                        'other' => '❓ Otro',
                    ])
                    ->label('Idioma'),
                
                Tables\Filters\SelectFilter::make('region')
                    ->options([
                        'spain' => '🇪🇸 España',
                        'italy' => '🇮🇹 Italia',
                        'france' => '🇫🇷 Francia',
                        'germany' => '🇩🇪 Alemania',
                        'poland' => '🇵🇱 Polonia',
                        'ireland' => '🇮🇪 Irlanda',
                        'portugal' => '🇵🇹 Portugal',
                        'mexico' => '🇲🇽 México',
                        'brazil' => '🇧🇷 Brasil',
                        'argentina' => '🇦🇷 Argentina',
                        'colombia' => '🇨🇴 Colombia',
                        'peru' => '🇵🇪 Perú',
                        'chile' => '🇨🇱 Chile',
                        'venezuela' => '🇻🇪 Venezuela',
                        'ecuador' => '🇪🇨 Ecuador',
                        'bolivia' => '🇧🇴 Bolivia',
                        'paraguay' => '🇵🇾 Paraguay',
                        'uruguay' => '🇺🇾 Uruguay',
                        'other' => '❓ Otro',
                    ])
                    ->label('Región'),
                
                Tables\Filters\SelectFilter::make('liturgical_year')
                    ->options([
                        '2024' => '2024',
                        '2025' => '2025',
                        '2026' => '2026',
                        '2027' => '2027',
                        '2028' => '2028',
                        '2029' => '2029',
                        '2030' => '2030',
                        'other' => 'Otro',
                    ])
                    ->label('Año Litúrgico'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activo',
                        'inactive' => '❌ Inactivo',
                        'under_review' => '👀 En Revisión',
                        'archived' => '📦 Archivado',
                        'deprecated' => '⚠️ Deprecado',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificados')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('approved_only')
                    ->label('Solo Aprobados')
                    ->query(fn (Builder $query): Builder => $query->where('is_approved', true)),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('current_year')
                    ->label('Año Actual')
                    ->query(fn (Builder $query): Builder => $query->where('liturgical_year', date('Y'))),
                
                Tables\Filters\Filter::make('spanish_region')
                    ->label('Región Española')
                    ->query(fn (Builder $query): Builder => $query->where('region', 'spain')),
                
                Tables\Filters\Filter::make('latin_rite')
                    ->label('Rito Latino')
                    ->query(fn (Builder $query): Builder => $query->where('rite', 'latin')),
                
                Tables\Filters\Filter::make('includes_all_seasons')
                    ->label('Todas las Temporadas')
                    ->query(fn (Builder $query): Builder => $query->where('includes_advent', true)
                        ->where('includes_christmas', true)
                        ->where('includes_lent', true)
                        ->where('includes_easter', true)),
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
                
                Tables\Actions\Action::make('approve_calendar')
                    ->label('Aprobar')
                    ->icon('fas-check')
                    ->action(function ($record): void {
                        $record->update(['is_approved' => true, 'status' => 'active']);
                    })
                    ->visible(fn ($record): bool => !$record->is_approved)
                    ->color('success'),
                
                Tables\Actions\Action::make('activate_calendar')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_calendar')
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
            ->defaultSort('liturgical_year', 'desc')
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
            'index' => Pages\ListLiturgicalCalendars::route('/'),
            'create' => Pages\CreateLiturgicalCalendar::route('/create'),
            'view' => Pages\ViewLiturgicalCalendar::route('/{record}'),
            'edit' => Pages\EditLiturgicalCalendar::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
