<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use App\Models\QuoteCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'fas-quote-left';

    protected static ?string $navigationGroup = 'Contenido y Medios';

    protected static ?string $navigationLabel = 'Citas';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Citas';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenido de la Cita')
                    ->schema([
                        Forms\Components\Textarea::make('text')
                            ->required()
                            ->maxLength(1000)
                            ->label('Texto de la Cita')
                            ->rows(4)
                            ->placeholder('Escribe aquí la cita o refrán...'),
                        
                        Forms\Components\TextInput::make('author')
                            ->maxLength(255)
                            ->label('Autor')
                            ->placeholder('Nombre del autor o "Anónimo"'),
                        
                        Forms\Components\TextInput::make('source')
                            ->maxLength(255)
                            ->label('Fuente')
                            ->placeholder('Libro, película, discurso, etc.'),
                    ])->columns(1),

                Forms\Components\Section::make('Clasificación')
                    ->schema([
                        Forms\Components\Select::make('language')
                            ->options([
                                'es' => 'Español',
                                'en' => 'Inglés',
                                'fr' => 'Francés',
                                'de' => 'Alemán',
                                'it' => 'Italiano',
                                'pt' => 'Portugués',
                                'ca' => 'Catalán',
                                'eu' => 'Euskera',
                                'gl' => 'Gallego',
                                'la' => 'Latín',
                                'gr' => 'Griego',
                                'ar' => 'Árabe',
                                'zh' => 'Chino',
                                'ja' => 'Japonés',
                                'ko' => 'Coreano',
                                'ru' => 'Ruso',
                            ])
                            ->required()
                            ->default('es')
                            ->label('Idioma'),
                        
                        Forms\Components\Select::make('category')
                            ->options(QuoteCategory::pluck('name', 'name'))
                            ->searchable()
                            ->preload()
                            ->label('Categoría'),
                        
                        Forms\Components\Select::make('mood')
                            ->options([
                                'inspiring' => 'Inspirador',
                                'motivational' => 'Motivacional',
                                'philosophical' => 'Filosófico',
                                'humorous' => 'Humorístico',
                                'romantic' => 'Romántico',
                                'melancholic' => 'Melancólico',
                                'energetic' => 'Energético',
                                'calm' => 'Tranquilo',
                                'mysterious' => 'Misterioso',
                                'optimistic' => 'Optimista',
                                'pessimistic' => 'Pesimista',
                            ])
                            ->label('Estado de Ánimo'),
                        
                        Forms\Components\Select::make('difficulty_level')
                            ->options([
                                'easy' => 'Fácil',
                                'medium' => 'Medio',
                                'hard' => 'Difícil',
                                'expert' => 'Experto',
                            ])
                            ->label('Nivel de Dificultad'),
                    ])->columns(2),

                Forms\Components\Section::make('Métricas y Popularidad')
                    ->schema([
                        Forms\Components\TextInput::make('word_count')
                            ->numeric()
                            ->label('Número de Palabras')
                            ->disabled()
                            ->helperText('Se calcula automáticamente'),
                        
                        Forms\Components\TextInput::make('character_count')
                            ->numeric()
                            ->label('Número de Caracteres')
                            ->disabled()
                            ->helperText('Se calcula automáticamente'),
                        
                        Forms\Components\TextInput::make('popularity_score')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(1)
                            ->label('Puntuación de Popularidad')
                            ->helperText('Valor entre 0 y 1'),
                        
                        Forms\Components\TextInput::make('usage_count')
                            ->numeric()
                            ->label('Contador de Uso')
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Etiquetas y Traducciones')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->separator(',')
                            ->placeholder('Agregar etiquetas...'),
                        
                        Forms\Components\KeyValue::make('translations')
                            ->label('Traducciones')
                            ->keyLabel('Idioma')
                            ->valueLabel('Traducción')
                            ->addActionLabel('Agregar Traducción'),
                    ])->columns(1),

                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->helperText('Indica si la cita ha sido verificada'),
                    ]),
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
                
                Tables\Columns\TextColumn::make('text')
                    ->label('Cita')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('author')
                    ->label('Autor')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('source')
                    ->label('Fuente')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\BadgeColumn::make('language')
                    ->label('Idioma')
                    ->colors([
                        'primary' => 'es',
                        'success' => 'en',
                        'warning' => 'fr',
                        'info' => 'de',
                        'danger' => 'it',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'es' => '🇪🇸 ES',
                        'en' => '🇬🇧 EN',
                        'fr' => '🇫🇷 FR',
                        'de' => '🇩🇪 DE',
                        'it' => '🇮🇹 IT',
                        'pt' => '🇵🇹 PT',
                        'ca' => '🏴󠁥󠁳󠁣󠁴󠁿 CA',
                        'eu' => '🏴󠁥󠁳󠁰󠁶󠁿 EU',
                        'gl' => '🏴󠁥󠁳󠁧󠁡󠁿 GL',
                        'la' => '🏛️ LA',
                        'gr' => '🇬🇷 GR',
                        'ar' => '🇸🇦 AR',
                        'zh' => '🇨🇳 ZH',
                        'ja' => '🇯🇵 JA',
                        'ko' => '🇰🇷 KO',
                        'ru' => '🇷🇺 RU',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('mood')
                    ->label('Estado de Ánimo')
                    ->colors([
                        'success' => 'inspiring',
                        'warning' => 'motivational',
                        'info' => 'philosophical',
                        'light' => 'humorous',
                        'danger' => 'romantic',
                        'secondary' => 'melancholic',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'inspiring' => '✨ Inspirador',
                        'motivational' => '🚀 Motivacional',
                        'philosophical' => '🤔 Filosófico',
                        'humorous' => '😄 Humorístico',
                        'romantic' => '💕 Romántico',
                        'melancholic' => '😔 Melancólico',
                        'energetic' => '⚡ Energético',
                        'calm' => '😌 Tranquilo',
                        'mysterious' => '🔮 Misterioso',
                        'optimistic' => '😊 Optimista',
                        'pessimistic' => '😞 Pesimista',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('difficulty_level')
                    ->label('Dificultad')
                    ->colors([
                        'success' => 'easy',
                        'info' => 'medium',
                        'warning' => 'hard',
                        'danger' => 'expert',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'easy' => 'Fácil',
                        'medium' => 'Medio',
                        'hard' => 'Difícil',
                        'expert' => 'Experto',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('word_count')
                    ->label('Palabras')
                    ->sortable()
                    ->numeric(),
                
                Tables\Columns\TextColumn::make('popularity_score')
                    ->label('Popularidad')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 0.8 => 'success',
                        $state >= 0.6 => 'info',
                        $state >= 0.4 => 'warning',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Usos')
                    ->sortable()
                    ->numeric(),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificada')
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
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'es' => 'Español',
                        'en' => 'Inglés',
                        'fr' => 'Francés',
                        'de' => 'Alemán',
                        'it' => 'Italiano',
                        'pt' => 'Portugués',
                    ])
                    ->label('Idioma'),
                
                Tables\Filters\SelectFilter::make('category')
                    ->options(QuoteCategory::pluck('name', 'name'))
                    ->label('Categoría'),
                
                Tables\Filters\SelectFilter::make('mood')
                    ->options([
                        'inspiring' => 'Inspirador',
                        'motivational' => 'Motivacional',
                        'philosophical' => 'Filosófico',
                        'humorous' => 'Humorístico',
                        'romantic' => 'Romántico',
                        'melancholic' => 'Melancólico',
                    ])
                    ->label('Estado de Ánimo'),
                
                Tables\Filters\SelectFilter::make('difficulty_level')
                    ->options([
                        'easy' => 'Fácil',
                        'medium' => 'Medio',
                        'hard' => 'Difícil',
                        'expert' => 'Experto',
                    ])
                    ->label('Nivel de Dificultad'),
                
                Tables\Filters\Filter::make('high_popularity')
                    ->label('Alta Popularidad')
                    ->query(fn (Builder $query): Builder => $query->where('popularity_score', '>=', 0.7)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('short_quotes')
                    ->label('Citas Cortas')
                    ->query(fn (Builder $query): Builder => $query->where('word_count', '<=', 20)),
                
                Tables\Filters\Filter::make('long_quotes')
                    ->label('Citas Largas')
                    ->query(fn (Builder $query): Builder => $query->where('word_count', '>=', 50)),
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
                
                Tables\Actions\Action::make('mark_verified')
                    ->label('Verificar')
                    ->icon('fas-check-circle')
                    ->action(function ($record): void {
                        $record->update(['is_verified' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
                
                Tables\Actions\Action::make('increment_usage')
                    ->label('Incrementar Uso')
                    ->icon('fas-plus')
                    ->action(function ($record): void {
                        $record->increment('usage_count');
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
                    
                    Tables\Actions\BulkAction::make('mark_verified')
                        ->label('Marcar como Verificadas')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_verified' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_unverified')
                        ->label('Marcar como No Verificadas')
                        ->icon('fas-times-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_verified' => false]);
                        })
                        ->color('secondary'),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
