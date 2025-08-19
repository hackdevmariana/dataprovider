<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultationServiceResource\Pages;
use App\Models\ConsultationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ConsultationServiceResource extends Resource
{
    protected static ?string $model = ConsultationService::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Monetización';

    protected static ?string $modelLabel = 'Servicio de Consultoría';

    protected static ?string $pluralModelLabel = 'Servicios de Consultoría';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('consultant_id')
                            ->label('Consultor')
                            ->relationship('consultant', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'technical' => 'Técnica',
                                'legal' => 'Legal',
                                'financial' => 'Financiera',
                                'installation' => 'Instalación',
                                'maintenance' => 'Mantenimiento',
                                'custom' => 'Personalizada',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('format')
                            ->label('Formato')
                            ->options([
                                'online' => 'Online',
                                'onsite' => 'Presencial',
                                'hybrid' => 'Híbrido',
                                'document_review' => 'Revisión de documentos',
                                'phone_call' => 'Llamada telefónica',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'requested' => 'Solicitada',
                                'accepted' => 'Aceptada',
                                'in_progress' => 'En progreso',
                                'completed' => 'Completada',
                                'cancelled' => 'Cancelada',
                                'disputed' => 'En disputa',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Precios y Tiempo')
                    ->schema([
                        Forms\Components\TextInput::make('hourly_rate')
                            ->label('Tarifa por Hora')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('fixed_price')
                            ->label('Precio Fijo')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Cantidad Total')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('estimated_hours')
                            ->label('Horas Estimadas')
                            ->numeric(),
                        
                        Forms\Components\TextInput::make('actual_hours')
                            ->label('Horas Reales')
                            ->numeric(),
                        
                        Forms\Components\TextInput::make('platform_commission')
                            ->label('Comisión Plataforma')
                            ->numeric()
                            ->step(0.0001)
                            ->suffix('%')
                            ->default(0.15)
                            ->helperText('Ejemplo: 0.15 = 15%'),
                    ])->columns(3),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('requested_at')
                            ->label('Fecha de Solicitud')
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('accepted_at')
                            ->label('Fecha de Aceptación'),
                        
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Fecha de Inicio'),
                        
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Fecha de Finalización'),
                        
                        Forms\Components\DateTimePicker::make('deadline')
                            ->label('Fecha Límite'),
                    ])->columns(2),

                Forms\Components\Section::make('Valoraciones')
                    ->schema([
                        Forms\Components\TextInput::make('client_rating')
                            ->label('Valoración del Cliente')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),
                        
                        Forms\Components\TextInput::make('consultant_rating')
                            ->label('Valoración del Consultor')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),
                        
                        Forms\Components\Textarea::make('client_review')
                            ->label('Reseña del Cliente')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('consultant_review')
                            ->label('Reseña del Consultor')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Notas')
                    ->schema([
                        Forms\Components\Textarea::make('client_notes')
                            ->label('Notas del Cliente')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('consultant_notes')
                            ->label('Notas del Consultor')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado'),
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
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('consultant.name')
                    ->label('Consultor')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'technical',
                        'success' => 'legal',
                        'warning' => 'financial',
                        'danger' => 'installation',
                        'secondary' => 'maintenance',
                        'gray' => 'custom',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'technical' => 'Técnica',
                        'legal' => 'Legal',
                        'financial' => 'Financiera',
                        'installation' => 'Instalación',
                        'maintenance' => 'Mantenimiento',
                        'custom' => 'Personalizada',
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'primary' => 'requested',
                        'success' => 'accepted',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'purple' => 'disputed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'requested' => 'Solicitada',
                        'accepted' => 'Aceptada',
                        'in_progress' => 'En progreso',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'disputed' => 'En disputa',
                    }),
                
                Tables\Columns\BadgeColumn::make('format')
                    ->label('Formato')
                    ->colors([
                        'primary' => 'online',
                        'success' => 'onsite',
                        'warning' => 'hybrid',
                        'secondary' => 'document_review',
                        'gray' => 'phone_call',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'online' => 'Online',
                        'onsite' => 'Presencial',
                        'hybrid' => 'Híbrido',
                        'document_review' => 'Documentos',
                        'phone_call' => 'Teléfono',
                    }),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Cantidad Total')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('estimated_hours')
                    ->label('Horas Est.')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('actual_hours')
                    ->label('Horas Real')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client_rating')
                    ->label('Rating Cliente')
                    ->formatStateUsing(fn ($state) => $state ? str_repeat('⭐', $state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('consultant_rating')
                    ->label('Rating Consultor')
                    ->formatStateUsing(fn ($state) => $state ? str_repeat('⭐', $state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Fecha Límite')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'technical' => 'Técnica',
                        'legal' => 'Legal',
                        'financial' => 'Financiera',
                        'installation' => 'Instalación',
                        'maintenance' => 'Mantenimiento',
                        'custom' => 'Personalizada',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'requested' => 'Solicitada',
                        'accepted' => 'Aceptada',
                        'in_progress' => 'En progreso',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'disputed' => 'En disputa',
                    ]),
                
                Tables\Filters\SelectFilter::make('format')
                    ->label('Formato')
                    ->options([
                        'online' => 'Online',
                        'onsite' => 'Presencial',
                        'hybrid' => 'Híbrido',
                        'document_review' => 'Revisión de documentos',
                        'phone_call' => 'Llamada telefónica',
                    ]),
                
                Tables\Filters\Filter::make('overdue')
                    ->label('Vencidas')
                    ->query(fn ($query) => $query->whereIn('status', ['accepted', 'in_progress'])->where('deadline', '<', now())),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('accept')
                    ->label('Aceptar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (ConsultationService $record) => $record->accept())
                    ->visible(fn (ConsultationService $record) => $record->status === 'requested'),
                
                Tables\Actions\Action::make('start')
                    ->label('Iniciar')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (ConsultationService $record) => $record->start())
                    ->visible(fn (ConsultationService $record) => $record->status === 'accepted'),
                
                Tables\Actions\Action::make('complete')
                    ->label('Completar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (ConsultationService $record) => $record->complete())
                    ->visible(fn (ConsultationService $record) => $record->status === 'in_progress'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Consultoría')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Título'),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),
                        
                        Infolists\Components\TextEntry::make('consultant.name')
                            ->label('Consultor'),
                        
                        Infolists\Components\TextEntry::make('client.name')
                            ->label('Cliente'),
                        
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'technical' => 'primary',
                                'legal' => 'success',
                                'financial' => 'warning',
                                'installation' => 'danger',
                                'maintenance' => 'secondary',
                                'custom' => 'gray',
                            }),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'requested' => 'primary',
                                'accepted' => 'success',
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'disputed' => 'purple',
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('Información Financiera')
                    ->schema([
                        Infolists\Components\TextEntry::make('hourly_rate')
                            ->label('Tarifa por Hora')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('fixed_price')
                            ->label('Precio Fijo')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Cantidad Total')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('platform_commission')
                            ->label('Comisión Plataforma')
                            ->formatStateUsing(fn ($state) => number_format($state * 100, 1) . '%'),
                        
                        Infolists\Components\TextEntry::make('net_amount')
                            ->label('Cantidad Neta')
                            ->formatStateUsing(fn ($record) => '€' . number_format($record->calculateNetAmount(), 2)),
                    ])->columns(3),

                Infolists\Components\Section::make('Tiempo y Progreso')
                    ->schema([
                        Infolists\Components\TextEntry::make('estimated_hours')
                            ->label('Horas Estimadas'),
                        
                        Infolists\Components\TextEntry::make('actual_hours')
                            ->label('Horas Reales'),
                        
                        Infolists\Components\TextEntry::make('deadline')
                            ->label('Fecha Límite')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('progress')
                            ->label('Progreso')
                            ->formatStateUsing(fn ($record) => $record->getProgress() . '%'),
                    ])->columns(2),

                Infolists\Components\Section::make('Valoraciones')
                    ->schema([
                        Infolists\Components\TextEntry::make('client_rating')
                            ->label('Valoración del Cliente')
                            ->formatStateUsing(fn ($state) => $state ? str_repeat('⭐', $state) . " ($state/5)" : 'Sin valorar'),
                        
                        Infolists\Components\TextEntry::make('consultant_rating')
                            ->label('Valoración del Consultor')
                            ->formatStateUsing(fn ($state) => $state ? str_repeat('⭐', $state) . " ($state/5)" : 'Sin valorar'),
                        
                        Infolists\Components\TextEntry::make('average_rating')
                            ->label('Valoración Promedio')
                            ->formatStateUsing(fn ($record) => number_format($record->getAverageRating(), 1) . '/5'),
                    ])->columns(3),

                Infolists\Components\Section::make('Reseñas')
                    ->schema([
                        Infolists\Components\TextEntry::make('client_review')
                            ->label('Reseña del Cliente'),
                        
                        Infolists\Components\TextEntry::make('consultant_review')
                            ->label('Reseña del Consultor'),
                    ]),
            ]);
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
            'index' => Pages\ListConsultationServices::route('/'),
            'create' => Pages\CreateConsultationService::route('/create'),
            'edit' => Pages\EditConsultationService::route('/{record}/edit'),
        ];
    }
}