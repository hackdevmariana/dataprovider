<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CooperativePostResource\Pages;
use App\Models\CooperativePost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CooperativePostResource extends Resource
{
    protected static ?string $model = CooperativePost::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Social System';

    protected static ?string $modelLabel = 'Post de Cooperativa';

    protected static ?string $pluralModelLabel = 'Posts de Cooperativas';

    protected static ?int $navigationSort = 5;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('cooperative_id')
                            ->label('Cooperativa')
                            ->relationship('cooperative', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('author_id')
                            ->label('Autor')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Contenido')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('post_type')
                            ->label('Tipo de Post')
                            ->options([
                                'announcement' => 'Anuncio',
                                'news' => 'Noticia',
                                'event' => 'Evento',
                                'discussion' => 'Discusión',
                                'update' => 'Actualización',
                            ])
                            ->default('announcement')
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'published' => 'Publicado',
                                'archived' => 'Archivado',
                            ])
                            ->default('draft')
                            ->required(),
                        
                        Forms\Components\Select::make('visibility')
                            ->label('Visibilidad')
                            ->options([
                                'public' => 'Público',
                                'members_only' => 'Solo Miembros',
                                'board_only' => 'Solo Junta',
                            ])
                            ->default('members_only')
                            ->required(),
                        
                        Forms\Components\Toggle::make('comments_enabled')
                            ->label('Comentarios Habilitados')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_pinned')
                            ->label('Fijado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado'),
                    ])->columns(3),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación'),
                        
                        Forms\Components\DateTimePicker::make('pinned_until')
                            ->label('Fijado Hasta')
                            ->visible(fn ($get) => $get('is_pinned')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cooperative.name')
                    ->label('Cooperativa')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Autor')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('post_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'announcement',
                        'info' => 'news',
                        'warning' => 'event',
                        'success' => 'discussion',
                        'gray' => 'update',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'announcement' => 'Anuncio',
                        'news' => 'Noticia',
                        'event' => 'Evento',
                        'discussion' => 'Discusión',
                        'update' => 'Actualización',
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                        'secondary' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                    }),
                
                Tables\Columns\BadgeColumn::make('visibility')
                    ->label('Visibilidad')
                    ->colors([
                        'success' => 'public',
                        'warning' => 'members_only',
                        'danger' => 'board_only',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => 'Público',
                        'members_only' => 'Miembros',
                        'board_only' => 'Junta',
                    }),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vistas')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Fijado')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cooperative_id')
                    ->label('Cooperativa')
                    ->relationship('cooperative', 'name'),
                
                Tables\Filters\SelectFilter::make('post_type')
                    ->options([
                        'announcement' => 'Anuncio',
                        'news' => 'Noticia',
                        'event' => 'Evento',
                        'discussion' => 'Discusión',
                        'update' => 'Actualización',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label('Fijado'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->action(fn (CooperativePost $record) => $record->publish())
                    ->visible(fn (CooperativePost $record) => $record->status === 'draft'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCooperativePosts::route('/'),
            'create' => Pages\CreateCooperativePost::route('/create'),
            'edit' => Pages\EditCooperativePost::route('/{record}/edit'),
        ];
    }
}