<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListItemResource\Pages;
use App\Filament\Resources\ListItemResource\RelationManagers;
use App\Models\ListItem;
use App\Models\UserList;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListItemResource extends Resource
{
    protected static ?string $navigationGroup = 'Sistema y Administración';
    protected static ?string $model = ListItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Elementos de Lista';
    protected static ?string $modelLabel = 'Elemento de Lista';
    protected static ?string $pluralModelLabel = 'Elementos de Lista';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Select::make('user_list_id')
                            ->required()
                            ->label('Lista de Usuario')
                            ->relationship('userList', 'name')
                            ->searchable()
                            ->preload(),
                        
                        Select::make('listable_type')
                            ->required()
                            ->label('Tipo de Contenido')
                            ->options([
                                'App\Models\NewsArticle' => 'Artículo de Noticias',
                                'App\Models\TopicPost' => 'Post de Tema',
                                'App\Models\CooperativePost' => 'Post de Cooperativa',
                                'App\Models\User' => 'Usuario',
                                'App\Models\Project' => 'Proyecto',
                            ])
                            ->searchable(),
                        
                        TextInput::make('listable_id')
                            ->required()
                            ->label('ID del Contenido')
                            ->numeric(),
                        
                        Select::make('added_by')
                            ->required()
                            ->label('Añadido por')
                            ->relationship('addedBy', 'name')
                            ->searchable()
                            ->preload(),
                        
                        TextInput::make('position')
                            ->label('Posición')
                            ->numeric()
                            ->default(1),
                    ])->columns(2),

                Section::make('Contenido Personalizado')
                    ->schema([
                        Textarea::make('personal_note')
                            ->label('Nota Personal')
                            ->rows(3),
                        
                        KeyValue::make('tags')
                            ->label('Tags')
                            ->keyLabel('Tag')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Tag'),
                        
                        TextInput::make('personal_rating')
                            ->label('Rating Personal')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(1)
                            ->maxValue(5),
                    ])->columns(1),

                Section::make('Configuración')
                    ->schema([
                        Select::make('added_mode')
                            ->required()
                            ->label('Modo de Adición')
                            ->options([
                                'manual' => 'Manual',
                                'auto_hashtag' => 'Auto-hashtag',
                                'auto_keyword' => 'Auto-palabra clave',
                                'auto_author' => 'Auto-autor',
                                'suggested' => 'Sugerido',
                                'imported' => 'Importado',
                            ])
                            ->default('manual'),
                        
                        Select::make('status')
                            ->required()
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'pending' => 'Pendiente',
                                'rejected' => 'Rechazado',
                                'archived' => 'Archivado',
                            ])
                            ->default('active'),
                        
                        Select::make('reviewed_by')
                            ->label('Revisado por')
                            ->relationship('reviewedBy', 'name')
                            ->searchable()
                            ->preload(),
                        
                        DateTimePicker::make('reviewed_at')
                            ->label('Fecha de Revisión'),
                    ])->columns(2),

                Section::make('Métricas')
                    ->schema([
                        TextInput::make('clicks_count')
                            ->label('Número de Clicks')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('likes_count')
                            ->label('Número de Likes')
                            ->numeric()
                            ->default(0),
                        
                        DateTimePicker::make('last_accessed_at')
                            ->label('Último Acceso'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                TextColumn::make('userList.name')
                    ->label('Lista')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('listable_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\NewsArticle' => 'Noticias',
                        'App\Models\TopicPost' => 'Post',
                        'App\Models\CooperativePost' => 'Cooperativa',
                        'App\Models\User' => 'Usuario',
                        'App\Models\Project' => 'Proyecto',
                        default => class_basename($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'App\Models\NewsArticle' => 'info',
                        'App\Models\TopicPost' => 'success',
                        'App\Models\CooperativePost' => 'warning',
                        'App\Models\User' => 'primary',
                        'App\Models\Project' => 'danger',
                        default => 'gray',
                    }),
                
                TextColumn::make('listable_id')
                    ->label('ID Contenido')
                    ->sortable(),
                
                TextColumn::make('addedBy.name')
                    ->label('Añadido por')
                    ->searchable()
                    ->limit(20),
                
                TextColumn::make('position')
                    ->label('Posición')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('personal_note')
                    ->label('Nota Personal')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                
                BadgeColumn::make('added_mode')
                    ->label('Modo')
                    ->colors([
                        'primary' => 'manual',
                        'success' => 'auto_hashtag',
                        'warning' => 'auto_keyword',
                        'info' => 'auto_author',
                        'gray' => 'suggested',
                        'danger' => 'imported',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'manual' => 'Manual',
                        'auto_hashtag' => 'Auto-hashtag',
                        'auto_keyword' => 'Auto-palabra',
                        'auto_author' => 'Auto-autor',
                        'suggested' => 'Sugerido',
                        'imported' => 'Importado',
                        default => $state,
                    }),
                
                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'rejected',
                        'gray' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'pending' => 'Pendiente',
                        'rejected' => 'Rechazado',
                        'archived' => 'Archivado',
                        default => $state,
                    }),
                
                TextColumn::make('personal_rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => $state ? "⭐ {$state}/5" : '-')
                    ->sortable(),
                
                TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('likes_count')
                    ->label('Likes')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('reviewedBy.name')
                    ->label('Revisado por')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('reviewed_at')
                    ->label('Fecha Revisión')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('last_accessed_at')
                    ->label('Último Acceso')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_list_id')
                    ->label('Lista')
                    ->relationship('userList', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('listable_type')
                    ->label('Tipo de Contenido')
                    ->options([
                        'App\Models\NewsArticle' => 'Noticias',
                        'App\Models\TopicPost' => 'Post',
                        'App\Models\CooperativePost' => 'Cooperativa',
                        'App\Models\User' => 'Usuario',
                        'App\Models\Project' => 'Proyecto',
                    ]),
                
                SelectFilter::make('added_mode')
                    ->label('Modo de Adición')
                    ->options([
                        'manual' => 'Manual',
                        'auto_hashtag' => 'Auto-hashtag',
                        'auto_keyword' => 'Auto-palabra clave',
                        'auto_author' => 'Auto-autor',
                        'suggested' => 'Sugerido',
                        'imported' => 'Importado',
                    ]),
                
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'pending' => 'Pendiente',
                        'rejected' => 'Rechazado',
                        'archived' => 'Archivado',
                    ]),
                
                SelectFilter::make('added_by')
                    ->label('Añadido por')
                    ->relationship('addedBy', 'name')
                    ->searchable()
                    ->preload(),
                
                TernaryFilter::make('has_personal_note')
                    ->label('Con Nota Personal')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('personal_note'),
                        false: fn (Builder $query) => $query->whereNull('personal_note'),
                    )
                    ->native(false),
                
                TernaryFilter::make('has_rating')
                    ->label('Con Rating')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('personal_rating'),
                        false: fn (Builder $query) => $query->whereNull('personal_rating'),
                    )
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListListItems::route('/'),
            'create' => Pages\CreateListItem::route('/create'),
            'edit' => Pages\EditListItem::route('/{record}/edit'),
        ];
    }
}
