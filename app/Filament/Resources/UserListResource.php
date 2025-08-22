<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserListResource\Pages;
use App\Models\UserList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class UserListResource extends Resource
{
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $model = UserList::class; // Assuming UserList model exists

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Form $form): Form
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
                            ->maxLength(255)
                            ->helperText('Se genera automáticamente si está vacío'),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario Propietario')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Apariencia')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Icono')
                            ->maxLength(50)
                            ->helperText('Nombre del icono (ej: star, heart)'),
                        Forms\Components\ColorPicker::make('color')
                            ->label('Color'),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Imagen de Portada')
                            ->image()
                            ->directory('user-lists/covers'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('list_type')
                            ->label('Tipo de Lista')
                            ->options([
                                'personal' => 'Personal',
                                'curated' => 'Curada',
                                'collaborative' => 'Colaborativa',
                                'template' => 'Plantilla',
                            ])
                            ->default('personal')
                            ->required(),
                        Forms\Components\Select::make('visibility')
                            ->label('Visibilidad')
                            ->options([
                                'public' => 'Pública',
                                'private' => 'Privada',
                                'followers' => 'Solo Seguidores',
                                'collaborative' => 'Colaborativa',
                            ])
                            ->default('public')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada'),
                        Forms\Components\Toggle::make('is_template')
                            ->label('Es Plantilla'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Funcionalidades')
                    ->schema([
                        Forms\Components\Toggle::make('allow_suggestions')
                            ->label('Permitir Sugerencias')
                            ->default(true),
                        Forms\Components\Toggle::make('allow_comments')
                            ->label('Permitir Comentarios')
                            ->default(true),
                        Forms\Components\Select::make('curation_mode')
                            ->label('Modo de Curación')
                            ->options([
                                'manual' => 'Manual',
                                'auto' => 'Automático',
                                'hybrid' => 'Híbrido',
                            ])
                            ->default('manual'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Tipos de Contenido Permitidos')
                    ->schema([
                        Forms\Components\CheckboxList::make('allowed_content_types')
                            ->label('Tipos de Contenido')
                            ->options([
                                'person' => 'Personas',
                                'event' => 'Eventos',
                                'news_article' => 'Noticias',
                                'point_of_interest' => 'Puntos de Interés',
                                'cooperative' => 'Cooperativas',
                                'energy_company' => 'Empresas Energéticas',
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Propietario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('list_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'personal' => 'gray',
                        'curated' => 'info',
                        'collaborative' => 'warning',
                        'template' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('visibility')
                    ->label('Visibilidad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'private' => 'danger',
                        'followers' => 'warning',
                        'collaborative' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Elementos')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('followers_count')
                    ->label('Seguidores')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('engagement_score')
                    ->label('Engagement')
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('list_type')
                    ->label('Tipo de Lista')
                    ->options([
                        'personal' => 'Personal',
                        'curated' => 'Curada',
                        'collaborative' => 'Colaborativa',
                        'template' => 'Plantilla',
                    ]),
                Tables\Filters\SelectFilter::make('visibility')
                    ->label('Visibilidad')
                    ->options([
                        'public' => 'Pública',
                        'private' => 'Privada',
                        'followers' => 'Solo Seguidores',
                        'collaborative' => 'Colaborativa',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado Activo'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacada'),
                Tables\Filters\TernaryFilter::make('is_template')
                    ->label('Es Plantilla'),
                Tables\Filters\Filter::make('has_items')
                    ->label('Con Elementos')
                    ->query(fn (Builder $query): Builder => $query->where('items_count', '>', 0)),
                Tables\Filters\Filter::make('has_followers')
                    ->label('Con Seguidores')
                    ->query(fn (Builder $query): Builder => $query->where('followers_count', '>', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_items')
                    ->label('Ver Elementos')
                    ->icon('heroicon-o-rectangle-stack')
                    ->url(fn (UserList $record): string => route('filament.admin.resources.user-lists.edit', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn (UserList $record): string => $record->is_featured ? 'Quitar Destacada' : 'Marcar Destacada')
                    ->icon(fn (UserList $record): string => $record->is_featured ? 'heroicon-o-star' : 'heroicon-o-star')
                    ->color(fn (UserList $record): string => $record->is_featured ? 'warning' : 'success')
                    ->action(function (UserList $record): void {
                        $record->update(['is_featured' => !$record->is_featured]);
                        \Filament\Notifications\Notification::make()
                            ->title($record->is_featured ? 'Lista marcada como destacada' : 'Lista quitada de destacadas')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggle_active')
                        ->label('Cambiar Estado Activo')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Collection $records): void {
                            $records->each(fn (UserList $record) => $record->update(['is_active' => !$record->is_active]));
                            \Filament\Notifications\Notification::make()
                                ->title('Estado de ' . $records->count() . ' listas actualizado')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('toggle_featured')
                        ->label('Cambiar Estado Destacada')
                        ->icon('heroicon-o-star')
                        ->action(function (Collection $records): void {
                            $records->each(fn (UserList $record) => $record->update(['is_featured' => !$record->is_featured]));
                            \Filament\Notifications\Notification::make()
                                ->title('Estado destacada de ' . $records->count() . ' listas actualizado')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'items']));
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
            'index' => Pages\ListUserLists::route('/'),
            'create' => Pages\CreateUserList::route('/create'),
            'edit' => Pages\EditUserList::route('/{record}/edit'),
        ];
    }
}
