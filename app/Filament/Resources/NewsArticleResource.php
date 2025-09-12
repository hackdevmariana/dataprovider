<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsArticleResource\Pages;
use App\Filament\Resources\NewsArticleResource\RelationManagers;
use App\Models\NewsArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\DateTimeColumn;
use Filament\Tables\Columns\IconColumn;

class NewsArticleResource extends Resource
{
    protected static ?string $model = NewsArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $label = 'Artículo de Noticias';
    protected static ?string $pluralLabel = 'Artículos de Noticias';
    protected static ?string $navigationLabel = 'Noticias';
    protected static ?int $navigationSort = 30;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Básica')
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Título del artículo'),
                    
                    TextInput::make('slug')
                        ->label('Slug')
                        ->maxLength(255)
                        ->placeholder('titulo-del-articulo')
                        ->helperText('URL amigable del artículo'),
                    
                    TextInput::make('summary')
                        ->label('Resumen')
                        ->maxLength(500)
                        ->placeholder('Resumen o entradilla del artículo'),
                    
                    Textarea::make('content')
                        ->label('Contenido')
                        ->rows(8)
                        ->placeholder('Contenido completo del artículo'),
                    
                    TextInput::make('source_url')
                        ->label('URL de la Fuente')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://ejemplo.com/noticia'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Fechas y Programación')
                ->schema([
                    DateTimePicker::make('published_at')
                        ->label('Fecha de Publicación')
                        ->placeholder('Cuándo se publicará el artículo'),
                    
                    DateTimePicker::make('featured_start')
                        ->label('Inicio Destacado')
                        ->placeholder('Cuándo comenzará a destacar'),
                    
                    DateTimePicker::make('featured_end')
                        ->label('Fin Destacado')
                        ->placeholder('Cuándo dejará de destacar'),
                ])
                ->columns(3),

