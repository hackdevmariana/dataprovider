<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsSourceResource\Pages;
use App\Filament\Resources\NewsSourceResource\RelationManagers;
use App\Models\NewsSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NewsSourceResource extends Resource
{
    protected static ?string $model = NewsSource::class;

    protected static ?string $navigationIcon = 'fas-newspaper';

    protected static ?string $navigationGroup = 'Contenido y Medios';

    protected static ?string $navigationLabel = 'Fuentes de Noticias';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Fuente de Noticias';

    protected static ?string $pluralModelLabel = 'Fuentes de Noticias';

    
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
                            ->label('Nombre de la Fuente')
                            ->placeholder('Ej: El País, BBC News, CNN...'),
                        
                        Forms\Components\TextInput::make('domain')
                            ->required()
                            ->maxLength(255)
                            ->label('Dominio')
                            ->placeholder('ejemplo.com')
                            ->helperText('Dominio principal sin http://'),
                        
                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->maxLength(500)
                            ->label('URL Principal')
                            ->placeholder('https://www.ejemplo.com')
                            ->url(),
                    ])->columns(1),

                Forms\Components\Section::make('Clasificación')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'newspaper' => '📰 Periódico',
                                'magazine' => '📖 Revista',
                                'tv' => '📺 Televisión',
                                'radio' => '📻 Radio',
                                'digital' => '💻 Digital',
                                'blog' => '✍️ Blog',
                                'social_media' => '📱 Red Social',
                                'wire_service' => '📡 Agencia',
                                'government' => '🏛️ Gobierno',
                                'academic' => '🎓 Académico',
                                'corporate' => '🏢 Corporativo',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Medio'),
                        
                        Forms\Components\Select::make('category')
                            ->options([
                                'general' => '🌍 General',
                                'politics' => '🏛️ Política',
                                'economy' => '💰 Economía',
                                'sports' => '⚽ Deportes',
                                'technology' => '💻 Tecnología',
                                'entertainment' => '🎬 Entretenimiento',
                                'health' => '🏥 Salud',
                                'science' => '🔬 Ciencia',
                                'culture' => '🎨 Cultura',
                                'international' => '🌐 Internacional',
                                'local' => '🏘️ Local',
                                'opinion' => '💭 Opinión',
                            ])
                            ->required()
                            ->label('Categoría'),
                        
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
                                'ar' => '🇸🇦 Árabe',
                                'zh' => '🇨🇳 Chino',
                                'ja' => '🇯🇵 Japonés',
                                'ko' => '🇰🇷 Coreano',
                                'ru' => '🇷🇺 Ruso',
                            ])
                            ->required()
                            ->default('es')
                            ->label('Idioma Principal'),
                        
                        Forms\Components\Select::make('country')
                            ->options([
                                'ES' => '🇪🇸 España',
                                'US' => '🇺🇸 Estados Unidos',
                                'GB' => '🇬🇧 Reino Unido',
                                'FR' => '🇫🇷 Francia',
                                'DE' => '🇩🇪 Alemania',
                                'IT' => '🇮🇹 Italia',
                                'PT' => '🇵🇹 Portugal',
                                'MX' => '🇲🇽 México',
                                'AR' => '🇦🇷 Argentina',
                                'CO' => '🇨🇴 Colombia',
                                'PE' => '🇵🇪 Perú',
                                'CL' => '🇨🇱 Chile',
                                'VE' => '🇻🇪 Venezuela',
                                'EC' => '🇪🇨 Ecuador',
                                'BO' => '🇧🇴 Bolivia',
                                'PY' => '🇵🇾 Paraguay',
                                'UY' => '🇺🇾 Uruguay',
                                'CR' => '🇨🇷 Costa Rica',
                                'PA' => '🇵🇦 Panamá',
                                'NI' => '🇳🇮 Nicaragua',
                                'HN' => '🇭🇳 Honduras',
                                'GT' => '🇬🇹 Guatemala',
                                'SV' => '🇸🇻 El Salvador',
                                'CU' => '🇨🇺 Cuba',
                                'DO' => '🇩🇴 República Dominicana',
                                'PR' => '🇵🇷 Puerto Rico',
                            ])
                            ->required()
                            ->default('ES')
                            ->label('País'),
                    ])->columns(2),

                Forms\Components\Section::make('Información de Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email de Contacto'),
                        
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50)
                            ->label('Teléfono'),
                        
                        Forms\Components\TextInput::make('address')
                            ->maxLength(500)
                            ->label('Dirección'),
                        
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100)
                            ->label('Ciudad'),
                        
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20)
                            ->label('Código Postal'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración y Estado')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true)
                            ->helperText('Indica si la fuente está activa y funcionando'),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(false)
                            ->helperText('Indica si la fuente ha sido verificada'),
                        
                        Forms\Components\Toggle::make('requires_subscription')
                            ->label('Requiere Suscripción')
                            ->default(false)
                            ->helperText('Indica si el contenido requiere suscripción'),
                        
                        Forms\Components\Select::make('reliability_score')
                            ->options([
                                '1' => '1 - Muy Baja',
                                '2' => '2 - Baja',
                                '3' => '3 - Media',
                                '4' => '4 - Alta',
                                '5' => '5 - Muy Alta',
                            ])
                            ->default('3')
                            ->label('Puntuación de Fiabilidad'),
                        
                        Forms\Components\Select::make('bias_rating')
                            ->options([
                                'neutral' => '⚖️ Neutral',
                                'left' => '⬅️ Izquierda',
                                'center_left' => '⬅️ Centro-Izquierda',
                                'center' => '⚖️ Centro',
                                'center_right' => '➡️ Centro-Derecha',
                                'right' => '➡️ Derecha',
                                'unknown' => '❓ Desconocido',
                            ])
                            ->default('neutral')
                            ->label('Tendencia Política'),
                    ])->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Breve descripción de la fuente...'),
                        
                        Forms\Components\KeyValue::make('social_media')
                            ->label('Redes Sociales')
                            ->keyLabel('Plataforma')
                            ->valueLabel('Usuario/URL')
                            ->addActionLabel('Agregar Red Social'),
                        
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Campo'),
                    ])->columns(1),
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
                    ->label('Nombre')
                    ->searchable()
                    ->limit(30)
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('domain')
                    ->label('Dominio')
                    ->searchable()
                    ->limit(25)
                    ->copyable()
                    ->color('secondary'),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'newspaper',
                        'success' => 'magazine',
                        'warning' => 'tv',
                        'info' => 'radio',
                        'danger' => 'digital',
                        'secondary' => 'blog',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'newspaper' => '📰 Periódico',
                        'magazine' => '📖 Revista',
                        'tv' => '📺 TV',
                        'radio' => '📻 Radio',
                        'digital' => '💻 Digital',
                        'blog' => '✍️ Blog',
                        'social_media' => '📱 Red Social',
                        'wire_service' => '📡 Agencia',
                        'government' => '🏛️ Gobierno',
                        'academic' => '🎓 Académico',
                        'corporate' => '🏢 Corporativo',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'general',
                        'success' => 'politics',
                        'warning' => 'economy',
                        'info' => 'sports',
                        'danger' => 'technology',
                        'secondary' => 'entertainment',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'general' => '🌍 General',
                        'politics' => '🏛️ Política',
                        'economy' => '💰 Economía',
                        'sports' => '⚽ Deportes',
                        'technology' => '💻 Tecnología',
                        'entertainment' => '🎬 Entretenimiento',
                        'health' => '🏥 Salud',
                        'science' => '🔬 Ciencia',
                        'culture' => '🎨 Cultura',
                        'international' => '🌐 Internacional',
                        'local' => '🏘️ Local',
                        'opinion' => '💭 Opinión',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ES' => '🇪🇸 España',
                        'US' => '🇺🇸 Estados Unidos',
                        'GB' => '🇬🇧 Reino Unido',
                        'FR' => '🇫🇷 Francia',
                        'DE' => '🇩🇪 Alemania',
                        'IT' => '🇮🇹 Italia',
                        'PT' => '🇵🇹 Portugal',
                        'MX' => '🇲🇽 México',
                        'AR' => '🇦🇷 Argentina',
                        'CO' => '🇨🇴 Colombia',
                        'PE' => '🇵🇪 Perú',
                        'CL' => '🇨🇱 Chile',
                        'VE' => '🇻🇪 Venezuela',
                        'EC' => '🇪🇨 Ecuador',
                        'BO' => '🇧🇴 Bolivia',
                        'PY' => '🇵🇾 Paraguay',
                        'UY' => '🇺🇾 Uruguay',
                        'CR' => '🇨🇷 Costa Rica',
                        'PA' => '🇵🇦 Panamá',
                        'NI' => '🇳🇮 Nicaragua',
                        'HN' => '🇭🇳 Honduras',
                        'GT' => '🇬🇹 Guatemala',
                        'SV' => '🇸🇻 El Salvador',
                        'CU' => '🇨🇺 Cuba',
                        'DO' => '🇩🇴 República Dominicana',
                        'PR' => '🇵🇷 Puerto Rico',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('reliability_score')
                    ->label('Fiabilidad')
                    ->colors([
                        'danger' => '1',
                        'warning' => '2',
                        'secondary' => '3',
                        'info' => '4',
                        'success' => '5',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => '1 ⭐',
                        '2' => '2 ⭐⭐',
                        '3' => '3 ⭐⭐⭐',
                        '4' => '4 ⭐⭐⭐⭐',
                        '5' => '5 ⭐⭐⭐⭐⭐',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('bias_rating')
                    ->label('Tendencia')
                    ->colors([
                        'secondary' => 'neutral',
                        'danger' => 'left',
                        'warning' => 'center_left',
                        'info' => 'center',
                        'success' => 'center_right',
                        'primary' => 'right',
                        'light' => 'unknown',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'neutral' => '⚖️ Neutral',
                        'left' => '⬅️ Izquierda',
                        'center_left' => '⬅️ Centro-Izq',
                        'center' => '⚖️ Centro',
                        'center_right' => '➡️ Centro-Der',
                        'right' => '➡️ Derecha',
                        'unknown' => '❓ Desconocido',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificada')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('requires_subscription')
                    ->label('Suscripción')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('success'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'newspaper' => '📰 Periódico',
                        'magazine' => '📖 Revista',
                        'tv' => '📺 Televisión',
                        'radio' => '📻 Radio',
                        'digital' => '💻 Digital',
                        'blog' => '✍️ Blog',
                        'social_media' => '📱 Red Social',
                        'wire_service' => '📡 Agencia',
                        'government' => '🏛️ Gobierno',
                        'academic' => '🎓 Académico',
                        'corporate' => '🏢 Corporativo',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Medio'),
                
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general' => '🌍 General',
                        'politics' => '🏛️ Política',
                        'economy' => '💰 Economía',
                        'sports' => '⚽ Deportes',
                        'technology' => '💻 Tecnología',
                        'entertainment' => '🎬 Entretenimiento',
                        'health' => '🏥 Salud',
                        'science' => '🔬 Ciencia',
                        'culture' => '🎨 Cultura',
                        'international' => '🌐 Internacional',
                        'local' => '🏘️ Local',
                        'opinion' => '💭 Opinión',
                    ])
                    ->label('Categoría'),
                
                Tables\Filters\SelectFilter::make('country')
                    ->options([
                        'ES' => '🇪🇸 España',
                        'US' => '🇺🇸 Estados Unidos',
                        'GB' => '🇬🇧 Reino Unido',
                        'FR' => '🇫🇷 Francia',
                        'DE' => '🇩🇪 Alemania',
                        'IT' => '🇮🇹 Italia',
                        'PT' => '🇵🇹 Portugal',
                        'MX' => '🇲🇽 México',
                        'AR' => '🇦🇷 Argentina',
                        'CO' => '🇨🇴 Colombia',
                    ])
                    ->label('País'),
                
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'es' => '🇪🇸 Español',
                        'en' => '🇬🇧 Inglés',
                        'fr' => '🇫🇷 Francés',
                        'de' => '🇩🇪 Alemán',
                        'it' => '🇮🇹 Italiano',
                        'pt' => '🇵🇹 Portugués',
                    ])
                    ->label('Idioma'),
                
                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                
                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true)),
                
                Tables\Filters\Filter::make('high_reliability')
                    ->label('Alta Fiabilidad')
                    ->query(fn (Builder $query): Builder => $query->whereIn('reliability_score', ['4', '5'])),
                
                Tables\Filters\Filter::make('free_sources')
                    ->label('Fuentes Gratuitas')
                    ->query(fn (Builder $query): Builder => $query->where('requires_subscription', false)),
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
                
                Tables\Actions\Action::make('visit_website')
                    ->label('Visitar Web')
                    ->icon('fas-external-link-alt')
                    ->url(fn ($record): string => $record->url)
                    ->openUrlInNewTab()
                    ->color('primary'),
                
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn ($record): string => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn ($record): string => $record->is_active ? 'fas-times' : 'fas-check')
                    ->action(function ($record): void {
                        $record->update(['is_active' => !$record->is_active]);
                    })
                    ->color(fn ($record): string => $record->is_active ? 'danger' : 'success'),
                
                Tables\Actions\Action::make('mark_verified')
                    ->label('Verificar')
                    ->icon('fas-check-circle')
                    ->action(function ($record): void {
                        $record->update(['is_verified' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_verified)
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar')
                        ->icon('fas-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_active' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar')
                        ->icon('fas-times')
                        ->action(function ($records): void {
                            $records->each->update(['is_active' => false]);
                        })
                        ->color('danger'),
                    
                    Tables\Actions\BulkAction::make('mark_verified')
                        ->label('Marcar como Verificadas')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_verified' => true]);
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
            'index' => Pages\ListNewsSources::route('/'),
            'create' => Pages\CreateNewsSource::route('/create'),
            'view' => Pages\ViewNewsSource::route('/{record}'),
            'edit' => Pages\EditNewsSource::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
