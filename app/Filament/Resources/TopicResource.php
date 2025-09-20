<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopicResource\Pages;
use App\Models\Topic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TopicResource extends Resource
{
    protected static ?string $model = Topic::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Usuarios y Social';

    protected static ?string $modelLabel = 'Tema';

    protected static ?string $pluralModelLabel = 'Temas';

    protected static ?int $navigationSort = 1;

    
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
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\Select::make('creator_id')
                            ->label('Creador')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('visibility')
                            ->label('Visibilidad')
                            ->options([
                                'public' => 'Público',
                                'private' => 'Privado',
                                'restricted' => 'Restringido',
                                'invite_only' => 'Solo por Invitación',
                                'archived' => 'Archivado',
                            ])
                            ->default('public')
                            ->required(),
                        
                        Forms\Components\Select::make('join_policy')
                            ->label('Política de Unión')
                            ->options([
                                'open' => 'Abierto',
                                'approval_required' => 'Requiere Aprobación',
                                'invite_only' => 'Solo por Invitación',
                                'closed' => 'Cerrado',
                            ])
                            ->default('open')
                            ->required(),
                        
                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'technology' => 'Tecnología',
                                'legislation' => 'Legislación',
                                'financing' => 'Financiación',
                                'installation' => 'Instalación',
                                'cooperative' => 'Cooperativa',
                                'market' => 'Mercado',
                                'efficiency' => 'Eficiencia',
                                'diy' => 'Hazlo Tú Mismo',
                                'news' => 'Noticias',
                                'beginners' => 'Principiantes',
                                'professional' => 'Profesional',
                                'regional' => 'Regional',
                                'research' => 'Investigación',
                                'storage' => 'Almacenamiento',
                                'grid' => 'Red Eléctrica',
                                'policy' => 'Políticas',
                                'sustainability' => 'Sostenibilidad',
                                'innovation' => 'Innovación',
                                'general' => 'General',
                            ])
                            ->default('general')
                            ->required(),
                        
                        Forms\Components\Select::make('difficulty_level')
                            ->label('Nivel de Dificultad')
                            ->options([
                                'beginner' => 'Principiante',
                                'intermediate' => 'Intermedio',
                                'advanced' => 'Avanzado',
                                'expert' => 'Experto',
                            ])
                            ->default('beginner')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Apariencia')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Icono')
                            ->maxLength(255),
                        
                        Forms\Components\ColorPicker::make('color')
                            ->label('Color')
                            ->default('#3B82F6'),
                        
                        Forms\Components\FileUpload::make('banner_image')
                            ->label('Imagen de Banner')
                            ->image()
                            ->maxSize(5120),
                        
                        Forms\Components\FileUpload::make('avatar_image')
                            ->label('Avatar')
                            ->image()
                            ->maxSize(2048)
                            ->avatar(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Avanzada')
                    ->schema([
                        Forms\Components\Toggle::make('requires_approval')
                            ->label('Requiere Aprobación')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('allow_polls')
                            ->label('Permitir Encuestas')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('allow_images')
                            ->label('Permitir Imágenes')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('allow_videos')
                            ->label('Permitir Videos')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('allow_links')
                            ->label('Permitir Enlaces')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('allow_files')
                            ->label('Permitir Archivos')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('enable_wiki')
                            ->label('Habilitar Wiki')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('enable_events')
                            ->label('Habilitar Eventos')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_image')
                    ->label('Avatar')
                    ->circular()
                    ->size(40),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'technology',
                        'success' => 'installation',
                        'warning' => 'financing',
                        'danger' => 'legislation',
                        'info' => 'research',
                        'gray' => 'general',
                    ]),
                
                Tables\Columns\BadgeColumn::make('visibility')
                    ->label('Visibilidad')
                    ->colors([
                        'success' => 'public',
                        'warning' => 'restricted',
                        'danger' => 'private',
                        'gray' => 'archived',
                    ]),
                
                Tables\Columns\TextColumn::make('members_count')
                    ->label('Miembros')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Posts')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('activity_score')
                    ->label('Actividad')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'technology' => 'Tecnología',
                        'legislation' => 'Legislación',
                        'financing' => 'Financiación',
                        'installation' => 'Instalación',
                        'cooperative' => 'Cooperativa',
                        'general' => 'General',
                    ]),
                
                Tables\Filters\SelectFilter::make('visibility')
                    ->options([
                        'public' => 'Público',
                        'private' => 'Privado',
                        'restricted' => 'Restringido',
                        'archived' => 'Archivado',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('feature')
                    ->label('Destacar')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(fn (Topic $record) => $record->update(['is_featured' => !$record->is_featured]))
                    ->visible(fn (Topic $record) => !$record->is_featured),
                
                Tables\Actions\Action::make('unfeature')
                    ->label('Quitar Destacado')
                    ->icon('heroicon-o-star')
                    ->color('gray')
                    ->action(fn (Topic $record) => $record->update(['is_featured' => false]))
                    ->visible(fn (Topic $record) => $record->is_featured),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('activity_score', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopics::route('/'),
            'create' => Pages\CreateTopic::route('/create'),
            'edit' => Pages\EditTopic::route('/{record}/edit'),
        ];
    }
}