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

    protected static ?string $navigationGroup = 'Social System';

    protected static ?string $modelLabel = 'Marcador';

    protected static ?string $pluralModelLabel = 'Marcadores';

    protected static ?int $navigationSort = 8;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
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
                            ->helperText('Ej: App\\Models\\Cooperative, App\\Models\\Region'),
                        
                        Forms\Components\TextInput::make('bookmarkable_id')
                            ->label('ID del Contenido')
                            ->required()
                            ->numeric(),
                        
                        Forms\Components\TextInput::make('folder')
                            ->label('Carpeta')
                            ->maxLength(255)
                            ->helperText('Carpeta para organizar el marcador'),
                        
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->placeholder('Añadir etiquetas')
                            ->helperText('Etiquetas para organizar los marcadores'),
                    ])->columns(2),

                Forms\Components\Section::make('Organización y Notas')
                    ->schema([
                        Forms\Components\Textarea::make('personal_notes')
                            ->label('Notas Personales')
                            ->rows(3)
                            ->helperText('Notas personales sobre este marcador'),
                        
                        Forms\Components\Select::make('priority')
                            ->label('Prioridad')
                            ->options([
                                0 => 'Normal',
                                1 => 'Importante',
                                2 => 'Urgente',
                            ])
                            ->default(0)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Público')
                            ->default(false)
                            ->helperText('Si está marcado, otros usuarios pueden ver este bookmark'),
                    ])->columns(2),

                Forms\Components\Section::make('Recordatorios')
                    ->schema([
                        Forms\Components\Toggle::make('reminder_enabled')
                            ->label('Habilitar Recordatorio')
                            ->default(false),
                        
                        Forms\Components\DateTimePicker::make('reminder_date')
                            ->label('Fecha del Recordatorio')
                            ->visible(fn (Forms\Get $get) => $get('reminder_enabled')),
                        
                        Forms\Components\Select::make('reminder_frequency')
                            ->label('Frecuencia')
                            ->options([
                                'once' => 'Una vez',
                                'weekly' => 'Semanal',
                                'monthly' => 'Mensual',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('reminder_enabled')),
                    ])->columns(2),

                Forms\Components\Section::make('Métricas')
                    ->schema([
                        Forms\Components\TextInput::make('access_count')
                            ->label('Contador de Accesos')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\DateTimePicker::make('last_accessed_at')
                            ->label('Último Acceso'),
                    ])->columns(2),
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
                
                Tables\Columns\TextColumn::make('bookmarkable_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('folder')
                    ->label('Carpeta')
                    ->searchable()
                    ->placeholder('Sin carpeta')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'gray',
                        1 => 'warning',
                        2 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Normal',
                        1 => 'Importante',
                        2 => 'Urgente',
                        default => 'Normal',
                    }),
                
                Tables\Columns\IconColumn::make('reminder_enabled')
                    ->label('Recordatorio')
                    ->boolean()
                    ->trueIcon('heroicon-o-bell')
                    ->falseIcon('heroicon-o-bell-slash'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash'),
                
                Tables\Columns\TextColumn::make('access_count')
                    ->label('Accesos')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
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
                        'App\\Models\\Cooperative' => 'Cooperativa',
                        'App\\Models\\Region' => 'Región',
                        'App\\Models\\TopicPost' => 'Post',
                        'App\\Models\\EnergyInstallation' => 'Instalación',
                        'App\\Models\\PlantSpecies' => 'Especie',
                    ]),
                
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options([
                        0 => 'Normal',
                        1 => 'Importante',
                        2 => 'Urgente',
                    ]),
                
                Tables\Filters\TernaryFilter::make('reminder_enabled')
                    ->label('Con Recordatorio'),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Públicos'),
                
                Tables\Filters\Filter::make('has_folder')
                    ->label('Con Carpeta')
                    ->query(fn ($query) => $query->whereNotNull('folder')),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Recientes (7 días)')
                    ->query(fn ($query) => $query->where('created_at', '>=', now()->subWeek())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_reminder')
                    ->label(fn (UserBookmark $record) => $record->reminder_enabled ? 'Desactivar Recordatorio' : 'Activar Recordatorio')
                    ->icon(fn (UserBookmark $record) => $record->reminder_enabled ? 'heroicon-o-bell-slash' : 'heroicon-o-bell')
                    ->color(fn (UserBookmark $record) => $record->reminder_enabled ? 'gray' : 'warning')
                    ->action(fn (UserBookmark $record) => $record->update(['reminder_enabled' => !$record->reminder_enabled])),
                
                Tables\Actions\Action::make('access')
                    ->label('Registrar Acceso')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->action(fn (UserBookmark $record) => $record->incrementAccess()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('add_to_folder')
                        ->label('Añadir a Carpeta')
                        ->icon('heroicon-o-folder-plus')
                        ->form([
                            Forms\Components\TextInput::make('folder')
                                ->label('Nombre de la Carpeta')
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $records) {
                                $records->update(['folder' => $data['folder']]);
                            }
                        }),
                    Tables\Actions\BulkAction::make('set_priority')
                        ->label('Establecer Prioridad')
                        ->icon('heroicon-o-flag')
                        ->form([
                            Forms\Components\Select::make('priority')
                                ->label('Prioridad')
                                ->options([
                                    0 => 'Normal',
                                    1 => 'Importante',
                                    2 => 'Urgente',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $records) {
                                $records->update(['priority' => $data['priority']]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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