<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopicPostResource\Pages;
use App\Models\TopicPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TopicPostResource extends Resource
{
    protected static ?string $model = TopicPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Social System';

    protected static ?string $modelLabel = 'Post del Tema';

    protected static ?string $pluralModelLabel = 'Posts de Temas';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Post')
                    ->schema([
                        Forms\Components\Select::make('topic_id')
                            ->label('Tema')
                            ->relationship('topic', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Autor')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\RichEditor::make('body')
                            ->label('Contenido')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Tipo y Estado')
                    ->schema([
                        Forms\Components\Select::make('post_type')
                            ->label('Tipo')
                            ->options([
                                'discussion' => 'Discusión',
                                'question' => 'Pregunta',
                                'announcement' => 'Anuncio',
                                'poll' => 'Encuesta',
                                'tutorial' => 'Tutorial',
                                'showcase' => 'Mostrar Trabajo',
                                'news' => 'Noticia',
                                'event' => 'Evento',
                                'help' => 'Ayuda',
                                'review' => 'Reseña',
                                'resource' => 'Recurso',
                                'job' => 'Trabajo',
                                'marketplace' => 'Mercado',
                                'case_study' => 'Caso de Estudio',
                                'research' => 'Investigación',
                            ])
                            ->default('discussion')
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'published' => 'Publicado',
                                'hidden' => 'Oculto',
                                'locked' => 'Bloqueado',
                                'deleted' => 'Eliminado',
                            ])
                            ->default('draft')
                            ->required(),
                        
                        Forms\Components\Select::make('priority')
                            ->label('Prioridad')
                            ->options([
                                'low' => 'Baja',
                                'normal' => 'Normal',
                                'high' => 'Alta',
                                'urgent' => 'Urgente',
                            ])
                            ->default('normal'),
                    ])->columns(3),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_pinned')
                            ->label('Fijado')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_anonymous')
                            ->label('Anónimo')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('allows_comments')
                            ->label('Permite Comentarios')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_solved')
                            ->label('Resuelto')
                            ->default(false)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'question'),
                        
                        Forms\Components\Toggle::make('notify_followers')
                            ->label('Notificar Seguidores')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Metadatos')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->placeholder('Añadir etiquetas'),
                        
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos Adicionales')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor'),
                        
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Fecha de Creación'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('topic.name')
                    ->label('Tema')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Autor')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('post_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'discussion',
                        'success' => 'question',
                        'warning' => 'announcement',
                        'info' => 'tutorial',
                        'gray' => 'showcase',
                        'purple' => 'help',
                        'orange' => 'review',
                        'green' => 'resource',
                        'blue' => 'job',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'discussion' => 'Discusión',
                        'question' => 'Pregunta',
                        'announcement' => 'Anuncio',
                        'poll' => 'Encuesta',
                        'tutorial' => 'Tutorial',
                        'showcase' => 'Mostrar',
                        'news' => 'Noticia',
                        'event' => 'Evento',
                        'help' => 'Ayuda',
                        'review' => 'Reseña',
                        'resource' => 'Recurso',
                        'job' => 'Trabajo',
                        'marketplace' => 'Mercado',
                        'case_study' => 'Caso de Estudio',
                        'research' => 'Investigación',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'published',
                        'gray' => 'draft',
                        'warning' => 'hidden',
                        'danger' => 'locked',
                        'secondary' => 'deleted',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'hidden' => 'Oculto',
                        'locked' => 'Bloqueado',
                        'deleted' => 'Eliminado',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comentarios')
                    ->counts('comments')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Fijado')
                    ->boolean()
                    ->trueIcon('heroicon-o-bookmark')
                    ->falseIcon('heroicon-o-bookmark'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star'),
                
                Tables\Columns\IconColumn::make('is_solved')
                    ->label('Resuelto')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('topic_id')
                    ->label('Tema')
                    ->relationship('topic', 'name')
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('post_type')
                    ->options([
                        'discussion' => 'Discusión',
                        'question' => 'Pregunta',
                        'announcement' => 'Anuncio',
                        'tutorial' => 'Tutorial',
                        'showcase' => 'Mostrar Trabajo',
                        'news' => 'Noticia',
                        'event' => 'Evento',
                        'help' => 'Ayuda',
                        'review' => 'Reseña',
                        'resource' => 'Recurso',
                        'job' => 'Trabajo',
                        'marketplace' => 'Mercado',
                        'case_study' => 'Caso de Estudio',
                        'research' => 'Investigación',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'hidden' => 'Oculto',
                        'locked' => 'Bloqueado',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label('Fijado'),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
                
                Tables\Filters\TernaryFilter::make('is_solved')
                    ->label('Resuelto'),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Recientes (7 días)')
                    ->query(fn ($query) => $query->where('created_at', '>=', now()->subWeek())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pin')
                    ->label(fn (TopicPost $record) => $record->is_pinned ? 'Desfijar' : 'Fijar')
                    ->icon('heroicon-o-bookmark')
                    ->color(fn (TopicPost $record) => $record->is_pinned ? 'gray' : 'warning')
                    ->action(fn (TopicPost $record) => $record->update(['is_pinned' => !$record->is_pinned])),
                
                Tables\Actions\Action::make('feature')
                    ->label(fn (TopicPost $record) => $record->is_featured ? 'Quitar Destacado' : 'Destacar')
                    ->icon('heroicon-o-star')
                    ->color(fn (TopicPost $record) => $record->is_featured ? 'gray' : 'warning')
                    ->action(fn (TopicPost $record) => $record->update(['is_featured' => !$record->is_featured])),
                
                Tables\Actions\Action::make('lock')
                    ->label(fn (TopicPost $record) => $record->status === 'locked' ? 'Desbloquear' : 'Bloquear')
                    ->icon('heroicon-o-lock-closed')
                    ->color(fn (TopicPost $record) => $record->status === 'locked' ? 'success' : 'danger')
                    ->action(function (TopicPost $record) {
                        $record->update([
                            'status' => $record->status === 'locked' ? 'published' : 'locked'
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publicar')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => 'published']);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopicPosts::route('/'),
            'create' => Pages\CreateTopicPost::route('/create'),
            'edit' => Pages\EditTopicPost::route('/{record}/edit'),
        ];
    }
}