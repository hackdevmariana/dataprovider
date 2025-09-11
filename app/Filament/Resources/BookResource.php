<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'fas-book';

    protected static ?string $navigationGroup = 'Biblioteca y Literatura';

    protected static ?string $navigationLabel = 'Libros';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Libro';

    protected static ?string $pluralModelLabel = 'Libros';

    
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
                            ->maxLength(500)
                            ->label('Título del Libro')
                            ->placeholder('Título completo del libro...'),
                        
                        Forms\Components\TextInput::make('subtitle')
                            ->maxLength(500)
                            ->label('Subtítulo')
                            ->placeholder('Subtítulo o descripción breve...'),
                        
                        Forms\Components\TextInput::make('original_title')
                            ->maxLength(500)
                            ->label('Título Original')
                            ->placeholder('Título en el idioma original...'),
                        
                        Forms\Components\TextInput::make('isbn')
                            ->maxLength(20)
                            ->label('ISBN')
                            ->placeholder('978-84-8181-227-5')
                            ->helperText('Código ISBN-10 o ISBN-13'),
                        
                        Forms\Components\TextInput::make('isbn13')
                            ->maxLength(20)
                            ->label('ISBN-13')
                            ->placeholder('9788481812275')
                            ->helperText('Código ISBN-13 sin guiones'),
                    ])->columns(2),

                Forms\Components\Section::make('Autoría y Editorial')
                    ->schema([
                        Forms\Components\TextInput::make('author')
                            ->required()
                            ->maxLength(255)
                            ->label('Autor Principal')
                            ->placeholder('Nombre completo del autor...'),
                        
                        Forms\Components\TextInput::make('co_authors')
                            ->maxLength(500)
                            ->label('Co-autores')
                            ->placeholder('Nombres separados por comas...'),
                        
                        Forms\Components\TextInput::make('editor')
                            ->maxLength(255)
                            ->label('Editor')
                            ->placeholder('Nombre del editor o compilador...'),
                        
                        Forms\Components\TextInput::make('publisher')
                            ->maxLength(255)
                            ->label('Editorial')
                            ->placeholder('Nombre de la editorial...'),
                        
                        Forms\Components\TextInput::make('publisher_location')
                            ->maxLength(255)
                            ->label('Ubicación de la Editorial')
                            ->placeholder('Ciudad, País...'),
                    ])->columns(2),

                Forms\Components\Section::make('Clasificación y Género')
                    ->schema([
                        Forms\Components\Select::make('genre')
                            ->options([
                                'fiction' => '📚 Ficción',
                                'non_fiction' => '📖 No Ficción',
                                'mystery' => '🔍 Misterio',
                                'romance' => '💕 Romance',
                                'science_fiction' => '🚀 Ciencia Ficción',
                                'fantasy' => '🐉 Fantasía',
                                'thriller' => '😱 Thriller',
                                'horror' => '👻 Terror',
                                'biography' => '👤 Biografía',
                                'history' => '🏛️ Historia',
                                'philosophy' => '🤔 Filosofía',
                                'religion' => '⛪ Religión',
                                'science' => '🔬 Ciencia',
                                'technology' => '💻 Tecnología',
                                'business' => '💼 Negocios',
                                'self_help' => '💪 Autoayuda',
                                'cookbook' => '👨‍🍳 Cocina',
                                'travel' => '✈️ Viajes',
                                'poetry' => '📝 Poesía',
                                'drama' => '🎭 Teatro',
                                'children' => '👶 Infantil',
                                'young_adult' => '👧👦 Juvenil',
                                'academic' => '🎓 Académico',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Género Principal'),
                        
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->separator(',')
                            ->placeholder('Agregar etiquetas...'),
                        
                        Forms\Components\Select::make('target_audience')
                            ->options([
                                'children' => '👶 Niños (0-12 años)',
                                'young_adult' => '👧👦 Jóvenes (13-18 años)',
                                'adult' => '👨👩 Adultos (18+ años)',
                                'academic' => '🎓 Académico',
                                'general' => '🌍 Público General',
                                'professional' => '💼 Profesional',
                                'senior' => '👴👵 Adultos Mayores',
                            ])
                            ->label('Audiencia Objetivo'),
                        
                        Forms\Components\Select::make('reading_level')
                            ->options([
                                'beginner' => '🌱 Principiante',
                                'intermediate' => '🌿 Intermedio',
                                'advanced' => '🌳 Avanzado',
                                'expert' => '🏔️ Experto',
                                'academic' => '🎓 Académico',
                            ])
                            ->label('Nivel de Lectura'),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles de Publicación')
                    ->schema([
                        Forms\Components\DatePicker::make('publication_date')
                            ->label('Fecha de Publicación')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\TextInput::make('edition')
                            ->maxLength(50)
                            ->label('Edición')
                            ->placeholder('1ª, 2ª, Revisada...'),
                        
                        Forms\Components\TextInput::make('pages')
                            ->numeric()
                            ->label('Número de Páginas')
                            ->minValue(1),
                        
                        Forms\Components\Select::make('format')
                            ->options([
                                'hardcover' => '📘 Tapa Dura',
                                'paperback' => '📗 Tapa Blanda',
                                'ebook' => '📱 E-book',
                                'audiobook' => '🎧 Audiolibro',
                                'pdf' => '📄 PDF',
                                'epub' => '📱 EPUB',
                                'mobi' => '📱 MOBI',
                                'other' => '❓ Otro',
                            ])
                            ->label('Formato'),
                        
                        Forms\Components\TextInput::make('dimensions')
                            ->maxLength(100)
                            ->label('Dimensiones')
                            ->placeholder('15 x 23 cm'),
                        
                        Forms\Components\TextInput::make('weight')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('kg')
                            ->label('Peso'),
                    ])->columns(2),

                Forms\Components\Section::make('Idioma y Traducción')
                    ->schema([
                        Forms\Components\Select::make('language')
                            ->options([
                                'es' => '🇪🇸 Español',
                                'en' => '🇬🇧 Inglés',
                                'fr' => '🇫🇷 Francés',
                                'de' => '🇩🇪 Alemán',
                                'it' => '🇮🇹 Italiano',
                                'pt' => '🇵🇹 Portugués',
                                'ca' => '🏴󠁥󠁳󠁣󠁴󠁿 Catalán',
                                'eu' => '🏴󠁥󠁳󠁰󠁶󠁿 Euskera',
                                'gl' => '🏴󠁥󠁳󠁧󠁡󠁿 Gallego',
                                'la' => '🏛️ Latín',
                                'gr' => '🇬🇷 Griego',
                                'ar' => '🇸🇦 Árabe',
                                'zh' => '🇨🇳 Chino',
                                'ja' => '🇯🇵 Japonés',
                                'ko' => '🇰🇷 Coreano',
                                'ru' => '🇷🇺 Ruso',
                            ])
                            ->required()
                            ->default('es')
                            ->label('Idioma Principal'),
                        
                        Forms\Components\Select::make('original_language')
                            ->options([
                                'es' => '🇪🇸 Español',
                                'en' => '🇬🇧 Inglés',
                                'fr' => '🇫🇷 Francés',
                                'de' => '🇩🇪 Alemán',
                                'it' => '🇮🇹 Italiano',
                                'pt' => '🇵🇹 Portugués',
                                'la' => '🏛️ Latín',
                                'gr' => '🇬🇷 Griego',
                                'ar' => '🇸🇦 Árabe',
                                'zh' => '🇨🇳 Chino',
                                'ja' => '🇯🇵 Japonés',
                                'ko' => '🇰🇷 Coreano',
                                'ru' => '🇷🇺 Ruso',
                            ])
                            ->label('Idioma Original'),
                        
                        Forms\Components\TextInput::make('translator')
                            ->maxLength(255)
                            ->label('Traductor')
                            ->placeholder('Nombre del traductor...'),
                        
                        Forms\Components\Toggle::make('is_translation')
                            ->label('Es una Traducción')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Contenido y Descripción')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->maxLength(2000)
                            ->label('Descripción')
                            ->rows(4)
                            ->placeholder('Sinopsis o descripción del libro...'),
                        
                        Forms\Components\Textarea::make('summary')
                            ->maxLength(1000)
                            ->label('Resumen')
                            ->rows(3)
                            ->placeholder('Resumen ejecutivo...'),
                        
                        Forms\Components\Textarea::make('table_of_contents')
                            ->maxLength(2000)
                            ->label('Índice')
                            ->rows(4)
                            ->placeholder('Índice o tabla de contenidos...'),
                        
                        Forms\Components\TextInput::make('keywords')
                            ->maxLength(500)
                            ->label('Palabras Clave')
                            ->placeholder('Palabras separadas por comas...'),
                    ])->columns(1),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\TextInput::make('series')
                            ->maxLength(255)
                            ->label('Serie')
                            ->placeholder('Nombre de la serie...'),
                        
                        Forms\Components\TextInput::make('series_number')
                            ->numeric()
                            ->label('Número en Serie')
                            ->placeholder('1, 2, 3...'),
                        
                        Forms\Components\TextInput::make('awards')
                            ->maxLength(500)
                            ->label('Premios')
                            ->placeholder('Premios recibidos...'),
                        
                        Forms\Components\KeyValue::make('additional_info')
                            ->label('Información Adicional')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Campo'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Disponibilidad')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Disponible')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_recommended')
                            ->label('Recomendado')
                            ->default(false),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'published' => '✅ Publicado',
                                'draft' => '📝 Borrador',
                                'review' => '👀 En Revisión',
                                'archived' => '📦 Archivado',
                                'out_of_print' => '🚫 Agotado',
                            ])
                            ->default('published')
                            ->label('Estado'),
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
                
                Tables\Columns\TextColumn::make('author')
                    ->label('Autor')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\BadgeColumn::make('genre')
                    ->label('Género')
                    ->colors([
                        'primary' => 'fiction',
                        'success' => 'non_fiction',
                        'warning' => 'mystery',
                        'info' => 'romance',
                        'danger' => 'science_fiction',
                        'secondary' => 'fantasy',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fiction' => '📚 Ficción',
                        'non_fiction' => '📖 No Ficción',
                        'mystery' => '🔍 Misterio',
                        'romance' => '💕 Romance',
                        'science_fiction' => '🚀 Ciencia Ficción',
                        'fantasy' => '🐉 Fantasía',
                        'thriller' => '😱 Thriller',
                        'horror' => '👻 Terror',
                        'biography' => '👤 Biografía',
                        'history' => '🏛️ Historia',
                        'philosophy' => '🤔 Filosofía',
                        'religion' => '⛪ Religión',
                        'science' => '🔬 Ciencia',
                        'technology' => '💻 Tecnología',
                        'business' => '💼 Negocios',
                        'self_help' => '💪 Autoayuda',
                        'cookbook' => '👨‍🍳 Cocina',
                        'travel' => '✈️ Viajes',
                        'poetry' => '📝 Poesía',
                        'drama' => '🎭 Teatro',
                        'children' => '👶 Infantil',
                        'young_adult' => '👧👦 Juvenil',
                        'academic' => '🎓 Académico',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('publisher')
                    ->label('Editorial')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('publication_date')
                    ->label('Publicación')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable()
                    ->limit(15)
                    ->copyable()
                    ->color('secondary'),
                
                Tables\Columns\TextColumn::make('pages')
                    ->label('Páginas')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('format')
                    ->label('Formato')
                    ->colors([
                        'primary' => 'hardcover',
                        'success' => 'paperback',
                        'info' => 'ebook',
                        'warning' => 'audiobook',
                        'secondary' => 'pdf',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hardcover' => '📘 Tapa Dura',
                        'paperback' => '📗 Tapa Blanda',
                        'ebook' => '📱 E-book',
                        'audiobook' => '🎧 Audiolibro',
                        'pdf' => '📄 PDF',
                        'epub' => '📱 EPUB',
                        'mobi' => '📱 MOBI',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('target_audience')
                    ->label('Audiencia')
                    ->colors([
                        'success' => 'children',
                        'warning' => 'young_adult',
                        'primary' => 'adult',
                        'info' => 'academic',
                        'secondary' => 'general',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'children' => '👶 Niños',
                        'young_adult' => '👧👦 Jóvenes',
                        'adult' => '👨👩 Adultos',
                        'academic' => '🎓 Académico',
                        'general' => '🌍 General',
                        'professional' => '💼 Profesional',
                        'senior' => '👴👵 Mayores',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Disponible')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_translation')
                    ->label('Traducción')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('genre')
                    ->options([
                        'fiction' => '📚 Ficción',
                        'non_fiction' => '📖 No Ficción',
                        'mystery' => '🔍 Misterio',
                        'romance' => '💕 Romance',
                        'science_fiction' => '🚀 Ciencia Ficción',
                        'fantasy' => '🐉 Fantasía',
                        'thriller' => '😱 Thriller',
                        'horror' => '👻 Terror',
                        'biography' => '👤 Biografía',
                        'history' => '🏛️ Historia',
                        'philosophy' => '🤔 Filosofía',
                        'religion' => '⛪ Religión',
                        'science' => '🔬 Ciencia',
                        'technology' => '💻 Tecnología',
                        'business' => '💼 Negocios',
                        'self_help' => '💪 Autoayuda',
                        'cookbook' => '👨‍🍳 Cocina',
                        'travel' => '✈️ Viajes',
                        'poetry' => '📝 Poesía',
                        'drama' => '🎭 Teatro',
                        'children' => '👶 Infantil',
                        'young_adult' => '👧👦 Juvenil',
                        'academic' => '🎓 Académico',
                        'other' => '❓ Otro',
                    ])
                    ->label('Género'),
                
                Tables\Filters\SelectFilter::make('format')
                    ->options([
                        'hardcover' => '📘 Tapa Dura',
                        'paperback' => '📗 Tapa Blanda',
                        'ebook' => '📱 E-book',
                        'audiobook' => '🎧 Audiolibro',
                        'pdf' => '📄 PDF',
                        'epub' => '📱 EPUB',
                        'mobi' => '📱 MOBI',
                        'other' => '❓ Otro',
                    ])
                    ->label('Formato'),
                
                Tables\Filters\SelectFilter::make('target_audience')
                    ->options([
                        'children' => '👶 Niños (0-12 años)',
                        'young_adult' => '👧👦 Jóvenes (13-18 años)',
                        'adult' => '👨👩 Adultos (18+ años)',
                        'academic' => '🎓 Académico',
                        'general' => '🌍 Público General',
                        'professional' => '💼 Profesional',
                        'senior' => '👴👵 Adultos Mayores',
                    ])
                    ->label('Audiencia'),
                
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'es' => '🇪🇸 Español',
                        'en' => '🇬🇧 Inglés',
                        'fr' => '🇫🇷 Francés',
                        'de' => '🇩🇪 Alemán',
                        'it' => '🇮🇹 Italiano',
                        'pt' => '🇵🇹 Portugués',
                        'ca' => '🏴󠁥󠁳󠁣󠁴󠁿 Catalán',
                        'eu' => '🏴󠁥󠁳󠁰󠁶󠁿 Euskera',
                        'gl' => '🏴󠁥󠁳󠁧󠁡󠁿 Gallego',
                    ])
                    ->label('Idioma'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'published' => '✅ Publicado',
                        'draft' => '📝 Borrador',
                        'review' => '👀 En Revisión',
                        'archived' => '📦 Archivado',
                        'out_of_print' => '🚫 Agotado',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('available_only')
                    ->label('Solo Disponibles')
                    ->query(fn (Builder $query): Builder => $query->where('is_available', true)),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('recent_publications')
                    ->label('Publicaciones Recientes')
                    ->query(fn (Builder $query): Builder => $query->where('publication_date', '>=', now()->subYear())),
                
                Tables\Filters\Filter::make('long_books')
                    ->label('Libros Largos')
                    ->query(fn (Builder $query): Builder => $query->where('pages', '>=', 500)),
                
                Tables\Filters\Filter::make('short_books')
                    ->label('Libros Cortos')
                    ->query(fn (Builder $query): Builder => $query->where('pages', '<=', 200)),
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
                
                Tables\Actions\Action::make('toggle_available')
                    ->label(fn ($record): string => $record->is_available ? 'Marcar No Disponible' : 'Marcar Disponible')
                    ->icon(fn ($record): string => $record->is_available ? 'fas-times' : 'fas-check')
                    ->action(function ($record): void {
                        $record->update(['is_available' => !$record->is_available]);
                    })
                    ->color(fn ($record): string => $record->is_available ? 'danger' : 'success'),
                
                Tables\Actions\Action::make('search_isbn')
                    ->label('Buscar ISBN')
                    ->icon('fas-search')
                    ->url(fn ($record): string => "https://www.google.com/search?q=ISBN+{$record->isbn}")
                    ->openUrlInNewTab()
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
                    
                    Tables\Actions\BulkAction::make('mark_available')
                        ->label('Marcar como Disponibles')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_available' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_unavailable')
                        ->label('Marcar como No Disponibles')
                        ->icon('fas-times')
                        ->action(function ($records): void {
                            $records->each->update(['is_available' => false]);
                        })
                        ->color('danger'),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
