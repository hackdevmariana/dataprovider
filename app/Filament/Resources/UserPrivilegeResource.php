<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserPrivilegeResource\Pages;
use App\Models\UserPrivilege;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class UserPrivilegeResource extends Resource
{
    protected static ?string $model = UserPrivilege::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Social System';

    protected static ?string $modelLabel = 'Privilegio de Usuario';

    protected static ?string $pluralModelLabel = 'Privilegios de Usuarios';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('privilege_type')
                            ->label('Tipo de Privilegio')
                            ->options([
                                'posting' => 'Publicación',
                                'voting' => 'Votación',
                                'moderation' => 'Moderación',
                                'verification' => 'Verificación',
                                'administration' => 'Administración',
                                'content_creation' => 'Creación de Contenido',
                                'expert_answers' => 'Respuestas de Experto',
                                'project_approval' => 'Aprobación de Proyectos',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('scope')
                            ->label('Ámbito')
                            ->options([
                                'global' => 'Global',
                                'topic' => 'Tema',
                                'cooperative' => 'Cooperativa',
                                'project' => 'Proyecto',
                                'region' => 'Región',
                            ])
                            ->default('global')
                            ->reactive()
                            ->required(),
                        
                        Forms\Components\TextInput::make('scope_id')
                            ->label('ID del Ámbito')
                            ->numeric()
                            ->visible(fn ($get) => $get('scope') !== 'global')
                            ->helperText('ID específico del ámbito seleccionado'),
                        
                        Forms\Components\Select::make('level')
                            ->label('Nivel')
                            ->options([
                                1 => 'Nivel 1 - Básico',
                                2 => 'Nivel 2 - Intermedio',
                                3 => 'Nivel 3 - Avanzado',
                                4 => 'Nivel 4 - Experto',
                                5 => 'Nivel 5 - Máximo',
                            ])
                            ->default(1)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Permisos y Límites')
                    ->schema([
                        Forms\Components\TagsInput::make('permissions')
                            ->label('Permisos Específicos')
                            ->helperText('Lista de permisos otorgados con este privilegio')
                            ->placeholder('Ejemplo: create_posts, edit_content, moderate_comments'),
                        
                        Forms\Components\KeyValue::make('limits')
                            ->label('Límites de Uso')
                            ->keyLabel('Tipo de Límite')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir límite')
                            ->helperText('Ej: posts_per_day: 10, votes_per_hour: 50'),
                    ]),

                Forms\Components\Section::make('Requisitos y Fechas')
                    ->schema([
                        Forms\Components\TextInput::make('reputation_required')
                            ->label('Reputación Requerida')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Reputación mínima necesaria para mantener este privilegio'),
                        
                        Forms\Components\DateTimePicker::make('granted_at')
                            ->label('Fecha de Concesión')
                            ->required()
                            ->default(now()),
                        
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->helperText('Dejar vacío si el privilegio es permanente'),
                        
                        Forms\Components\Select::make('granted_by')
                            ->label('Otorgado por')
                            ->relationship('grantor', 'name')
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Razón')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                        
                        Forms\Components\Textarea::make('reason')
                            ->label('Razón de Concesión')
                            ->rows(3)
                            ->helperText('Explicación de por qué se otorgó este privilegio'),
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
                
                Tables\Columns\BadgeColumn::make('privilege_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'posting',
                        'success' => 'voting',
                        'warning' => 'moderation',
                        'danger' => 'verification',
                        'info' => 'administration',
                        'gray' => 'content_creation',
                        'purple' => 'expert_answers',
                        'orange' => 'project_approval',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'posting' => 'Publicación',
                        'voting' => 'Votación',
                        'moderation' => 'Moderación',
                        'verification' => 'Verificación',
                        'administration' => 'Administración',
                        'content_creation' => 'Creación',
                        'expert_answers' => 'Experto',
                        'project_approval' => 'Aprobación',
                    }),
                
                Tables\Columns\BadgeColumn::make('scope')
                    ->label('Ámbito')
                    ->colors([
                        'primary' => 'global',
                        'success' => 'topic',
                        'warning' => 'cooperative',
                        'danger' => 'project',
                        'info' => 'region',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'global' => 'Global',
                        'topic' => 'Tema',
                        'cooperative' => 'Cooperativa',
                        'project' => 'Proyecto',
                        'region' => 'Región',
                    }),
                
                Tables\Columns\TextColumn::make('scope_id')
                    ->label('ID Ámbito')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\BadgeColumn::make('level')
                    ->label('Nivel')
                    ->colors([
                        'gray' => 1,
                        'blue' => 2,
                        'yellow' => 3,
                        'orange' => 4,
                        'red' => 5,
                    ])
                    ->formatStateUsing(fn (int $state): string => "Nivel $state"),
                
                Tables\Columns\TextColumn::make('reputation_required')
                    ->label('Rep. Req.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                
                Tables\Columns\TextColumn::make('granted_at')
                    ->label('Otorgado')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Nunca')
                    ->color(fn ($record) => $record && $record->isExpired() ? 'danger' : null),
                
                Tables\Columns\TextColumn::make('grantor.name')
                    ->label('Otorgado por')
                    ->placeholder('Sistema')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('privilege_type')
                    ->label('Tipo de Privilegio')
                    ->options([
                        'posting' => 'Publicación',
                        'voting' => 'Votación',
                        'moderation' => 'Moderación',
                        'verification' => 'Verificación',
                        'administration' => 'Administración',
                        'content_creation' => 'Creación de Contenido',
                        'expert_answers' => 'Respuestas de Experto',
                        'project_approval' => 'Aprobación de Proyectos',
                    ]),
                
                Tables\Filters\SelectFilter::make('scope')
                    ->label('Ámbito')
                    ->options([
                        'global' => 'Global',
                        'topic' => 'Tema',
                        'cooperative' => 'Cooperativa',
                        'project' => 'Proyecto',
                        'region' => 'Región',
                    ]),
                
                Tables\Filters\SelectFilter::make('level')
                    ->label('Nivel')
                    ->options([
                        1 => 'Nivel 1',
                        2 => 'Nivel 2',
                        3 => 'Nivel 3',
                        4 => 'Nivel 4',
                        5 => 'Nivel 5',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
                
                Tables\Filters\Filter::make('expired')
                    ->label('Expirados')
                    ->query(fn ($query) => $query->whereNotNull('expires_at')->where('expires_at', '<', now())),
                
                Tables\Filters\Filter::make('valid')
                    ->label('Válidos')
                    ->query(fn ($query) => $query->where('is_active', true)->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('revoke')
                    ->label('Revocar')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (UserPrivilege $record) => $record->update(['is_active' => false]))
                    ->visible(fn (UserPrivilege $record) => $record->is_active),
                
                Tables\Actions\Action::make('activate')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (UserPrivilege $record) => $record->update(['is_active' => true]))
                    ->visible(fn (UserPrivilege $record) => !$record->is_active),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('granted_at', 'desc');
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
            'index' => Pages\ListUserPrivileges::route('/'),
            'create' => Pages\CreateUserPrivilege::route('/create'),
            'edit' => Pages\EditUserPrivilege::route('/{record}/edit'),
        ];
    }
}