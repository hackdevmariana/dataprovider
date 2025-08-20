<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopicMembershipResource\Pages;
use App\Models\TopicMembership;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TopicMembershipResource extends Resource
{
    protected static ?string $model = TopicMembership::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Comunidades';

    protected static ?string $modelLabel = 'Membresía';

    protected static ?string $pluralModelLabel = 'Membresías';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Membresía')
                    ->schema([
                        Forms\Components\Select::make('topic_id')
                            ->label('Tema')
                            ->relationship('topic', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options([
                                'member' => 'Miembro',
                                'moderator' => 'Moderador',
                                'admin' => 'Administrador',
                                'owner' => 'Propietario',
                            ])
                            ->default('member')
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'pending' => 'Pendiente',
                                'suspended' => 'Suspendido',
                                'banned' => 'Baneado',
                                'left' => 'Ha dejado el tema',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('can_post')
                            ->label('Puede Publicar')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('can_comment')
                            ->label('Puede Comentar')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('can_moderate')
                            ->label('Puede Moderar')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('receives_notifications')
                            ->label('Recibe Notificaciones')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('joined_at')
                            ->label('Fecha de Unión')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('last_activity_at')
                            ->label('Última Actividad'),
                        
                        Forms\Components\DateTimePicker::make('suspended_until')
                            ->label('Suspendido Hasta'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('topic.name')
                    ->label('Tema')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Rol')
                    ->colors([
                        'success' => 'owner',
                        'warning' => 'admin',
                        'info' => 'moderator',
                        'gray' => 'member',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'owner' => 'Propietario',
                        'admin' => 'Administrador',
                        'moderator' => 'Moderador',
                        'member' => 'Miembro',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => ['suspended', 'banned'],
                        'gray' => 'left',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'pending' => 'Pendiente',
                        'suspended' => 'Suspendido',
                        'banned' => 'Baneado',
                        'left' => 'Ha dejado',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('can_post')
                    ->label('Puede Publicar')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('joined_at')
                    ->label('Se Unió')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Última Actividad')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Nunca')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'owner' => 'Propietario',
                        'admin' => 'Administrador',
                        'moderator' => 'Moderador',
                        'member' => 'Miembro',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Activo',
                        'pending' => 'Pendiente',
                        'suspended' => 'Suspendido',
                        'banned' => 'Baneado',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),
                
                Tables\Filters\Filter::make('recent_members')
                    ->label('Miembros Recientes (7 días)')
                    ->query(fn ($query) => $query->where('joined_at', '>=', now()->subWeek())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('promote')
                    ->label('Promover')
                    ->icon('heroicon-o-arrow-up')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->label('Nuevo Rol')
                            ->options([
                                'moderator' => 'Moderador',
                                'admin' => 'Administrador',
                            ])
                            ->required(),
                    ])
                    ->action(function (TopicMembership $record, array $data) {
                        $record->update(['role' => $data['role']]);
                    })
                    ->visible(fn (TopicMembership $record) => $record->role === 'member'),
                
                Tables\Actions\Action::make('suspend')
                    ->label('Suspender')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->form([
                        Forms\Components\DateTimePicker::make('suspended_until')
                            ->label('Suspendido Hasta')
                            ->required(),
                    ])
                    ->action(function (TopicMembership $record, array $data) {
                        $record->update([
                            'status' => 'suspended',
                            'suspended_until' => $data['suspended_until'],
                        ]);
                    })
                    ->visible(fn (TopicMembership $record) => $record->status === 'active'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('joined_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopicMemberships::route('/'),
            'create' => Pages\CreateTopicMembership::route('/create'),
            'edit' => Pages\EditTopicMembership::route('/{record}/edit'),
        ];
    }
}