<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserBookmarkResource\Pages;
use App\Models\UserBookmark;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserBookmarkResource extends Resource
{
    protected static ?string $model = UserBookmark::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationGroup = 'Sistema Social';

    protected static ?string $modelLabel = 'Marcador';

    protected static ?string $pluralModelLabel = 'Marcadores';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Marcador')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\TextInput::make('bookmarkable_type')
                            ->label('Tipo de Contenido')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ej: App\\Models\\Post, App\\Models\\Topic'),
                        
                        Forms\Components\TextInput::make('bookmarkable_id')
                            ->label('ID del Contenido')
                            ->required()
                            ->numeric(),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->maxLength(255)
                            ->helperText('Título personalizado del marcador'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->helperText('Notas personales sobre este marcador'),
                    ])->columns(2),

                Forms\Components\Section::make('Organización')
                    ->schema([
                        Forms\Components\TextInput::make('collection_name')
                            ->label('Colección')
                            ->maxLength(100)
                            ->helperText('Nombre de la colección o carpeta'),
                        
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->placeholder('Añadir etiquetas')
                            ->helperText('Etiquetas para organizar los marcadores'),
                        
                        Forms\Components\Select::make('visibility')
                            ->label('Visibilidad')
                            ->options([
                                'private' => 'Privado',
                                'public' => 'Público',
                                'shared' => 'Compartido',
                            ])
                            ->default('private')
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_favorite')
                            ->label('Favorito')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Metadatos')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos Adicionales')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir metadato'),
                        
                        Forms\Components\DateTimePicker::make('bookmarked_at')
                            ->label('Fecha de Marcado')
                            ->default(now())
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50)
                    ->placeholder('Sin título'),
                
                Tables\Columns\TextColumn::make('bookmarkable_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('collection_name')
                    ->label('Colección')
                    ->searchable()
                    ->placeholder('Sin colección')
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('visibility')
                    ->label('Visibilidad')
                    ->colors([
                        'gray' => 'private',
                        'success' => 'public',
                        'warning' => 'shared',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'private' => 'Privado',
                        'public' => 'Público',
                        'shared' => 'Compartido',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\IconColumn::make('is_favorite')
                    ->label('Favorito')
                    ->boolean()
                    ->trueIcon('heroicon-o-heart')
                    ->falseIcon('heroicon-o-heart'),
                
                Tables\Columns\TextColumn::make('bookmarked_at')
                    ->label('Marcado')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('last_accessed_at')
                    ->label('Último Acceso')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Nunca')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bookmarkable_type')
                    ->label('Tipo de Contenido')
                    ->options([
                        'App\\Models\\Topic' => 'Tema',
                        'App\\Models\\Post' => 'Post',
                        'App\\Models\\Project' => 'Proyecto',
                        'App\\Models\\User' => 'Usuario',
                    ]),
                
                Tables\Filters\SelectFilter::make('visibility')
                    ->options([
                        'private' => 'Privado',
                        'public' => 'Público',
                        'shared' => 'Compartido',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_favorite')
                    ->label('Favorito'),
                
                Tables\Filters\Filter::make('has_collection')
                    ->label('Con Colección')
                    ->query(fn ($query) => $query->whereNotNull('collection_name')),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Recientes (7 días)')
                    ->query(fn ($query) => $query->where('bookmarked_at', '>=', now()->subWeek())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_favorite')
                    ->label(fn (UserBookmark $record) => $record->is_favorite ? 'Quitar Favorito' : 'Marcar Favorito')
                    ->icon(fn (UserBookmark $record) => $record->is_favorite ? 'heroicon-o-heart-slash' : 'heroicon-o-heart')
                    ->color(fn (UserBookmark $record) => $record->is_favorite ? 'gray' : 'danger')
                    ->action(fn (UserBookmark $record) => $record->update(['is_favorite' => !$record->is_favorite])),
                
                Tables\Actions\Action::make('access')
                    ->label('Registrar Acceso')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->action(fn (UserBookmark $record) => $record->update(['last_accessed_at' => now()])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('add_to_collection')
                        ->label('Añadir a Colección')
                        ->icon('heroicon-o-folder-plus')
                        ->form([
                            Forms\Components\TextInput::make('collection_name')
                                ->label('Nombre de la Colección')
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['collection_name' => $data['collection_name']]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('bookmarked_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserBookmarks::route('/'),
            'create' => Pages\CreateUserBookmark::route('/create'),
            'edit' => Pages\EditUserBookmark::route('/{record}/edit'),
        ];
    }
}