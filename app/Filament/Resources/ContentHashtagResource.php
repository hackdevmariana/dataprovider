<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentHashtagResource\Pages;
use App\Filament\Resources\ContentHashtagResource\RelationManagers;
use App\Models\ContentHashtag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContentHashtagResource extends Resource
{
    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $model = ContentHashtag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';
    protected static ?string $label = 'Contenido-Hashtag';
    protected static ?string $pluralLabel = 'Contenidos-Hashtags';
    protected static ?string $navigationLabel = 'Relaciones Hashtag';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Hashtag')
                    ->schema([
                        Forms\Components\Select::make('hashtag_id')
                            ->label('Hashtag')
                            ->relationship('hashtag', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('added_by')
                            ->label('Añadido por')
                            ->relationship('addedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contenido Asociado')
                    ->schema([
                        Forms\Components\Select::make('hashtaggable_type')
                            ->label('Tipo de Contenido')
                            ->options([
                                'App\\Models\\Person' => 'Persona',
                                'App\\Models\\Event' => 'Evento',
                                'App\\Models\\NewsArticle' => 'Artículo de Noticias',
                                'App\\Models\\UserGeneratedContent' => 'Contenido Generado por Usuario',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->reset('hashtaggable_id')),
                        
                        Forms\Components\Select::make('hashtaggable_id')
                            ->label('Contenido Específico')
                            ->options(function (callable $get) {
                                $type = $get('hashtaggable_type');
                                if (!$type) return [];
                                
                                return match($type) {
                                    'App\\Models\\Person' => \App\Models\Person::pluck('name', 'id')->toArray(),
                                    'App\\Models\\Event' => \App\Models\Event::pluck('name', 'id')->toArray(),
                                    'App\\Models\\NewsArticle' => \App\Models\NewsArticle::pluck('title', 'id')->toArray(),
                                    'App\\Models\\UserGeneratedContent' => \App\Models\UserGeneratedContent::pluck('title', 'id')->toArray(),
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Métricas y Configuración')
                    ->schema([
                        Forms\Components\TextInput::make('clicks_count')
                            ->label('Contador de Clicks')
                            ->numeric()
                            ->default(0)
                            ->helperText('Número de clicks en el hashtag desde este contenido'),
                        
                        Forms\Components\TextInput::make('relevance_score')
                            ->label('Puntuación de Relevancia')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(100)
                            ->helperText('Relevancia del hashtag para este contenido (0-100)'),
                        
                        Forms\Components\Toggle::make('is_auto_generated')
                            ->label('Auto-Generado')
                            ->helperText('Si fue añadido automáticamente por IA'),
                        
                        Forms\Components\TextInput::make('confidence_score')
                            ->label('Puntuación de Confianza')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('Confianza de la IA (0-100) - solo para hashtags auto-generados')
                            ->visible(fn (callable $get) => $get('is_auto_generated')),
                    ])
                    ->columns(2),
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
                
                Tables\Columns\TextColumn::make('hashtag.name')
                    ->label('Hashtag')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->url(fn ($record) => ($record->hashtag_id ?? null) ? route('filament.admin.resources.hashtags.edit', $record->hashtag_id) : null),
                
                Tables\Columns\TextColumn::make('content_type')
                    ->label('Tipo Contenido')
                    ->getStateUsing(function ($record) {
                        if (!$record->hashtaggable_type) return 'Sin tipo';
                        
                        return match($record->hashtaggable_type) {
                            'App\\Models\\Person' => 'Persona',
                            'App\\Models\\Event' => 'Evento',
                            'App\\Models\\NewsArticle' => 'Artículo',
                            'App\\Models\\UserGeneratedContent' => 'Contenido UGC',
                            default => str_replace('App\\Models\\', '', $record->hashtaggable_type),
                        };
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Persona' => 'info',
                        'Evento' => 'warning',
                        'Artículo' => 'success',
                        'Contenido UGC' => 'primary',
                        'Sin tipo' => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('content_name')
                    ->label('Nombre Contenido')
                    ->getStateUsing(function ($record) {
                        $content = $record->hashtaggable;
                        if (!$content) return 'Contenido no encontrado';
                        
                        return match($record->hashtaggable_type) {
                            'App\\Models\\Person' => $content->name ?? 'Persona #' . $content->id,
                            'App\\Models\\Event' => $content->name ?? 'Evento #' . $content->id,
                            'App\\Models\\NewsArticle' => $content->title ?? 'Artículo #' . $content->id,
                            'App\\Models\\UserGeneratedContent' => $content->title ?? 'Contenido #' . $content->id,
                            default => 'Contenido #' . $content->id,
                        };
                    })
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function ($record) {
                        $content = $record->hashtaggable;
                        if (!$content) return 'Contenido no encontrado';
                        
                        return match($record->hashtaggable_type) {
                            'App\\Models\\Person' => $content->name ?? 'Persona #' . $content->id,
                            'App\\Models\\Event' => $content->name ?? 'Evento #' . $content->id,
                            'App\\Models\\NewsArticle' => $content->title ?? 'Artículo #' . $content->id,
                            'App\\Models\\UserGeneratedContent' => $content->title ?? 'Contenido #' . $content->id,
                            default => 'Contenido #' . $content->id,
                        };
                    }),
                
                Tables\Columns\TextColumn::make('addedBy.name')
                    ->label('Añadido por')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Usuario no encontrado')
                    ->color('primary')
                    ->url(fn ($record) => ($record->added_by ?? null) ? route('filament.admin.resources.users.edit', $record->added_by) : null),
                
                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        ($record->clicks_count ?? 0) >= 100 => 'success',
                        ($record->clicks_count ?? 0) >= 50 => 'info',
                        ($record->clicks_count ?? 0) >= 10 => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('relevance_score')
                    ->label('Relevancia')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        ($record->relevance_score ?? 0) >= 90 => 'success',
                        ($record->relevance_score ?? 0) >= 75 => 'info',
                        ($record->relevance_score ?? 0) >= 60 => 'warning',
                        default => 'danger',
                    }),
                
                Tables\Columns\IconColumn::make('is_auto_generated')
                    ->label('Auto-Generado')
                    ->boolean()
                    ->trueIcon('heroicon-o-cpu-chip')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('warning')
                    ->falseColor('info'),
                
                Tables\Columns\TextColumn::make('confidence_score')
                    ->label('Confianza IA')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix('%')
                    ->placeholder('N/A')
                    ->color(fn ($record) => match (true) {
                        ($record->confidence_score ?? 0) >= 90 => 'success',
                        ($record->confidence_score ?? 0) >= 75 => 'info',
                        ($record->confidence_score ?? 0) >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->visible(fn ($record) => ($record->is_auto_generated ?? false)),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hashtag')
                    ->label('Hashtag')
                    ->relationship('hashtag', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los hashtags'),
                
                Tables\Filters\SelectFilter::make('added_by')
                    ->label('Usuario')
                    ->relationship('addedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los usuarios'),
                
                Tables\Filters\SelectFilter::make('content_type')
                    ->label('Tipo de Contenido')
                    ->options([
                        'App\\Models\\Person' => 'Persona',
                        'App\\Models\\Event' => 'Evento',
                        'App\\Models\\NewsArticle' => 'Artículo de Noticias',
                        'App\\Models\\UserGeneratedContent' => 'Contenido UGC',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['values'])) {
                            return $query->whereIn('hashtaggable_type', $data['values']);
                        }
                        return $query;
                    }),
                
                Tables\Filters\TernaryFilter::make('is_auto_generated')
                    ->label('Generación')
                    ->placeholder('Todos')
                    ->trueLabel('Solo auto-generados')
                    ->falseLabel('Solo manuales'),
                
                Tables\Filters\Filter::make('relevance_range')
                    ->label('Rango de Relevancia')
                    ->form([
                        Forms\Components\TextInput::make('relevance_from')
                            ->label('Desde')
                            ->numeric()
                            ->placeholder('Ej: 70'),
                        Forms\Components\TextInput::make('relevance_to')
                            ->label('Hasta')
                            ->numeric()
                            ->placeholder('Ej: 100'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['relevance_from'],
                                fn (Builder $query, $relevanceFrom): Builder => $query->where('relevance_score', '>=', $relevanceFrom),
                            )
                            ->when(
                                $data['relevance_to'],
                                fn (Builder $query, $relevanceTo): Builder => $query->where('relevance_score', '<=', $relevanceTo),
                            );
                    }),
                
                Tables\Filters\Filter::make('clicks_range')
                    ->label('Rango de Clicks')
                    ->form([
                        Forms\Components\TextInput::make('clicks_from')
                            ->label('Desde')
                            ->numeric()
                            ->placeholder('Ej: 0'),
                        Forms\Components\TextInput::make('clicks_to')
                            ->label('Hasta')
                            ->numeric()
                            ->placeholder('Ej: 100'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['clicks_from'],
                                fn (Builder $query, $clicksFrom): Builder => $query->where('clicks_count', '>=', $clicksFrom),
                            )
                            ->when(
                                $data['clicks_to'],
                                fn (Builder $query, $clicksTo): Builder => $query->where('clicks_count', '<=', $clicksTo),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_hashtag')
                    ->label('Ver Hashtag')
                    ->icon('heroicon-o-hashtag')
                    ->url(fn ($record) => ($record->hashtag_id ?? null) ? route('filament.admin.resources.hashtags.edit', $record->hashtag_id) : null)
                    ->openUrlInNewTab()
                    ->color('success')
                    ->visible(fn ($record) => ($record->hashtag_id ?? null) !== null),
                Tables\Actions\Action::make('view_content')
                    ->label('Ver Contenido')
                    ->icon('heroicon-o-eye')
                    ->url(function ($record) {
                        $content = $record->hashtaggable;
                        if (!$content) return null;
                        
                        if (!$record->hashtaggable_type) return null;
                        
                        return match($record->hashtaggable_type) {
                            'App\\Models\\Person' => route('filament.admin.resources.people.edit', $content->id),
                            'App\\Models\\Event' => route('filament.admin.resources.events.edit', $content->id),
                            'App\\Models\\NewsArticle' => route('filament.admin.resources.news-articles.edit', $content->id),
                            'App\\Models\\UserGeneratedContent' => route('filament.admin.resources.user-generated-contents.edit', $content->id),
                            default => null,
                        };
                    })
                    ->openUrlInNewTab()
                    ->color('info')
                    ->visible(fn ($record) => ($record->hashtaggable ?? null) !== null && ($record->hashtaggable_type ?? null) !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('increment_clicks')
                    ->label('Incrementar Clicks')
                    ->icon('heroicon-o-arrow-up')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if (method_exists($record, 'incrementClicks')) {
                                $record->incrementClicks();
                            } else {
                                $record->increment('clicks_count');
                            }
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Clicks incrementados')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Incrementar Clicks')
                    ->modalDescription('¿Estás seguro de que quieres incrementar los clicks de estos hashtags?')
                    ->color('success'),
                Tables\Actions\BulkAction::make('calculate_engagement')
                    ->label('Calcular Engagement')
                    ->icon('heroicon-o-calculator')
                    ->action(function ($records) {
                        $totalClicks = $records->sum('clicks_count') ?? 0;
                        $averageRelevance = $records->avg('relevance_score') ?? 0;
                        $autoGeneratedCount = $records->where('is_auto_generated', true)->count();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Análisis de Engagement completado')
                            ->body("Total clicks: {$totalClicks}\nRelevancia promedio: " . round($averageRelevance, 1) . "\nAuto-generados: {$autoGeneratedCount}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Calcular Engagement')
                    ->modalDescription('¿Estás seguro de que quieres calcular el engagement de estos hashtags?')
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['hashtag', 'addedBy', 'hashtaggable']));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['hashtag', 'addedBy', 'hashtaggable']);
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
            'index' => Pages\ListContentHashtags::route('/'),
            'create' => Pages\CreateContentHashtag::route('/create'),
            'edit' => Pages\EditContentHashtag::route('/{record}/edit'),
        ];
    }
}