            Forms\Components\Section::make('Relaciones')
                ->schema([
                    Select::make('media_outlet_id')
                        ->label('Medio de Comunicación')
                        ->relationship('mediaOutlet', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar medio'),
                    
                    Select::make('author_id')
                        ->label('Autor')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar autor'),
                    
                    Select::make('municipality_id')
                        ->label('Municipio')
                        ->relationship('municipality', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar municipio'),
                    
                    Select::make('language_id')
                        ->label('Idioma')
                        ->relationship('language', 'language')
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar idioma'),
                    
                    Select::make('image_id')
                        ->label('Imagen Principal')
                        ->relationship('image', 'url')
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar imagen'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Clasificación y Estado')
                ->schema([
                    Select::make('category')
                        ->label('Categoría')
                        ->options([
                            'energia' => 'Energía',
                            'sostenibilidad' => 'Sostenibilidad',
                            'medio_ambiente' => 'Medio Ambiente',
                            'tecnologia' => 'Tecnología',
                            'economia' => 'Economía',
                            'politica' => 'Política',
                            'turismo' => 'Turismo',
                            'educacion' => 'Educación',
                            'salud' => 'Salud',
                        ])
                        ->default('general')
                        ->required(),
                    
                    TextInput::make('topic_focus')
                        ->label('Enfoque Temático')
                        ->placeholder('Enfoque específico del artículo'),
                    
                    Select::make('article_type')
                        ->label('Tipo de Artículo')
                        ->options([
                            'noticia' => 'Noticia',
                            'reportaje' => 'Reportaje',
                            'entrevista' => 'Entrevista',
                            'opinion' => 'Opinión',
                            'analisis' => 'Análisis',
                            'comunicado' => 'Comunicado',
                        ])
                        ->default('noticia')
                        ->required(),
                    
                    Select::make('status')
                        ->label('Estado')
                        ->options([
                            'draft' => 'Borrador',
                            'review' => 'En Revisión',
                            'published' => 'Publicado',
                            'archived' => 'Archivado',
                        ])
                        ->default('draft')
                        ->required(),
                    
                    Select::make('visibility')
                        ->label('Visibilidad')
                        ->options([
                            'public' => 'Público',
                            'private' => 'Privado',
                        ])
                        ->default('public')
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Configuración')
                ->schema([
                    Toggle::make('is_outstanding')
                        ->label('Artículo Destacado')
                        ->helperText('Marcar si es un artículo destacado'),
                    
                    Toggle::make('is_verified')
                        ->label('Artículo Verificado')
                        ->helperText('Marcar si el contenido ha sido verificado'),
                    
                    Toggle::make('is_scraped')
                        ->label('Obtenido por Scraping')
                        ->helperText('Si fue obtenido automáticamente'),
                    
                    Toggle::make('is_translated')
                        ->label('Artículo Traducido')
                        ->helperText('Si es una traducción de otro idioma'),
                    
                    Toggle::make('is_breaking_news')
                        ->label('Noticia de Última Hora')
                        ->helperText('Si es una noticia urgente'),
                    
                    Toggle::make('is_evergreen')
                        ->label('Contenido Perenne')
                        ->helperText('Si es contenido que no caduca'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Métricas')
                ->schema([
                    TextInput::make('views_count')
                        ->label('Contador de Vistas')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->helperText('Número de visualizaciones'),
                    
                    TextInput::make('shares_count')
                        ->label('Contador de Compartidos')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->helperText('Número de veces compartido'),
                    
                    TextInput::make('comments_count')
                        ->label('Contador de Comentarios')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->helperText('Número de comentarios'),
                    
                    TextInput::make('reading_time_minutes')
                        ->label('Tiempo de Lectura (minutos)')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.1)
                        ->helperText('Tiempo estimado de lectura'),
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['mediaOutlet', 'author', 'municipality', 'language']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('title')
                    ->label('Título')
                    ->limit(50)
                    ->searchable()
                    ->sortable()
                    ->tooltip(function ($record) {
                        return $record->title;
                    }),
                
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'energia' => 'success',
                        'sostenibilidad' => 'info',
                        'medio_ambiente' => 'warning',
                        'tecnologia' => 'primary',
                        'economia' => 'gray',
                        'politica' => 'danger',
                        'turismo' => 'success',
                        'educacion' => 'info',
                        'salud' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'energia' => 'Energía',
                        'sostenibilidad' => 'Sostenibilidad',
                        'medio_ambiente' => 'Medio Ambiente',
                        'tecnologia' => 'Tecnología',
                        'economia' => 'Economía',
                        'politica' => 'Política',
                        'turismo' => 'Turismo',
                        'educacion' => 'Educación',
                        'salud' => 'Salud',
                        default => ucfirst($state),
                    }),
                
                TextColumn::make('mediaOutlet.name')
                    ->label('Medio')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('author.name')
                    ->label('Autor')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin autor')
                    ->color('info'),
                
                TextColumn::make('municipality.name')
                    ->label('Municipio')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin municipio')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color('success'),
                
                TextColumn::make('views_count')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        ($record->views_count ?? 0) >= 5000 => 'success',
                        ($record->views_count ?? 0) >= 2000 => 'info',
                        ($record->views_count ?? 0) >= 500 => 'warning',
                        default => 'gray',
                    }),
                
                TextColumn::make('shares_count')
                    ->label('Compartidos')
                    ->numeric()
                    ->sortable()
                    ->placeholder('0')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('comments_count')
                    ->label('Comentarios')
                    ->numeric()
                    ->sortable()
                    ->placeholder('0')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('environmental_impact_score')
                    ->label('Impacto Ambiental')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        ($record->environmental_impact_score ?? 0) >= 8 => 'success',
                        ($record->environmental_impact_score ?? 0) >= 6 => 'info',
                        ($record->environmental_impact_score ?? 0) >= 4 => 'warning',
                        default => 'gray',
                    })
                    ->suffix('/10'),
                
                IconColumn::make('is_outstanding')
                    ->label('Destacado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                IconColumn::make('is_breaking_news')
                    ->label('Última Hora')
                    ->boolean()
                    ->trueIcon('heroicon-o-fire')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('danger')
                    ->falseColor('gray'),
                
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'review' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Publicado',
                        'draft' => 'Borrador',
                        'review' => 'En Revisión',
                        'archived' => 'Archivado',
                        default => ucfirst($state),
                    }),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'energia' => 'Energía',
                        'sostenibilidad' => 'Sostenibilidad',
                        'medio_ambiente' => 'Medio Ambiente',
                        'tecnologia' => 'Tecnología',
                        'economia' => 'Economía',
                        'politica' => 'Política',
                        'turismo' => 'Turismo',
                        'educacion' => 'Educación',
                        'salud' => 'Salud',
                    ])
                    ->searchable()
                    ->placeholder('Todas las categorías'),
                
