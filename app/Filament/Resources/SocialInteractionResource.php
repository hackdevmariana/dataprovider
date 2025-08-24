<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialInteractionResource\Pages;
use App\Filament\Resources\SocialInteractionResource\RelationManagers;
use App\Models\SocialInteraction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SocialInteractionResource extends Resource
{
    protected static ?string $navigationGroup = 'Social System';
    protected static ?string $model = SocialInteraction::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Interacción Social';
    protected static ?string $pluralModelLabel = 'Interacciones Sociales';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Interacción')
                    ->description('Detalles principales de la interacción social')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Usuario')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Select::make('interaction_type')
                                    ->label('Tipo de Interacción')
                                    ->options([
                                        'like' => '👍 Me gusta',
                                        'love' => '❤️ Me encanta',
                                        'wow' => '😮 Me asombra',
                                        'celebrate' => '🎉 Celebrar',
                                        'support' => '🤝 Apoyar',
                                        'share' => '📤 Compartir',
                                        'bookmark' => '🔖 Guardar en favoritos',
                                        'follow' => '👀 Seguir',
                                        'subscribe' => '🔔 Suscribirse',
                                        'report' => '🚨 Reportar',
                                        'hide' => '👁️‍🗨️ Ocultar',
                                        'block' => '🚫 Bloquear',
                                    ])
                                    ->required()
                                    ->searchable(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('interactable_type')
                                    ->label('Tipo de Objeto')
                                    ->required()
                                    ->placeholder('App\Models\Post')
                                    ->helperText('Clase del modelo con el que se interactúa'),
                                Forms\Components\TextInput::make('interactable_id')
                                    ->label('ID del Objeto')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('1'),
                            ]),
                        Forms\Components\Textarea::make('interaction_note')
                            ->label('Nota de Interacción')
                            ->rows(3)
                            ->placeholder('Nota opcional del usuario sobre la interacción'),
                        Forms\Components\KeyValue::make('interaction_data')
                            ->label('Datos Adicionales')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor')
                            ->helperText('Datos JSON adicionales específicos de la interacción'),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Contexto y Ubicación')
                    ->description('Información contextual de la interacción')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('source')
                                    ->label('Fuente')
                                    ->placeholder('feed, profile, notification')
                                    ->helperText('Desde dónde se realizó la interacción'),
                                Forms\Components\TextInput::make('device_type')
                                    ->label('Tipo de Dispositivo')
                                    ->placeholder('mobile, desktop, tablet')
                                    ->helperText('Dispositivo desde el que se interactuó'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitud')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->placeholder('40.4168')
                                    ->helperText('Coordenada de latitud (opcional)'),
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitud')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->placeholder('-3.7038')
                                    ->helperText('Coordenada de longitud (opcional)'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Configuración de Privacidad')
                    ->description('Opciones de privacidad y visibilidad')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('is_public')
                                    ->label('Interacción Pública')
                                    ->default(true)
                                    ->helperText('Si la interacción es visible públicamente'),
                                Forms\Components\Toggle::make('notify_author')
                                    ->label('Notificar al Autor')
                                    ->default(true)
                                    ->helperText('Enviar notificación al autor del contenido'),
                                Forms\Components\Toggle::make('show_in_activity')
                                    ->label('Mostrar en Actividad')
                                    ->default(true)
                                    ->helperText('Aparecer en el feed de actividad del usuario'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Métricas y Calidad')
                    ->description('Puntuaciones y métricas de engagement')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('engagement_weight')
                                    ->label('Peso de Engagement')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->helperText('Peso para algoritmos de engagement (1-10)'),
                                Forms\Components\TextInput::make('quality_score')
                                    ->label('Puntuación de Calidad')
                                    ->numeric()
                                    ->default(100)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->helperText('Score de calidad de la interacción'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Configuración Temporal')
                    ->description('Opciones de expiración y temporalidad')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('interaction_expires_at')
                                    ->label('Fecha de Expiración')
                                    ->helperText('Cuándo expira la interacción (opcional)'),
                                Forms\Components\Toggle::make('is_temporary')
                                    ->label('Interacción Temporal')
                                    ->default(false)
                                    ->helperText('Si es una interacción que expira automáticamente'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Estado')
                    ->description('Estado actual de la interacción')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => '🟢 Activa',
                                'withdrawn' => '🔄 Retirada',
                                'expired' => '⏰ Expirada',
                                'flagged' => '🚩 Reportada',
                                'hidden' => '👁️‍🗨️ Oculta',
                            ])
                            ->default('active')
                            ->required()
                            ->searchable(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Información del Sistema')
                    ->description('Datos automáticos del sistema')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Creada')
                            ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i:s') ?? 'N/A'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Actualizada')
                            ->content(fn ($record) => $record?->updated_at?->format('d/m/Y H:i:s') ?? 'N/A'),
                        Forms\Components\Placeholder::make('interaction_info')
                            ->label('Información de la Interacción')
                            ->content(function ($record) {
                                if (!$record) return 'N/A';
                                return $record->getEmoji() . ' ' . $record->getReadableDescription();
                            }),
                        Forms\Components\Placeholder::make('engagement_info')
                            ->label('Información de Engagement')
                            ->content(function ($record) {
                                if (!$record) return 'N/A';
                                $weight = $record->getEngagementWeight();
                                $isPositive = $record->isPositive();
                                $isNegative = $record->isNegative();
                                return "Peso: {$weight} | " . 
                                       ($isPositive ? '✅ Positiva' : '') . 
                                       ($isNegative ? '❌ Negativa' : '') . 
                                       (!$isPositive && !$isNegative ? '⚪ Neutral' : '');
                            }),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('interaction_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => $state . ' ' . (new SocialInteraction())->getEmoji())
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'like', 'love', 'wow', 'celebrate', 'support' => 'success',
                        'share', 'bookmark', 'follow', 'subscribe' => 'info',
                        'report', 'hide', 'block' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('interactable_type')
                    ->label('Objeto')
                    ->formatStateUsing(fn ($state) => Str::afterLast($state, '\\'))
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('interactable_id')
                    ->label('ID Objeto')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('interaction_note')
                    ->label('Nota')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('source')
                    ->label('Fuente')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('device_type')
                    ->label('Dispositivo')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('notify_author')
                    ->label('Notificar')
                    ->boolean()
                    ->trueIcon('heroicon-o-bell')
                    ->falseIcon('heroicon-o-bell-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\IconColumn::make('show_in_activity')
                    ->label('En Actividad')
                    ->boolean()
                    ->trueIcon('heroicon-o-rss')
                    ->falseIcon('heroicon-o-rss')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('engagement_weight')
                    ->label('Peso')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('quality_score')
                    ->label('Calidad')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    )
                    ->suffix('%')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 90 => 'success',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'withdrawn' => 'warning',
                        'expired' => 'gray',
                        'flagged' => 'danger',
                        'hidden' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => '🟢 Activa',
                        'withdrawn' => '🔄 Retirada',
                        'expired' => '⏰ Expirada',
                        'flagged' => '🚩 Reportada',
                        'hidden' => '👁️‍🗨️ Oculta',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('interaction_expires_at')
                    ->label('Expira')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('is_temporary')
                    ->label('Temporal')
                    ->boolean()
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('interaction_type')
                    ->label('Tipo de Interacción')
                    ->options([
                        'like' => '👍 Me gusta',
                        'love' => '❤️ Me encanta',
                        'wow' => '😮 Me asombra',
                        'celebrate' => '🎉 Celebrar',
                        'support' => '🤝 Apoyar',
                        'share' => '📤 Compartir',
                        'bookmark' => '🔖 Guardar en favoritos',
                        'follow' => '👀 Seguir',
                        'subscribe' => '🔔 Suscribirse',
                        'report' => '🚨 Reportar',
                        'hide' => '👁️‍🗨️ Ocultar',
                        'block' => '🚫 Bloquear',
                    ])
                    ->multiple()
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => '🟢 Activa',
                        'withdrawn' => '🔄 Retirada',
                        'expired' => '⏰ Expirada',
                        'flagged' => '🚩 Reportada',
                        'hidden' => '👁️‍🗨️ Oculta',
                    ])
                    ->multiple()
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('source')
                    ->label('Fuente')
                    ->options([
                        'feed' => 'Feed',
                        'profile' => 'Perfil',
                        'notification' => 'Notificación',
                        'search' => 'Búsqueda',
                        'direct' => 'Directo',
                    ])
                    ->multiple()
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('device_type')
                    ->label('Dispositivo')
                    ->options([
                        'mobile' => 'Móvil',
                        'desktop' => 'Escritorio',
                        'tablet' => 'Tablet',
                        'smart_tv' => 'Smart TV',
                        'other' => 'Otro',
                    ])
                    ->multiple()
                    ->searchable(),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Interacción Pública')
                    ->placeholder('Todas')
                    ->trueLabel('Solo públicas')
                    ->falseLabel('Solo privadas'),
                
                Tables\Filters\TernaryFilter::make('is_temporary')
                    ->label('Interacción Temporal')
                    ->placeholder('Todas')
                    ->trueLabel('Solo temporales')
                    ->falseLabel('Solo permanentes'),
                
                Tables\Filters\Filter::make('high_engagement')
                    ->label('Alto Engagement')
                    ->query(fn (Builder $query) => $query->where('engagement_weight', '>=', 5)),
                
                Tables\Filters\Filter::make('high_quality')
                    ->label('Alta Calidad')
                    ->query(fn (Builder $query) => $query->where('quality_score', '>=', 80)),
                
                Tables\Filters\Filter::make('recent_interactions')
                    ->label('Interacciones Recientes')
                    ->query(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7))),
                
                Tables\Filters\Filter::make('expired_interactions')
                    ->label('Interacciones Expiradas')
                    ->query(fn (Builder $query) => $query->whereNotNull('interaction_expires_at')->where('interaction_expires_at', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\Action::make('toggle_public')
                    ->label('Cambiar Visibilidad')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->action(function (SocialInteraction $record) {
                        $record->update(['is_public' => !$record->is_public]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cambiar Visibilidad')
                    ->modalDescription('¿Estás seguro de que quieres cambiar la visibilidad de esta interacción?')
                    ->modalSubmitActionLabel('Confirmar')
                    ->modalCancelActionLabel('Cancelar'),
                Tables\Actions\Action::make('withdraw_interaction')
                    ->label('Retirar Interacción')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->action(function (SocialInteraction $record) {
                        $record->withdraw();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Retirar Interacción')
                    ->modalDescription('¿Estás seguro de que quieres retirar esta interacción?')
                    ->modalSubmitActionLabel('Confirmar')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(fn ($record) => $record->status === 'active'),
                Tables\Actions\Action::make('mark_expired')
                    ->label('Marcar como Expirada')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->action(function (SocialInteraction $record) {
                        $record->markAsExpired();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Marcar como Expirada')
                    ->modalDescription('¿Estás seguro de que quieres marcar esta interacción como expirada?')
                    ->modalSubmitActionLabel('Confirmar')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(fn ($record) => $record->status === 'active'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash'),
                    Tables\Actions\BulkAction::make('mark_public')
                        ->label('Marcar como Públicas')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_public' => true]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Marcar como Públicas')
                        ->modalDescription('¿Estás seguro de que quieres marcar las interacciones seleccionadas como públicas?')
                        ->modalSubmitActionLabel('Confirmar')
                        ->modalCancelActionLabel('Cancelar'),
                    Tables\Actions\BulkAction::make('mark_private')
                        ->label('Marcar como Privadas')
                        ->icon('heroicon-o-eye-slash')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_public' => false]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Marcar como Privadas')
                        ->modalDescription('¿Estás seguro de que quieres marcar las interacciones seleccionadas como privadas?')
                        ->modalSubmitActionLabel('Confirmar')
                        ->modalCancelActionLabel('Cancelar'),
                    Tables\Actions\BulkAction::make('withdraw_selected')
                        ->label('Retirar Seleccionadas')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'active') {
                                    $record->withdraw();
                                }
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Retirar Interacciones')
                        ->modalDescription('¿Estás seguro de que quieres retirar las interacciones seleccionadas?')
                        ->modalSubmitActionLabel('Confirmar')
                        ->modalCancelActionLabel('Cancelar'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated()
            ->searchable()
            ->searchPlaceholder('Buscar interacciones...');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'interactable'])
            ->whereNotNull('user_id');
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
            'index' => Pages\ListSocialInteractions::route('/'),
            'create' => Pages\CreateSocialInteraction::route('/create'),
            'edit' => Pages\EditSocialInteraction::route('/{record}/edit'),
        ];
    }
}
