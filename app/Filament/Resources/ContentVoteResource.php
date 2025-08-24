<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentVoteResource\Pages;
use App\Filament\Resources\ContentVoteResource\RelationManagers;
use App\Models\ContentVote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ContentVoteResource extends Resource
{
    protected static ?string $model = ContentVote::class;
    protected static ?string $navigationIcon = 'heroicon-s-pencil-square';
    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $modelLabel = 'Voto de Contenido';
    protected static ?string $pluralModelLabel = 'Votos de Contenido';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del Voto')
                ->description('Datos principales del voto')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->label('Usuario que Vota')
                                ->relationship('user', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('vote_type')
                                ->label('Tipo de Voto')
                                ->options([
                                    'upvote' => '👍 Upvote (Me gusta)',
                                    'downvote' => '👎 Downvote (No me gusta)',
                                ])
                                ->required()
                                ->default('upvote'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('vote_weight')
                                ->label('Peso del Voto')
                                ->numeric()
                                ->step(1)
                                ->minValue(1)
                                ->maxValue(20)
                                ->default(1)
                                ->helperText('Peso basado en la reputación del usuario'),

                            Forms\Components\Toggle::make('is_helpful_vote')
                                ->label('Voto Útil')
                                ->default(false)
                                ->helperText('Si este voto es especialmente útil'),
                        ]),
                ])
                ->collapsible(false),

            Forms\Components\Section::make('Contenido Votado')
                ->description('Información del contenido que se está votando')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('votable_type')
                                ->label('Tipo de Contenido')
                                ->options([
                                    'App\Models\NewsArticle' => '📰 Artículo de Noticia',
                                    'App\Models\EnergyInstallation' => '⚡ Instalación Energética',
                                    'App\Models\PlantSpecies' => '🌱 Especie Vegetal',
                                    'App\Models\ProductionRight' => '🏭 Derecho de Producción',
                                    'App\Models\UserGeneratedContent' => '👤 Contenido Generado por Usuario',
                                    'App\Models\TopicPost' => '💬 Post de Tema',
                                    'App\Models\TopicComment' => '💭 Comentario de Tema',
                                ])
                                ->required()
                                ->searchable()
                                ->reactive(),

                            Forms\Components\TextInput::make('votable_id')
                                ->label('ID del Contenido')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->helperText('ID del contenido específico que se está votando'),
                        ]),

                    Forms\Components\Placeholder::make('content_preview')
                        ->label('Vista Previa del Contenido')
                        ->content(function ($get) {
                            $type = $get('votable_type');
                            $id = $get('votable_id');
                            
                            if (!$type || !$id) {
                                return 'Selecciona el tipo y ID del contenido para ver la vista previa';
                            }
                            
                            try {
                                $content = $type::find($id);
                                if ($content) {
                                    $title = $content->title ?? $content->name ?? 'Sin título';
                                    return "📋 {$title}";
                                }
                                return '❌ Contenido no encontrado';
                            } catch (\Exception $e) {
                                return '❌ Error al cargar el contenido';
                            }
                        })
                        ->visible(fn ($get) => $get('votable_type') && $get('votable_id')),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Contexto del Voto')
                ->description('Información adicional sobre el voto')
                ->schema([
                    Forms\Components\Textarea::make('reason')
                        ->label('Razón del Voto')
                        ->rows(3)
                        ->placeholder('Especialmente importante para downvotes...')
                        ->helperText('Explicación del motivo del voto (opcional)')
                        ->visible(fn ($get) => $get('vote_type') === 'downvote'),

                    Forms\Components\KeyValue::make('metadata')
                        ->label('Metadatos del Voto')
                        ->keyLabel('Clave')
                        ->valueLabel('Valor')
                        ->addActionLabel('Añadir Metadato')
                        ->helperText('Información contextual del voto (dispositivo, navegador, etc.)'),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Validación')
                ->description('Estado de validación del voto')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('is_valid')
                                ->label('Voto Válido')
                                ->default(true)
                                ->helperText('Si el voto cumple las reglas de la comunidad'),

                            Forms\Components\Select::make('validated_by')
                                ->label('Validado Por')
                                ->relationship('validator', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Usuario que validó este voto'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Relaciones')
                ->description('Usuarios y contenido asociados')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Placeholder::make('user_info')
                                ->label('Información del Usuario')
                                ->content(function ($get) {
                                    $userId = $get('user_id');
                                    if (!$userId) return 'Selecciona un usuario';
                                    
                                    $user = \App\Models\User::find($userId);
                                    if ($user) {
                                        return "👤 {$user->name} ({$user->email})";
                                    }
                                    return 'Usuario no encontrado';
                                }),

                            Forms\Components\Placeholder::make('validator_info')
                                ->label('Información del Validador')
                                ->content(function ($get) {
                                    $validatorId = $get('validated_by');
                                    if (!$validatorId) return 'Sin validador';
                                    
                                    $validator = \App\Models\User::find($validatorId);
                                    if ($validator) {
                                        return "🔍 {$validator->name} ({$validator->email})";
                                    }
                                    return 'Validador no encontrado';
                                })
                                ->visible(fn ($get) => $get('validated_by')),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->tooltip(function ($record) {
                        return $record?->user?->name ?? 'Usuario no encontrado';
                    })
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('vote_type')
                    ->label('Tipo de Voto')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'success' => 'upvote',
                        'danger' => 'downvote',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'upvote' => '👍 Upvote',
                            'downvote' => '👎 Downvote',
                            default => $state,
                        };
                    }),

                Tables\Columns\TextColumn::make('vote_weight')
                    ->label('Peso')
                    ->searchable()
                    ->sortable()
                    ->numeric()
                    ->badge()
                    ->color(function ($record) {
                        if (!$record) return 'gray';
                        return match (true) {
                            $record->vote_weight >= 8 => 'success',
                            $record->vote_weight >= 5 => 'warning',
                            $record->vote_weight >= 2 => 'info',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('votable_type')
                    ->label('Tipo de Contenido')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('secondary')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\NewsArticle' => '📰 Noticia',
                        'App\Models\EnergyInstallation' => '⚡ Instalación',
                        'App\Models\PlantSpecies' => '🌱 Especie',
                        'App\Models\ProductionRight' => '🏭 Derecho',
                        'App\Models\UserGeneratedContent' => '👤 UGC',
                        'App\Models\TopicPost' => '💬 Post',
                        'App\Models\TopicComment' => '💭 Comentario',
                        default => class_basename($state),
                    }),

                Tables\Columns\TextColumn::make('votable_id')
                    ->label('ID Contenido')
                    ->searchable()
                    ->sortable()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('content_preview')
                    ->label('Contenido')
                    ->getStateUsing(function ($record) {
                        try {
                            $content = $record->votable;
                            if ($content) {
                                $title = $content->title ?? $content->name ?? 'Sin título';
                                return Str::limit($title, 40);
                            }
                            return 'Contenido no encontrado';
                        } catch (\Exception $e) {
                            return 'Error al cargar';
                        }
                    })
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function ($record) {
                        try {
                            $content = $record->votable;
                            if ($content) {
                                $title = $content->title ?? $content->name ?? 'Sin título';
                                return $title;
                            }
                            return 'Contenido no encontrado';
                        } catch (\Exception $e) {
                            return 'Error al cargar el contenido';
                        }
                    }),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Razón')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record?->reason ?? 'Sin razón';
                    })
                    ->visible(function ($record) {
                        return $record && $record->reason !== null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_helpful_vote')
                    ->label('Útil')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_valid')
                    ->label('Válido')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('validator.name')
                    ->label('Validado Por')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(function ($record) {
                        return $record && $record->validated_by !== null;
                    }),

                Tables\Columns\TextColumn::make('metadata_summary')
                    ->label('Metadatos')
                    ->getStateUsing(function ($record) {
                        if (!$record->metadata) return 'Sin metadatos';
                        
                        $summary = [];
                        if (isset($record->metadata['device_type'])) {
                            $summary[] = $record->metadata['device_type'];
                        }
                        if (isset($record->metadata['voting_context'])) {
                            $summary[] = $record->metadata['voting_context'];
                        }
                        if (isset($record->metadata['user_experience_level'])) {
                            $summary[] = $record->metadata['user_experience_level'];
                        }
                        
                        return implode(', ', $summary) ?: 'Metadatos disponibles';
                    })
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vote_type')
                    ->label('Tipo de Voto')
                    ->options([
                        'upvote' => '👍 Upvote',
                        'downvote' => '👎 Downvote',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('votable_type')
                    ->label('Tipo de Contenido')
                    ->options([
                        'App\Models\NewsArticle' => '📰 Artículos de Noticia',
                        'App\Models\EnergyInstallation' => '⚡ Instalaciones Energéticas',
                        'App\Models\PlantSpecies' => '🌱 Especies Vegetales',
                        'App\Models\ProductionRight' => '🏭 Derechos de Producción',
                        'App\Models\UserGeneratedContent' => '👤 Contenido Generado por Usuario',
                        'App\Models\TopicPost' => '💬 Posts de Tema',
                        'App\Models\TopicComment' => '💭 Comentarios de Tema',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\Filter::make('helpful_votes_only')
                    ->label('Solo Votos Útiles')
                    ->query(fn (Builder $query) => $query->where('is_helpful_vote', true))
                    ->toggle(),

                Tables\Filters\Filter::make('valid_votes_only')
                    ->label('Solo Votos Válidos')
                    ->query(fn (Builder $query) => $query->where('is_valid', true))
                    ->toggle(),

                Tables\Filters\Filter::make('high_weight_votes')
                    ->label('Votos de Alto Peso')
                    ->query(fn (Builder $query) => $query->where('vote_weight', '>', 5))
                    ->toggle(),

                Tables\Filters\Filter::make('downvotes_with_reason')
                    ->label('Downvotes con Razón')
                    ->query(fn (Builder $query) => $query->where('vote_type', 'downvote')->whereNotNull('reason'))
                    ->toggle(),

                Tables\Filters\Filter::make('validated_votes')
                    ->label('Votos Validados')
                    ->query(fn (Builder $query) => $query->whereNotNull('validated_by'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),

                Tables\Actions\Action::make('view_content')
                    ->label('Ver Contenido')
                    ->icon('heroicon-m-link')
                    ->color('info')
                    ->action(function (ContentVote $record): void {
                        \Filament\Notifications\Notification::make()
                            ->title('Ver Contenido')
                            ->body("Redirigiendo al contenido: {$record->votable_type} #{$record->votable_id}")
                            ->info()
                            ->send();
                    })
                    ->tooltip('Ver el contenido que se está votando'),

                Tables\Actions\Action::make('toggle_helpful')
                    ->label('Cambiar Útil')
                    ->icon('heroicon-o-star')
                    ->color(function ($record) {
                        if (!$record) return 'gray';
                        return $record->is_helpful_vote ? 'gray' : 'warning';
                    })
                    ->action(function (ContentVote $record): void {
                        $record->update(['is_helpful_vote' => !$record->is_helpful_vote]);
                        
                        $status = $record->is_helpful_vote ? 'marcado como útil' : 'desmarcado como útil';
                        \Filament\Notifications\Notification::make()
                            ->title('Estado Actualizado')
                            ->body("El voto ha sido {$status}")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Cambiar el estado de voto útil'),

                Tables\Actions\Action::make('toggle_valid')
                    ->label('Cambiar Válido')
                    ->icon('heroicon-o-check-circle')
                    ->color(function ($record) {
                        if (!$record) return 'gray';
                        return $record->is_valid ? 'danger' : 'success';
                    })
                    ->action(function (ContentVote $record): void {
                        $record->update(['is_valid' => !$record->is_valid]);
                        
                        $status = $record->is_valid ? 'marcado como válido' : 'marcado como inválido';
                        \Filament\Notifications\Notification::make()
                            ->title('Estado Actualizado')
                            ->body("El voto ha sido {$status}")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Cambiar el estado de validez del voto'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Eliminar Seleccionados'),
                
                Tables\Actions\BulkAction::make('mark_as_helpful')
                    ->label('Marcar como Útiles')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(function (Collection $records): void {
                        $records->each(function ($record) {
                            $record->update(['is_helpful_vote' => true]);
                        });
                        $count = $records->count();
                        \Filament\Notifications\Notification::make()
                            ->title('Votos Marcados')
                            ->body("Se han marcado {$count} votos como útiles")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Marcar votos seleccionados como útiles'),
                
                Tables\Actions\BulkAction::make('mark_as_valid')
                    ->label('Marcar como Válidos')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Collection $records): void {
                        $records->each(function ($record) {
                            $record->update(['is_valid' => true]);
                        });
                        $count = $records->count();
                        \Filament\Notifications\Notification::make()
                            ->title('Votos Validados')
                            ->body("Se han marcado {$count} votos como válidos")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Marcar votos seleccionados como válidos'),
                
                Tables\Actions\BulkAction::make('export_vote_stats')
                    ->label('Exportar Estadísticas')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (Collection $records): void {
                        $count = $records->count();
                        $upvotes = $records->where('vote_type', 'upvote')->count();
                        $downvotes = $records->where('vote_type', 'downvote')->count();
                        $avgWeight = $records->avg('vote_weight');
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Estadísticas Exportadas')
                            ->body("{$count} votos: {$upvotes} upvotes, {$downvotes} downvotes, peso medio: " . number_format($avgWeight, 1))
                            ->success()
                            ->send();
                    })
                    ->tooltip('Exportar estadísticas de los votos seleccionados'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([25, 50, 100])
            ->searchable()
            ->searchPlaceholder('Buscar por usuario, tipo de voto o contenido...');
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
            'index' => Pages\ListContentVotes::route('/'),
            'create' => Pages\CreateContentVote::route('/create'),
            'edit' => Pages\EditContentVote::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'validator']) // Cargar relaciones por defecto
            ->whereNotNull('user_id') // Solo registros con usuario válido
            ->whereNotNull('votable_type') // Solo registros con tipo de contenido válido
            ->whereNotNull('votable_id'); // Solo registros con ID de contenido válido
    }
}