                Tables\Filters\SelectFilter::make('media_outlet')
                    ->label('Medio de Comunicación')
                    ->relationship('mediaOutlet', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los medios'),
                
                Tables\Filters\SelectFilter::make('author')
                    ->label('Autor')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los autores'),
                
                Tables\Filters\SelectFilter::make('municipality')
                    ->label('Municipio')
                    ->relationship('municipality', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los municipios'),
                
                Tables\Filters\TernaryFilter::make('is_outstanding')
                    ->label('Artículo Destacado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo destacados')
                    ->falseLabel('Solo no destacados'),
                
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Artículo Verificado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo verificados')
                    ->falseLabel('Solo no verificados'),
                
                Tables\Filters\TernaryFilter::make('is_breaking_news')
                    ->label('Última Hora')
                    ->placeholder('Todos')
                    ->trueLabel('Solo últimas horas')
                    ->falseLabel('Solo noticias normales'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'review' => 'En Revisión',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                    ])
                    ->placeholder('Todos los estados'),
                
                Tables\Filters\Filter::make('views_range')
                    ->label('Rango de Vistas')
                    ->form([
                        Forms\Components\TextInput::make('views_from')
                            ->label('Desde')
                            ->numeric()
                            ->placeholder('Ej: 1000'),
                        Forms\Components\TextInput::make('views_to')
                            ->label('Hasta')
                            ->numeric()
                            ->placeholder('Ej: 10000'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['views_from'],
                                fn (Builder $query, $viewsFrom): Builder => $query->where('views_count', '>=', $viewsFrom),
                            )
                            ->when(
                                $data['views_to'],
                                fn (Builder $query, $viewsTo): Builder => $query->where('views_count', '<=', $viewsTo),
                            );
                    }),
                
                Tables\Filters\Filter::make('impact_range')
                    ->label('Rango de Impacto Ambiental')
                    ->form([
                        Forms\Components\TextInput::make('impact_from')
                            ->label('Desde')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->placeholder('Ej: 7'),
                        Forms\Components\TextInput::make('impact_to')
                            ->label('Hasta')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->placeholder('Ej: 10'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['impact_from'],
                                fn (Builder $query, $impactFrom): Builder => $query->where('environmental_impact_score', '>=', $impactFrom),
                            )
                            ->when(
                                $data['impact_to'],
                                fn (Builder $query, $impactTo): Builder => $query->where('environmental_impact_score', '<=', $impactTo),
                            );
                    }),
                
                Tables\Filters\Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label('Publicado desde'),
                        Forms\Components\DatePicker::make('published_to')
                            ->label('Publicado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $publishedFrom): Builder => $query->whereDate('published_at', '>=', $publishedFrom),
                            )
                            ->when(
                                $data['published_to'],
                                fn (Builder $query, $publishedTo): Builder => $query->whereDate('published_at', '<=', $publishedTo),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_media_outlet')
                    ->label('Ver Medio')
                    ->icon('heroicon-o-newspaper')
                    ->url(fn ($record) => $record->media_outlet_id ? route('filament.admin.resources.media-outlets.edit', $record->media_outlet_id) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->visible(fn ($record) => $record->media_outlet_id !== null),
                Tables\Actions\Action::make('view_author')
                    ->label('Ver Autor')
                    ->icon('heroicon-o-user')
                    ->url(fn ($record) => $record->author_id ? route('filament.admin.resources.people.edit', $record->author_id) : null)
                    ->openUrlInNewTab()
                    ->color('info')
                    ->visible(fn ($record) => $record->author_id !== null),
                Tables\Actions\Action::make('toggle_outstanding')
                    ->label('Cambiar Destacado')
                    ->icon('heroicon-o-star')
                    ->action(function ($record) {
                        $record->update(['is_outstanding' => !$record->is_outstanding]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Estado destacado actualizado')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cambiar Estado Destacado')
                    ->modalDescription('¿Estás seguro de que quieres cambiar el estado destacado de este artículo?')
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('mark_as_outstanding')
                    ->label('Marcar como Destacados')
                    ->icon('heroicon-o-star')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update(['is_outstanding' => true]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Artículos marcados como destacados')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Marcar como Destacados')
                    ->modalDescription('¿Estás seguro de que quieres marcar estos artículos como destacados?')
                    ->color('warning'),
                Tables\Actions\BulkAction::make('mark_as_verified')
                    ->label('Marcar como Verificados')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update(['is_verified' => true]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Artículos marcados como verificados')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Marcar como Verificados')
                    ->modalDescription('¿Estás seguro de que quieres marcar estos artículos como verificados?')
                    ->color('success'),
                Tables\Actions\BulkAction::make('calculate_engagement_stats')
                    ->label('Calcular Estadísticas de Engagement')
                    ->icon('heroicon-o-calculator')
                    ->action(function ($records) {
                        $totalViews = $records->sum('views_count');
                        $totalShares = $records->sum('shares_count');
                        $totalComments = $records->sum('comments_count');
                        $avgImpact = $records->avg('environmental_impact_score');
                        $outstandingCount = $records->where('is_outstanding', true)->count();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Estadísticas de Engagement calculadas')
                            ->body("Total vistas: {$totalViews}\nTotal compartidos: {$totalShares}\nTotal comentarios: {$totalComments}\nImpacto promedio: " . round($avgImpact, 1) . "\nDestacados: {$outstandingCount}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Calcular Estadísticas')
                    ->modalDescription('¿Estás seguro de que quieres calcular las estadísticas de engagement de estos artículos?')
                    ->color('info'),
            ])
            ->defaultSort('published_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['mediaOutlet', 'author', 'municipality', 'language']));
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
            'index' => Pages\ListNewsArticles::route('/'),
            'create' => Pages\CreateNewsArticle::route('/create'),
            'edit' => Pages\EditNewsArticle::route('/{record}/edit'),
        ];
    }
}
