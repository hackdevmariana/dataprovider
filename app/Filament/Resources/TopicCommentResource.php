<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopicCommentResource\Pages;
use App\Filament\Resources\TopicCommentResource\RelationManagers;
use App\Models\TopicComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TopicCommentResource extends Resource
{
    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $model = TopicComment::class;

    protected static ?string $navigationIcon = 'fas-comments';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Comentario')
                    ->schema([
                        Forms\Components\Select::make('topic_post_id')
                            ->label('Post del Tema')
                            ->relationship('topicPost', 'title')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('parent_id')
                            ->label('Comentario Padre')
                            ->relationship('parent', 'body')
                            ->searchable()
                            ->placeholder('Comentario raíz'),
                        Forms\Components\Textarea::make('body')
                            ->label('Contenido del Comentario')
                            ->required()
                            ->rows(4)
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Extracto')
                            ->rows(2)
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración del Comentario')
                    ->schema([
                        Forms\Components\Select::make('comment_type')
                            ->label('Tipo de Comentario')
                            ->options([
                                'comment' => 'Comentario Regular',
                                'answer' => 'Respuesta',
                                'solution' => 'Solución',
                                'clarification' => 'Aclaración',
                                'moderator_note' => 'Nota de Moderador',
                                'bot_response' => 'Respuesta Automática',
                                'system_message' => 'Mensaje del Sistema',
                            ])
                            ->default('comment')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'published' => 'Publicado',
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobado',
                                'hidden' => 'Oculto',
                                'deleted' => 'Eliminado',
                                'spam' => 'Spam',
                                'flagged' => 'Reportado',
                            ])
                            ->default('published')
                            ->required(),
                        Forms\Components\TextInput::make('language')
                            ->label('Idioma')
                            ->default('es')
                            ->maxLength(5),
                    ])->columns(3),

                Forms\Components\Section::make('Jerarquía y Threading')
                    ->schema([
                        Forms\Components\TextInput::make('depth')
                            ->label('Profundidad')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('thread_path')
                            ->label('Ruta del Hilo')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden de Visualización')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('children_count')
                            ->label('Número de Respuestas')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('descendants_count')
                            ->label('Total de Descendientes')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('root_comment_id')
                            ->label('ID del Comentario Raíz')
                            ->numeric(),
                    ])->columns(3),

                Forms\Components\Section::make('Estados Especiales')
                    ->schema([
                        Forms\Components\Toggle::make('is_best_answer')
                            ->label('Mejor Respuesta')
                            ->default(false),
                        Forms\Components\Toggle::make('is_author_reply')
                            ->label('Respuesta del Autor')
                            ->default(false),
                        Forms\Components\Toggle::make('is_moderator_reply')
                            ->label('Respuesta de Moderador')
                            ->default(false),
                        Forms\Components\Toggle::make('is_pinned')
                            ->label('Fijado')
                            ->default(false),
                        Forms\Components\Toggle::make('is_highlighted')
                            ->label('Destacado')
                            ->default(false),
                        Forms\Components\Toggle::make('is_edited')
                            ->label('Editado')
                            ->default(false),
                        Forms\Components\Toggle::make('is_deleted')
                            ->label('Eliminado')
                            ->default(false),
                    ])->columns(4),

                Forms\Components\Section::make('Métricas de Engagement')
                    ->schema([
                        Forms\Components\TextInput::make('upvotes_count')
                            ->label('Upvotes')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('downvotes_count')
                            ->label('Downvotes')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('score')
                            ->label('Puntuación')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('replies_count')
                            ->label('Respuestas')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('likes_count')
                            ->label('Likes')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('reports_count')
                            ->label('Reportes')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('helpful_votes')
                            ->label('Votos Útiles')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('not_helpful_votes')
                            ->label('Votos No Útiles')
                            ->numeric()
                            ->default(0),
                    ])->columns(4),

                Forms\Components\Section::make('Métricas de Calidad')
                    ->schema([
                        Forms\Components\TextInput::make('quality_score')
                            ->label('Puntuación de Calidad')
                            ->numeric()
                            ->step(0.01)
                            ->default(100),
                        Forms\Components\TextInput::make('helpfulness_score')
                            ->label('Puntuación de Utilidad')
                            ->numeric()
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\TextInput::make('relevance_score')
                            ->label('Puntuación de Relevancia')
                            ->numeric()
                            ->step(0.01)
                            ->default(100),
                        Forms\Components\TextInput::make('read_time_seconds')
                            ->label('Tiempo de Lectura (seg)')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('engagement_rate')
                            ->label('Tasa de Engagement')
                            ->numeric()
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\TextInput::make('ranking_score')
                            ->label('Puntuación de Ranking')
                            ->numeric()
                            ->step(0.01)
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Contenido Multimedia')
                    ->schema([
                        Forms\Components\KeyValue::make('images')
                            ->label('Imágenes')
                            ->keyLabel('Tipo')
                            ->valueLabel('URL'),
                        Forms\Components\KeyValue::make('attachments')
                            ->label('Archivos Adjuntos')
                            ->keyLabel('Nombre')
                            ->valueLabel('URL'),
                        Forms\Components\KeyValue::make('links')
                            ->label('Enlaces')
                            ->keyLabel('Texto')
                            ->valueLabel('URL'),
                        Forms\Components\KeyValue::make('code_snippets')
                            ->label('Fragmentos de Código')
                            ->keyLabel('Lenguaje')
                            ->valueLabel('Código'),
                    ])->columns(2),

                Forms\Components\Section::make('Moderación')
                    ->schema([
                        Forms\Components\KeyValue::make('moderation_flags')
                            ->label('Flags de Moderación')
                            ->keyLabel('Tipo')
                            ->valueLabel('Descripción'),
                        Forms\Components\Textarea::make('moderation_notes')
                            ->label('Notas de Moderación')
                            ->rows(3),
                        Forms\Components\TextInput::make('moderated_by')
                            ->label('Moderado por')
                            ->numeric()
                            ->placeholder('ID del usuario moderador'),
                        Forms\Components\DateTimePicker::make('moderated_at')
                            ->label('Fecha de Moderación'),
                    ])->columns(2),

                Forms\Components\Section::make('Información de Edición')
                    ->schema([
                        Forms\Components\DateTimePicker::make('last_edited_at')
                            ->label('Última Edición'),
                        Forms\Components\TextInput::make('last_edited_by')
                            ->label('Editado por')
                            ->numeric()
                            ->placeholder('ID del usuario editor'),
                        Forms\Components\Textarea::make('edit_reason')
                            ->label('Razón de la Edición')
                            ->rows(2),
                        Forms\Components\TextInput::make('edit_count')
                            ->label('Número de Ediciones')
                            ->numeric()
                            ->default(0),
                        Forms\Components\KeyValue::make('edit_history')
                            ->label('Historial de Ediciones')
                            ->keyLabel('Fecha')
                            ->valueLabel('Cambios'),
                    ])->columns(2),

                Forms\Components\Section::make('Etiquetado y Menciones')
                    ->schema([
                        Forms\Components\KeyValue::make('mentioned_users')
                            ->label('Usuarios Mencionados')
                            ->keyLabel('Usuario')
                            ->valueLabel('Contexto'),
                        Forms\Components\KeyValue::make('tags')
                            ->label('Etiquetas')
                            ->keyLabel('Categoría')
                            ->valueLabel('Valor'),
                        Forms\Components\Textarea::make('quote_text')
                            ->label('Texto Citado')
                            ->rows(2),
                        Forms\Components\Select::make('quoted_comment_id')
                            ->label('Comentario Citado')
                            ->relationship('parent', 'body')
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración de Notificaciones')
                    ->schema([
                        Forms\Components\Toggle::make('notify_parent_author')
                            ->label('Notificar al Autor del Padre')
                            ->default(true),
                        Forms\Components\Toggle::make('notify_post_author')
                            ->label('Notificar al Autor del Post')
                            ->default(true),
                        Forms\Components\Toggle::make('notify_followers')
                            ->label('Notificar a Seguidores')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Información de Actividad')
                    ->schema([
                        Forms\Components\DateTimePicker::make('last_activity_at')
                            ->label('Última Actividad'),
                        Forms\Components\TextInput::make('views_count')
                            ->label('Visualizaciones')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('unique_views_count')
                            ->label('Visualizaciones Únicas')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('controversy_score')
                            ->label('Puntuación de Controversia')
                            ->numeric()
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\DateTimePicker::make('hot_until')
                            ->label('Hot hasta'),
                    ])->columns(3),

                Forms\Components\Section::make('Información Técnica')
                    ->schema([
                        Forms\Components\TextInput::make('source')
                            ->label('Fuente')
                            ->maxLength(255)
                            ->default('web'),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->rows(2),
                        Forms\Components\KeyValue::make('creation_metadata')
                            ->label('Metadatos de Creación')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor'),
                        Forms\Components\TextInput::make('author_reputation_at_time')
                            ->label('Reputación del Autor')
                            ->numeric()
                            ->step(0.01)
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración de Threading Avanzado')
                    ->schema([
                        Forms\Components\KeyValue::make('thread_participants')
                            ->label('Participantes del Hilo')
                            ->keyLabel('Usuario')
                            ->valueLabel('Rol'),
                        Forms\Components\Toggle::make('breaks_thread')
                            ->label('Rompe el Hilo')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('thread_last_activity')
                            ->label('Última Actividad del Hilo'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración de Visualización')
                    ->schema([
                        Forms\Components\Toggle::make('collapsed_by_default')
                            ->label('Colapsado por Defecto')
                            ->default(false),
                        Forms\Components\Toggle::make('show_score')
                            ->label('Mostrar Puntuación')
                            ->default(true),
                        Forms\Components\Toggle::make('allow_replies')
                            ->label('Permitir Respuestas')
                            ->default(true),
                        Forms\Components\TextInput::make('max_reply_depth')
                            ->label('Profundidad Máxima de Respuestas')
                            ->numeric(),
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
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('topicPost.title')
                    ->label('Post del Tema')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('body')
                    ->label('Contenido')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('comment_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'comment' => 'gray',
                        'answer' => 'success',
                        'solution' => 'warning',
                        'clarification' => 'info',
                        'moderator_note' => 'danger',
                        'bot_response' => 'secondary',
                        'system_message' => 'dark',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'pending' => 'warning',
                        'approved' => 'info',
                        'hidden' => 'secondary',
                        'deleted' => 'danger',
                        'spam' => 'dark',
                        'flagged' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('depth')
                    ->label('Profundidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.body')
                    ->label('Comentario Padre')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('is_best_answer')
                    ->label('Mejor Respuesta')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_pinned')
                    ->label('Fijado')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Puntuación')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 10 => 'success',
                        $state > 0 => 'info',
                        $state === 0 => 'gray',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('upvotes_count')
                    ->label('Upvotes')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('downvotes_count')
                    ->label('Downvotes')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Respuestas')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('quality_score')
                    ->label('Calidad')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 70 => 'info',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('engagement_rate')
                    ->label('Engagement')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix('%')
                    ->sortable()
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 5.0 => 'success',
                        $state >= 2.0 => 'info',
                        $state >= 0.5 => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('language')
                    ->label('Idioma')
                    ->badge()
                    ->color('secondary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('comment_type')
                    ->label('Tipo de Comentario')
                    ->options([
                        'comment' => 'Comentario Regular',
                        'answer' => 'Respuesta',
                        'solution' => 'Solución',
                        'clarification' => 'Aclaración',
                        'moderator_note' => 'Nota de Moderador',
                        'bot_response' => 'Respuesta Automática',
                        'system_message' => 'Mensaje del Sistema',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'published' => 'Publicado',
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'hidden' => 'Oculto',
                        'deleted' => 'Eliminado',
                        'spam' => 'Spam',
                        'flagged' => 'Reportado',
                    ]),
                Tables\Filters\SelectFilter::make('language')
                    ->label('Idioma')
                    ->options([
                        'es' => 'Español',
                        'en' => 'Inglés',
                        'ca' => 'Catalán',
                        'eu' => 'Euskera',
                        'gl' => 'Gallego',
                    ]),
                Tables\Filters\Filter::make('best_answers')
                    ->label('Mejores Respuestas')
                    ->query(fn (Builder $query): Builder => $query->where('is_best_answer', true)),
                Tables\Filters\Filter::make('pinned_comments')
                    ->label('Comentarios Fijados')
                    ->query(fn (Builder $query): Builder => $query->where('is_pinned', true)),
                Tables\Filters\Filter::make('high_quality')
                    ->label('Alta Calidad')
                    ->query(fn (Builder $query): Builder => $query->where('quality_score', '>=', 90)),
                Tables\Filters\Filter::make('high_engagement')
                    ->label('Alto Engagement')
                    ->query(fn (Builder $query): Builder => $query->where('engagement_rate', '>=', 5.0)),
                Tables\Filters\Filter::make('recent_comments')
                    ->label('Comentarios Recientes')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),
                Tables\Filters\Filter::make('root_comments')
                    ->label('Comentarios Raíz')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),
                Tables\Filters\Filter::make('replies')
                    ->label('Solo Respuestas')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('parent_id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('fas-check-circle')
                    ->color('success')
                    ->visible(fn (TopicComment $record): bool => $record->status === 'pending')
                    ->action(function (TopicComment $record): void {
                        $record->update(['status' => 'approved']);
                        \Filament\Notifications\Notification::make()
                            ->title('Comentario aprobado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('pin')
                    ->label('Fijar')
                    ->icon('fas-thumbtack')
                    ->color('warning')
                    ->visible(fn (TopicComment $record): bool => !$record->is_pinned)
                    ->action(function (TopicComment $record): void {
                        $record->update(['is_pinned' => true]);
                        \Filament\Notifications\Notification::make()
                            ->title('Comentario fijado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('unpin')
                    ->label('Desfijar')
                    ->icon('fas-thumbtack')
                    ->color('gray')
                    ->visible(fn (TopicComment $record): bool => $record->is_pinned)
                    ->action(function (TopicComment $record): void {
                        $record->update(['is_pinned' => false]);
                        \Filament\Notifications\Notification::make()
                            ->title('Comentario desfijado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('mark_best_answer')
                    ->label('Marcar como Mejor')
                    ->icon('fas-star')
                    ->color('warning')
                    ->visible(fn (TopicComment $record): bool => !$record->is_best_answer)
                    ->action(function (TopicComment $record): void {
                        // Desmarcar otras respuestas como mejor respuesta
                        $record->topicPost->comments()
                             ->where('is_best_answer', true)
                             ->update(['is_best_answer' => false]);
                        
                        $record->update(['is_best_answer' => true]);
                        \Filament\Notifications\Notification::make()
                            ->title('Marcado como mejor respuesta')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Aprobar Seleccionados')
                        ->icon('fas-check-circle')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $records->each(fn (TopicComment $record) => $record->update(['status' => 'approved']));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' comentarios aprobados')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('pin')
                        ->label('Fijar Seleccionados')
                        ->icon('fas-thumbtack')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $records->each(fn (TopicComment $record) => $record->update(['is_pinned' => true]));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' comentarios fijados')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('hide')
                        ->label('Ocultar Seleccionados')
                        ->icon('fas-eye-slash')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $records->each(fn (TopicComment $record) => $record->update(['status' => 'hidden']));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' comentarios ocultos')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'topicPost', 'parent']));
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
            'index' => Pages\ListTopicComments::route('/'),
            'create' => Pages\CreateTopicComment::route('/create'),
            'edit' => Pages\EditTopicComment::route('/{record}/edit'),
        ];
    }

    /**
     * Obtener estadísticas rápidas para el dashboard
     */
    public static function getStats(): array
    {
        $total = TopicComment::count();
        $published = TopicComment::where('status', 'published')->count();
        $pending = TopicComment::where('status', 'pending')->count();
        $bestAnswers = TopicComment::where('is_best_answer', true)->count();
        $pinned = TopicComment::where('is_pinned', true)->count();
        $totalScore = TopicComment::sum('score');
        $avgQuality = TopicComment::avg('quality_score');
        
        return [
            'total_comments' => $total,
            'published_comments' => $published,
            'pending_comments' => $pending,
            'best_answers' => $bestAnswers,
            'pinned_comments' => $pinned,
            'total_score' => $totalScore,
            'average_quality' => round($avgQuality, 1),
        ];
    }
}
