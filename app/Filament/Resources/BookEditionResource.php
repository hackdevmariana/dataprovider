<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookEditionResource\Pages;
use App\Models\BookEdition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookEditionResource extends Resource
{
    protected static ?string $model = BookEdition::class;

    protected static ?string $navigationIcon = 'fas-book-open';

    protected static ?string $navigationGroup = 'Contenido y Medios';

    protected static ?string $navigationLabel = 'Ediciones de Libros';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Edición de Libro';

    protected static ?string $pluralModelLabel = 'Ediciones de Libros';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('edition_title')
                            ->required()
                            ->maxLength(255)
                            ->label('Título de la Edición')
                            ->placeholder('Título específico de esta edición...'),
                        
                        Forms\Components\TextInput::make('edition_code')
                            ->maxLength(100)
                            ->label('Código de Edición')
                            ->placeholder('Código único identificador...'),
                        
                        Forms\Components\Select::make('book_id')
                            ->relationship('book', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Libro')
                            ->placeholder('Selecciona el libro...'),
                        
                        Forms\Components\Select::make('edition_type')
                            ->options([
                                'first' => '🥇 Primera',
                                'second' => '🥈 Segunda',
                                'third' => '🥉 Tercera',
                                'fourth' => '4️⃣ Cuarta',
                                'fifth' => '5️⃣ Quinta',
                                'sixth' => '6️⃣ Sexta',
                                'seventh' => '7️⃣ Séptima',
                                'eighth' => '8️⃣ Octava',
                                'ninth' => '9️⃣ Novena',
                                'tenth' => '🔟 Décima',
                                'revised' => '✏️ Revisada',
                                'expanded' => '📈 Ampliada',
                                'abridged' => '✂️ Resumida',
                                'annotated' => '📝 Anotada',
                                'illustrated' => '🎨 Ilustrada',
                                'special' => '⭐ Especial',
                                'limited' => '🔒 Limitada',
                                'collector' => '💎 Coleccionista',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Edición'),
                        
                        Forms\Components\TextInput::make('edition_number')
                            ->numeric()
                            ->label('Número de Edición')
                            ->placeholder('Número ordinal de la edición...'),
                        
                        Forms\Components\TextInput::make('isbn')
                            ->maxLength(20)
                            ->label('ISBN')
                            ->placeholder('ISBN de la edición...'),
                        
                        Forms\Components\TextInput::make('isbn_13')
                            ->maxLength(20)
                            ->label('ISBN-13')
                            ->placeholder('ISBN-13 de la edición...'),
                        
                        Forms\Components\TextInput::make('isbn_10')
                            ->maxLength(20)
                            ->label('ISBN-10')
                            ->placeholder('ISBN-10 de la edición...'),
                        
                        Forms\Components\TextInput::make('barcode')
                            ->maxLength(50)
                            ->label('Código de Barras')
                            ->placeholder('Código de barras...'),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles de Publicación')
                    ->schema([
                        Forms\Components\TextInput::make('publisher_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Editorial')
                            ->placeholder('Nombre de la editorial...'),
                        
                        Forms\Components\TextInput::make('publisher_location')
                            ->maxLength(255)
                            ->label('Ubicación de la Editorial')
                            ->placeholder('Ciudad, país de la editorial...'),
                        
                        Forms\Components\DatePicker::make('publication_date')
                            ->required()
                            ->label('Fecha de Publicación')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\TextInput::make('publication_year')
                            ->numeric()
                            ->label('Año de Publicación')
                            ->placeholder('Año de publicación...')
                            ->minValue(1000)
                            ->maxValue(2100),
                        
                        Forms\Components\TextInput::make('print_run')
                            ->numeric()
                            ->label('Tirada de Impresión')
                            ->placeholder('Número de ejemplares impresos...'),
                        
                        Forms\Components\Select::make('print_run_type')
                            ->options([
                                'limited' => '🔒 Limitada',
                                'standard' => '📊 Estándar',
                                'mass_market' => '🏪 Mercado Masivo',
                                'trade' => '💼 Comercial',
                                'academic' => '🎓 Académica',
                                'other' => '❓ Otro',
                            ])
                            ->label('Tipo de Tirada'),
                        
                        Forms\Components\TextInput::make('copyright_year')
                            ->numeric()
                            ->label('Año de Copyright')
                            ->placeholder('Año de copyright...'),
                        
                        Forms\Components\TextInput::make('copyright_holder')
                            ->maxLength(255)
                            ->label('Titular de Copyright')
                            ->placeholder('Titular de los derechos...'),
                        
                        Forms\Components\TextInput::make('legal_deposit')
                            ->maxLength(100)
                            ->label('Depósito Legal')
                            ->placeholder('Número de depósito legal...'),
                    ])->columns(2),

                Forms\Components\Section::make('Características Físicas')
                    ->schema([
                        Forms\Components\Select::make('format')
                            ->options([
                                'hardcover' => '📚 Tapa Dura',
                                'paperback' => '📖 Tapa Blanda',
                                'mass_market_paperback' => '📗 Tapa Blanda de Bolsillo',
                                'trade_paperback' => '📘 Tapa Blanda Comercial',
                                'spiral_bound' => '📎 Espiral',
                                'ring_bound' => '📎 Anillas',
                                'leather_bound' => '🐄 Piel',
                                'cloth_bound' => '🧵 Tela',
                                'board_book' => '📋 Cartón',
                                'ebook' => '💻 E-book',
                                'audiobook' => '🎧 Audio Libro',
                                'pdf' => '📄 PDF',
                                'epub' => '📱 EPUB',
                                'mobi' => '📱 MOBI',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Formato'),
                        
                        Forms\Components\TextInput::make('pages')
                            ->numeric()
                            ->label('Páginas')
                            ->placeholder('Número de páginas...'),
                        
                        Forms\Components\TextInput::make('height_cm')
                            ->numeric()
                            ->label('Alto (cm)')
                            ->placeholder('Altura en centímetros...'),
                        
                        Forms\Components\TextInput::make('width_cm')
                            ->numeric()
                            ->label('Ancho (cm)')
                            ->placeholder('Ancho en centímetros...'),
                        
                        Forms\Components\TextInput::make('thickness_cm')
                            ->numeric()
                            ->label('Grosor (cm)')
                            ->placeholder('Grosor en centímetros...'),
                        
                        Forms\Components\TextInput::make('weight_grams')
                            ->numeric()
                            ->label('Peso (gramos)')
                            ->placeholder('Peso en gramos...'),
                        
                        Forms\Components\Select::make('binding_type')
                            ->options([
                                'perfect' => '📏 Perfecto',
                                'saddle' => '🐎 Silla de Montar',
                                'spiral' => '📎 Espiral',
                                'ring' => '📎 Anillas',
                                'staples' => '📎 Grapas',
                                'sewn' => '🧵 Cosido',
                                'glued' => '🔗 Pegado',
                                'other' => '❓ Otro',
                            ])
                            ->label('Tipo de Encuadernación'),
                        
                        Forms\Components\Toggle::make('has_dust_jacket')
                            ->label('Tiene Sobrecubierta')
                            ->default(false)
                            ->helperText('La edición incluye sobrecubierta'),
                        
                        Forms\Components\Toggle::make('has_bookmark')
                            ->label('Tiene Marcador')
                            ->default(false)
                            ->helperText('La edición incluye marcador'),
                        
                        Forms\Components\Toggle::make('has_case')
                            ->label('Tiene Estuche')
                            ->default(false)
                            ->helperText('La edición incluye estuche'),
                    ])->columns(2),

                Forms\Components\Section::make('Contenido y Características')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción de esta edición...'),
                        
                        Forms\Components\Textarea::make('changes_from_previous')
                            ->maxLength(500)
                            ->label('Cambios Respecto a la Anterior')
                            ->rows(2)
                            ->placeholder('Cambios en esta edición...'),
                        
                        Forms\Components\Toggle::make('is_illustrated')
                            ->label('Está Ilustrado')
                            ->default(false)
                            ->helperText('La edición incluye ilustraciones'),
                        
                        Forms\Components\TextInput::make('illustration_count')
                            ->numeric()
                            ->label('Número de Ilustraciones')
                            ->placeholder('Número de ilustraciones...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_illustrated')),
                        
                        Forms\Components\Toggle::make('is_annotated')
                            ->label('Está Anotado')
                            ->default(false)
                            ->helperText('La edición incluye anotaciones'),
                        
                        Forms\Components\Toggle::make('has_index')
                            ->label('Tiene Índice')
                            ->default(false)
                            ->helperText('La edición incluye índice'),
                        
                        Forms\Components\Toggle::make('has_bibliography')
                            ->label('Tiene Bibliografía')
                            ->default(false)
                            ->helperText('La edición incluye bibliografía'),
                        
                        Forms\Components\Toggle::make('has_glossary')
                            ->label('Tiene Glosario')
                            ->default(false)
                            ->helperText('La edición incluye glosario'),
                        
                        Forms\Components\Toggle::make('has_appendix')
                            ->label('Tiene Apéndices')
                            ->default(false)
                            ->helperText('La edición incluye apéndices'),
                        
                        Forms\Components\Textarea::make('special_features')
                            ->maxLength(500)
                            ->label('Características Especiales')
                            ->rows(2)
                            ->placeholder('Características especiales...'),
                        
                        Forms\Components\Textarea::make('table_of_contents')
                            ->maxLength(1000)
                            ->label('Índice de Contenidos')
                            ->rows(3)
                            ->placeholder('Índice de contenidos...'),
                    ])->columns(2),

                Forms\Components\Section::make('Idioma y Traducción')
                    ->schema([
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
                                'la' => '🏛️ Latín',
                                'gr' => '🇬🇷 Griego',
                                'ar' => '🇸🇦 Árabe',
                                'zh' => '🇨🇳 Chino',
                                'ja' => '🇯🇵 Japonés',
                                'ko' => '🇰🇷 Coreano',
                                'ru' => '🇷🇺 Ruso',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('es')
                            ->label('Idioma'),
                        
                        Forms\Components\Toggle::make('is_translation')
                            ->label('Es Traducción')
                            ->default(false)
                            ->helperText('Esta edición es una traducción'),
                        
                        Forms\Components\TextInput::make('original_language')
                            ->maxLength(10)
                            ->label('Idioma Original')
                            ->placeholder('Idioma original si es traducción...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_translation')),
                        
                        Forms\Components\TextInput::make('translator_name')
                            ->maxLength(255)
                            ->label('Nombre del Traductor')
                            ->placeholder('Nombre del traductor...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_translation')),
                        
                        Forms\Components\TextInput::make('translation_year')
                            ->numeric()
                            ->label('Año de Traducción')
                            ->placeholder('Año de la traducción...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_translation')),
                        
                        Forms\Components\Toggle::make('is_bilingual')
                            ->label('Es Bilingüe')
                            ->default(false)
                            ->helperText('La edición es bilingüe'),
                        
                        Forms\Components\TextInput::make('second_language')
                            ->maxLength(10)
                            ->label('Segundo Idioma')
                            ->placeholder('Segundo idioma si es bilingüe...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_bilingual')),
                    ])->columns(2),

                Forms\Components\Section::make('Precio y Disponibilidad')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->label('Precio')
                            ->placeholder('Precio de la edición...'),
                        
                        Forms\Components\Select::make('currency')
                            ->options([
                                'EUR' => '€ EUR',
                                'USD' => '$ USD',
                                'GBP' => '£ GBP',
                                'JPY' => '¥ JPY',
                                'CHF' => 'CHF',
                                'CAD' => 'C$ CAD',
                                'AUD' => 'A$ AUD',
                                'other' => 'Otro',
                            ])
                            ->default('EUR')
                            ->label('Moneda'),
                        
                        Forms\Components\Select::make('availability_status')
                            ->options([
                                'available' => '✅ Disponible',
                                'limited' => '⚠️ Limitada',
                                'out_of_stock' => '❌ Agotada',
                                'pre_order' => '📅 Pre-orden',
                                'discontinued' => '🛑 Discontinuada',
                                'rare' => '💎 Rara',
                                'collector' => '💎 Coleccionista',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('available')
                            ->label('Estado de Disponibilidad'),
                        
                        Forms\Components\Toggle::make('is_limited_edition')
                            ->label('Es Edición Limitada')
                            ->default(false)
                            ->helperText('Edición de tirada limitada'),
                        
                        Forms\Components\TextInput::make('limited_edition_number')
                            ->numeric()
                            ->label('Número de Edición Limitada')
                            ->placeholder('Número de esta edición limitada...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_limited_edition')),
                        
                        Forms\Components\TextInput::make('total_limited_copies')
                            ->numeric()
                            ->label('Total de Copias Limitadas')
                            ->placeholder('Total de copias de la edición limitada...')
                            ->visible(fn (Forms\Get $get): bool => $get('is_limited_edition')),
                        
                        Forms\Components\Toggle::make('is_signed')
                            ->label('Está Firmado')
                            ->default(false)
                            ->helperText('La edición está firmada por el autor'),
                        
                        Forms\Components\Toggle::make('is_numbered')
                            ->label('Está Numerado')
                            ->default(false)
                            ->helperText('La edición está numerada'),
                        
                        Forms\Components\Toggle::make('is_premium')
                            ->label('Es Premium')
                            ->default(false)
                            ->helperText('Edición de alta calidad'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Calidad')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '✅ Activa',
                                'inactive' => '❌ Inactiva',
                                'out_of_print' => '🖨️ Agotada',
                                'discontinued' => '🛑 Discontinuada',
                                'rare' => '💎 Rara',
                                'collector' => '💎 Coleccionista',
                                'archived' => '📦 Archivada',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('active')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada')
                            ->default(false)
                            ->helperText('Edición importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular')
                            ->default(false)
                            ->helperText('Edición popular entre los lectores'),
                        
                        Forms\Components\Toggle::make('is_new')
                            ->label('Nueva')
                            ->default(false)
                            ->helperText('Edición recién publicada'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('La edición ha sido verificada'),
                        
                        Forms\Components\Select::make('condition_rating')
                            ->options([
                                'mint' => '🟢 Mint (Perfecto)',
                                'near_mint' => '🟢 Near Mint (Casi Perfecto)',
                                'excellent' => '🟢 Excelente',
                                'very_good' => '🟢 Muy Bueno',
                                'good' => '🟡 Bueno',
                                'fair' => '🟠 Regular',
                                'poor' => '🔴 Pobre',
                                'other' => '❓ Otro',
                            ])
                            ->label('Estado de Conservación'),
                        
                        Forms\Components\TextInput::make('reviewer')
                            ->maxLength(255)
                            ->label('Revisor')
                            ->placeholder('Persona que revisó la edición...'),
                        
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
                
                Tables\Columns\TextColumn::make('edition_title')
                    ->label('Edición')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Libro')
                    ->searchable()
                    ->limit(30)
                    ->weight('medium'),
                
                Tables\Columns\BadgeColumn::make('edition_type')
                    ->label('Tipo')
                    ->colors([
                        'warning' => 'first',
                        'success' => 'second',
                        'info' => 'third',
                        'primary' => 'revised',
                        'danger' => 'expanded',
                        'secondary' => 'special',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'first' => '🥇 Primera',
                        'second' => '🥈 Segunda',
                        'third' => '🥉 Tercera',
                        'fourth' => '4️⃣ Cuarta',
                        'fifth' => '5️⃣ Quinta',
                        'sixth' => '6️⃣ Sexta',
                        'seventh' => '7️⃣ Séptima',
                        'eighth' => '8️⃣ Octava',
                        'ninth' => '9️⃣ Novena',
                        'tenth' => '🔟 Décima',
                        'revised' => '✏️ Revisada',
                        'expanded' => '📈 Ampliada',
                        'abridged' => '✂️ Resumida',
                        'annotated' => '📝 Anotada',
                        'illustrated' => '🎨 Ilustrada',
                        'special' => '⭐ Especial',
                        'limited' => '🔒 Limitada',
                        'collector' => '💎 Coleccionista',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('edition_number')
                    ->label('Número')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('publisher_name')
                    ->label('Editorial')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('publication_year')
                    ->label('Año')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 2020 => 'success',
                        $state >= 2010 => 'info',
                        $state >= 2000 => 'warning',
                        $state >= 1990 => 'secondary',
                        $state >= 1980 => 'danger',
                        default => 'primary',
                    }),
                
                Tables\Columns\BadgeColumn::make('format')
                    ->label('Formato')
                    ->colors([
                        'primary' => 'hardcover',
                        'success' => 'paperback',
                        'warning' => 'ebook',
                        'info' => 'audiobook',
                        'danger' => 'pdf',
                        'secondary' => 'other',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hardcover' => '📚 Tapa Dura',
                        'paperback' => '📖 Tapa Blanda',
                        'mass_market_paperback' => '📗 Tapa Blanda de Bolsillo',
                        'trade_paperback' => '📘 Tapa Blanda Comercial',
                        'spiral_bound' => '📎 Espiral',
                        'ring_bound' => '📎 Anillas',
                        'leather_bound' => '🐄 Piel',
                        'cloth_bound' => '🧵 Tela',
                        'board_book' => '📋 Cartón',
                        'ebook' => '💻 E-book',
                        'audiobook' => '🎧 Audio Libro',
                        'pdf' => '📄 PDF',
                        'epub' => '📱 EPUB',
                        'mobi' => '📱 MOBI',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('pages')
                    ->label('Páginas')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 100 => 'success',
                        $state <= 300 => 'info',
                        $state <= 500 => 'warning',
                        $state <= 800 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->prefix('€')
                    ->color(fn (float $state): string => match (true) {
                        $state <= 10 => 'success',
                        $state <= 25 => 'info',
                        $state <= 50 => 'warning',
                        $state <= 100 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\BadgeColumn::make('availability_status')
                    ->label('Disponibilidad')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'limited',
                        'danger' => 'out_of_stock',
                        'info' => 'pre_order',
                        'secondary' => 'discontinued',
                        'primary' => 'rare',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => '✅ Disponible',
                        'limited' => '⚠️ Limitada',
                        'out_of_stock' => '❌ Agotada',
                        'pre_order' => '📅 Pre-orden',
                        'discontinued' => '🛑 Discontinuada',
                        'rare' => '💎 Rara',
                        'collector' => '💎 Coleccionista',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_limited_edition')
                    ->label('Limitada')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_signed')
                    ->label('Firmado')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_illustrated')
                    ->label('Ilustrado')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
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
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'out_of_print',
                        'secondary' => 'discontinued',
                        'primary' => 'rare',
                        'info' => 'collector',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'out_of_print' => '🖨️ Agotada',
                        'discontinued' => '🛑 Discontinuada',
                        'rare' => '💎 Rara',
                        'collector' => '💎 Coleccionista',
                        'archived' => '📦 Archivada',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('edition_type')
                    ->options([
                        'first' => '🥇 Primera',
                        'second' => '🥈 Segunda',
                        'third' => '🥉 Tercera',
                        'fourth' => '4️⃣ Cuarta',
                        'fifth' => '5️⃣ Quinta',
                        'sixth' => '6️⃣ Sexta',
                        'seventh' => '7️⃣ Séptima',
                        'eighth' => '8️⃣ Octava',
                        'ninth' => '9️⃣ Novena',
                        'tenth' => '🔟 Décima',
                        'revised' => '✏️ Revisada',
                        'expanded' => '📈 Ampliada',
                        'abridged' => '✂️ Resumida',
                        'annotated' => '📝 Anotada',
                        'illustrated' => '🎨 Ilustrada',
                        'special' => '⭐ Especial',
                        'limited' => '🔒 Limitada',
                        'collector' => '💎 Coleccionista',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Edición'),
                
                Tables\Filters\SelectFilter::make('format')
                    ->options([
                        'hardcover' => '📚 Tapa Dura',
                        'paperback' => '📖 Tapa Blanda',
                        'mass_market_paperback' => '📗 Tapa Blanda de Bolsillo',
                        'trade_paperback' => '📘 Tapa Blanda Comercial',
                        'spiral_bound' => '📎 Espiral',
                        'ring_bound' => '📎 Anillas',
                        'leather_bound' => '🐄 Piel',
                        'cloth_bound' => '🧵 Tela',
                        'board_book' => '📋 Cartón',
                        'ebook' => '💻 E-book',
                        'audiobook' => '🎧 Audio Libro',
                        'pdf' => '📄 PDF',
                        'epub' => '📱 EPUB',
                        'mobi' => '📱 MOBI',
                        'other' => '❓ Otro',
                    ])
                    ->label('Formato'),
                
                Tables\Filters\SelectFilter::make('language')
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
                        'la' => '🏛️ Latín',
                        'gr' => '🇬🇷 Griego',
                        'ar' => '🇸🇦 Árabe',
                        'zh' => '🇨🇳 Chino',
                        'ja' => '🇯🇵 Japonés',
                        'ko' => '🇰🇷 Coreano',
                        'ru' => '🇷🇺 Ruso',
                        'other' => '❓ Otro',
                    ])
                    ->label('Idioma'),
                
                Tables\Filters\SelectFilter::make('availability_status')
                    ->options([
                        'available' => '✅ Disponible',
                        'limited' => '⚠️ Limitada',
                        'out_of_stock' => '❌ Agotada',
                        'pre_order' => '📅 Pre-orden',
                        'discontinued' => '🛑 Discontinuada',
                        'rare' => '💎 Rara',
                        'collector' => '💎 Coleccionista',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado de Disponibilidad'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '✅ Activa',
                        'inactive' => '❌ Inactiva',
                        'out_of_print' => '🖨️ Agotada',
                        'discontinued' => '🛑 Discontinuada',
                        'rare' => '💎 Rara',
                        'collector' => '💎 Coleccionista',
                        'archived' => '📦 Archivada',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('popular_only')
                    ->label('Solo Populares')
                    ->query(fn (Builder $query): Builder => $query->where('is_popular', true)),
                
                Tables\Filters\Filter::make('limited_editions')
                    ->label('Solo Ediciones Limitadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_limited_edition', true)),
                
                Tables\Filters\Filter::make('signed_editions')
                    ->label('Solo Firmadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_signed', true)),
                
                Tables\Filters\Filter::make('illustrated_editions')
                    ->label('Solo Ilustradas')
                    ->query(fn (Builder $query): Builder => $query->where('is_illustrated', true)),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                
                Tables\Filters\Filter::make('available_only')
                    ->label('Solo Disponibles')
                    ->query(fn (Builder $query): Builder => $query->where('availability_status', 'available')),
                
                Tables\Filters\Filter::make('recent_publications')
                    ->label('Publicaciones Recientes (2020+)')
                    ->query(fn (Builder $query): Builder => $query->where('publication_year', '>=', 2020)),
                
                Tables\Filters\Filter::make('classic_editions')
                    ->label('Ediciones Clásicas (Pre-2000)')
                    ->query(fn (Builder $query): Builder => $query->where('publication_year', '<', 2000)),
                
                Tables\Filters\Filter::make('high_page_count')
                    ->label('Muchas Páginas (500+)')
                    ->query(fn (Builder $query): Builder => $query->where('pages', '>=', 500)),
                
                Tables\Filters\Filter::make('low_price')
                    ->label('Precio Bajo (≤15€)')
                    ->query(fn (Builder $query): Builder => $query->where('price', '<=', 15)),
                
                Tables\Filters\Filter::make('premium_price')
                    ->label('Precio Premium (≥50€)')
                    ->query(fn (Builder $query): Builder => $query->where('price', '>=', 50)),
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
                    ->icon(fn ($record): string => $record->is_popular ? 'fas-heart' : 'far-heart')
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
                
                Tables\Actions\Action::make('activate_edition')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('deactivate_edition')
                    ->label('Desactivar')
                    ->icon('fas-pause')
                    ->action(function ($record): void {
                        $record->update(['status' => 'inactive']);
                    })
                    ->visible(fn ($record): bool => $record->status === 'active')
                    ->color('warning'),
                
                Tables\Actions\Action::make('view_book')
                    ->label('Ver Libro')
                    ->icon('fas-book')
                    ->action(function ($record): void {
                        // Aquí se implementaría la navegación al libro
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
                        ->icon('fas-heart')
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
            'index' => Pages\ListBookEditions::route('/'),
            'create' => Pages\CreateBookEdition::route('/create'),
            'view' => Pages\ViewBookEdition::route('/{record}'),
            'edit' => Pages\EditBookEdition::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
