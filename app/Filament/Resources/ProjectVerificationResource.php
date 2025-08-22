<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectVerificationResource\Pages;
use App\Models\ProjectVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ProjectVerificationResource extends Resource
{
    protected static ?string $model = ProjectVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Projects & Monetization';

    protected static ?string $modelLabel = 'Verificación de Proyecto';

    protected static ?string $pluralModelLabel = 'Verificaciones de Proyectos';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('project_proposal_id')
                            ->label('Proyecto')
                            ->relationship('projectProposal', 'title')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('requested_by')
                            ->label('Solicitado por')
                            ->relationship('requester', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('verified_by')
                            ->label('Verificado por')
                            ->relationship('verifier', 'name')
                            ->searchable(),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'basic' => 'Básica',
                                'advanced' => 'Avanzada',
                                'professional' => 'Profesional',
                                'enterprise' => 'Enterprise',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'requested' => 'Solicitada',
                                'in_review' => 'En revisión',
                                'approved' => 'Aprobada',
                                'rejected' => 'Rechazada',
                                'expired' => 'Expirada',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Financiera')
                    ->schema([
                        Forms\Components\TextInput::make('fee')
                            ->label('Tarifa')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->required(),
                        
                        Forms\Components\TextInput::make('currency')
                            ->label('Moneda')
                            ->default('EUR')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('requested_at')
                            ->label('Fecha de Solicitud')
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('reviewed_at')
                            ->label('Fecha de Revisión'),
                        
                        Forms\Components\DateTimePicker::make('verified_at')
                            ->label('Fecha de Verificación'),
                        
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Fecha de Expiración'),
                    ])->columns(2),

                Forms\Components\Section::make('Resultados de Verificación')
                    ->schema([
                        Forms\Components\TextInput::make('score')
                            ->label('Puntuación')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('certificate_number')
                            ->label('Número de Certificado')
                            ->maxLength(255),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Público')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Notas y Comentarios')
                    ->schema([
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Notas de Verificación')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Razón de Rechazo')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('projectProposal.title')
                    ->label('Proyecto')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Solicitante')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Verificador')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'basic',
                        'success' => 'advanced',
                        'warning' => 'professional',
                        'danger' => 'enterprise',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'basic' => 'Básica',
                        'advanced' => 'Avanzada',
                        'professional' => 'Profesional',
                        'enterprise' => 'Enterprise',
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'primary' => 'requested',
                        'warning' => 'in_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'secondary' => 'expired',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'requested' => 'Solicitada',
                        'in_review' => 'En revisión',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        'expired' => 'Expirada',
                    }),
                
                Tables\Columns\TextColumn::make('fee')
                    ->label('Tarifa')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('score')
                    ->label('Puntuación')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('certificate_number')
                    ->label('Certificado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Solicitada')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Verificada')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : null),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'basic' => 'Básica',
                        'advanced' => 'Avanzada',
                        'professional' => 'Profesional',
                        'enterprise' => 'Enterprise',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'requested' => 'Solicitada',
                        'in_review' => 'En revisión',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        'expired' => 'Expirada',
                    ]),
                
                Tables\Filters\Filter::make('pending_review')
                    ->label('Pendientes de revisión')
                    ->query(fn ($query) => $query->where('status', 'requested')),
                
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Próximas a expirar')
                    ->query(fn ($query) => $query->where('status', 'approved')->whereBetween('expires_at', [now(), now()->addDays(30)])),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Público'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('start_review')
                    ->label('Iniciar Revisión')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (ProjectVerification $record) {
                        $record->update([
                            'status' => 'in_review',
                            'reviewed_at' => now(),
                            'verified_by' => auth()->id(),
                        ]);
                    })
                    ->visible(fn (ProjectVerification $record) => $record->status === 'requested'),
                
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('score')
                            ->label('Puntuación')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                        
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Notas de Verificación')
                            ->rows(3),
                    ])
                    ->action(function (ProjectVerification $record, array $data) {
                        $record->approve(
                            ['approved' => true, 'score' => $data['score']],
                            $data['verification_notes'],
                            $data['score']
                        );
                    })
                    ->visible(fn (ProjectVerification $record) => $record->status === 'in_review'),
                
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Razón de Rechazo')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Notas Adicionales')
                            ->rows(3),
                    ])
                    ->action(function (ProjectVerification $record, array $data) {
                        $record->reject(
                            $data['rejection_reason'],
                            $data['verification_notes']
                        );
                    })
                    ->visible(fn (ProjectVerification $record) => $record->status === 'in_review'),
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
                Infolists\Components\Section::make('Información de la Verificación')
                    ->schema([
                        Infolists\Components\TextEntry::make('projectProposal.title')
                            ->label('Proyecto'),
                        
                        Infolists\Components\TextEntry::make('requester.name')
                            ->label('Solicitante'),
                        
                        Infolists\Components\TextEntry::make('verifier.name')
                            ->label('Verificador'),
                        
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'basic' => 'primary',
                                'advanced' => 'success',
                                'professional' => 'warning',
                                'enterprise' => 'danger',
                            }),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'requested' => 'primary',
                                'in_review' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'expired' => 'secondary',
                            }),
                        
                        Infolists\Components\TextEntry::make('fee')
                            ->label('Tarifa')
                            ->money('EUR'),
                    ])->columns(2),

                Infolists\Components\Section::make('Fechas Importantes')
                    ->schema([
                        Infolists\Components\TextEntry::make('requested_at')
                            ->label('Solicitada')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('reviewed_at')
                            ->label('En revisión')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('verified_at')
                            ->label('Verificada')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Expira')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('Resultados')
                    ->schema([
                        Infolists\Components\TextEntry::make('score')
                            ->label('Puntuación'),
                        
                        Infolists\Components\TextEntry::make('certificate_number')
                            ->label('Número de Certificado'),
                        
                        Infolists\Components\IconEntry::make('is_public')
                            ->label('Público')
                            ->boolean(),
                        
                        Infolists\Components\TextEntry::make('days_until_expiration')
                            ->label('Días hasta expiración')
                            ->formatStateUsing(fn ($record) => $record->getDaysUntilExpiration() . ' días'),
                    ])->columns(2),

                Infolists\Components\Section::make('Notas y Comentarios')
                    ->schema([
                        Infolists\Components\TextEntry::make('verification_notes')
                            ->label('Notas de Verificación'),
                        
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Razón de Rechazo'),
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
            'index' => Pages\ListProjectVerifications::route('/'),
            'create' => Pages\CreateProjectVerification::route('/create'),
            'edit' => Pages\EditProjectVerification::route('/{record}/edit'),
        ];
    }
}