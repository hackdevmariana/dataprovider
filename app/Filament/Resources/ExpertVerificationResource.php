<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpertVerificationResource\Pages;
use App\Models\ExpertVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ExpertVerificationResource extends Resource
{
    protected static ?string $model = ExpertVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Social System';

    protected static ?string $modelLabel = 'Verificación de Experto';

    protected static ?string $pluralModelLabel = 'Verificaciones de Expertos';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Solicitante')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('expertise_area')
                            ->label('Área de Expertise')
                            ->options([
                                'solar' => 'Energía Solar',
                                'wind' => 'Energía Eólica',
                                'legal' => 'Legal y Regulatorio',
                                'financial' => 'Financiero',
                                'technical' => 'Técnico General',
                                'installation' => 'Instalación y Mantenimiento',
                                'grid' => 'Redes Eléctricas',
                                'storage' => 'Almacenamiento',
                                'efficiency' => 'Eficiencia Energética',
                                'sustainability' => 'Sostenibilidad',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('verification_level')
                            ->label('Nivel de Verificación')
                            ->options([
                                'basic' => 'Básico',
                                'advanced' => 'Avanzado',
                                'professional' => 'Profesional',
                                'expert' => 'Experto',
                            ])
                            ->default('basic')
                            ->required(),
                        
                        Forms\Components\TextInput::make('years_experience')
                            ->label('Años de Experiencia')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Descripción y Documentación')
                    ->schema([
                        Forms\Components\Textarea::make('expertise_description')
                            ->label('Descripción de Expertise')
                            ->required()
                            ->rows(4)
                            ->helperText('Describa su experiencia y conocimientos en el área'),
                        
                        Forms\Components\KeyValue::make('credentials')
                            ->label('Credenciales')
                            ->keyLabel('Tipo de Credencial')
                            ->valueLabel('Detalle')
                            ->addActionLabel('Añadir credencial'),
                        
                        Forms\Components\KeyValue::make('verification_documents')
                            ->label('Documentos de Verificación')
                            ->keyLabel('Tipo de Documento')
                            ->valueLabel('URL o Referencia')
                            ->addActionLabel('Añadir documento'),
                    ]),

                Forms\Components\Section::make('Historial Profesional')
                    ->schema([
                        Forms\Components\Repeater::make('certifications')
                            ->label('Certificaciones')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre de la Certificación')
                                    ->required(),
                                Forms\Components\TextInput::make('issuer')
                                    ->label('Entidad Emisora')
                                    ->required(),
                                Forms\Components\DatePicker::make('date')
                                    ->label('Fecha de Obtención'),
                                Forms\Components\DatePicker::make('expires')
                                    ->label('Fecha de Expiración'),
                            ])
                            ->columns(2)
                            ->collapsible(),
                        
                        Forms\Components\Repeater::make('education')
                            ->label('Formación Académica')
                            ->schema([
                                Forms\Components\TextInput::make('degree')
                                    ->label('Título/Grado')
                                    ->required(),
                                Forms\Components\TextInput::make('institution')
                                    ->label('Institución')
                                    ->required(),
                                Forms\Components\TextInput::make('year')
                                    ->label('Año de Graduación')
                                    ->numeric(),
                            ])
                            ->columns(3)
                            ->collapsible(),
                        
                        Forms\Components\Repeater::make('work_history')
                            ->label('Experiencia Laboral')
                            ->schema([
                                Forms\Components\TextInput::make('position')
                                    ->label('Cargo')
                                    ->required(),
                                Forms\Components\TextInput::make('company')
                                    ->label('Empresa')
                                    ->required(),
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Fecha de Inicio'),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Fecha de Fin'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción')
                                    ->rows(2),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Estado de Verificación')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'under_review' => 'En Revisión',
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                                'expired' => 'Expirado',
                            ])
                            ->default('pending')
                            ->required(),
                        
                        Forms\Components\TextInput::make('verification_fee')
                            ->label('Tarifa de Verificación')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->default(0),
                        
                        Forms\Components\Select::make('verified_by')
                            ->label('Verificado por')
                            ->relationship('verifier', 'name')
                            ->searchable(),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Verificación Pública')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('submitted_at')
                            ->label('Fecha de Solicitud')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('reviewed_at')
                            ->label('Fecha de Revisión'),
                        
                        Forms\Components\DateTimePicker::make('verified_at')
                            ->label('Fecha de Verificación'),
                        
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Fecha de Expiración'),
                    ])->columns(2),

                Forms\Components\Section::make('Notas y Resultados')
                    ->schema([
                        Forms\Components\TextInput::make('verification_score')
                            ->label('Puntuación de Verificación')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->helperText('Puntuación del 1 al 100'),
                        
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Notas del Verificador')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Razón de Rechazo')
                            ->rows(3)
                            ->visible(fn ($get) => $get('status') === 'rejected'),
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
                
                Tables\Columns\BadgeColumn::make('expertise_area')
                    ->label('Área')
                    ->colors([
                        'warning' => 'solar',
                        'info' => 'wind',
                        'danger' => 'legal',
                        'success' => 'financial',
                        'primary' => 'technical',
                        'gray' => 'installation',
                        'purple' => 'grid',
                        'orange' => 'storage',
                        'green' => 'efficiency',
                        'blue' => 'sustainability',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'solar' => 'Solar',
                        'wind' => 'Eólica',
                        'legal' => 'Legal',
                        'financial' => 'Financiero',
                        'technical' => 'Técnico',
                        'installation' => 'Instalación',
                        'grid' => 'Redes',
                        'storage' => 'Almacenamiento',
                        'efficiency' => 'Eficiencia',
                        'sustainability' => 'Sostenibilidad',
                    }),
                
                Tables\Columns\BadgeColumn::make('verification_level')
                    ->label('Nivel')
                    ->colors([
                        'gray' => 'basic',
                        'blue' => 'advanced',
                        'yellow' => 'professional',
                        'red' => 'expert',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'basic' => 'Básico',
                        'advanced' => 'Avanzado',
                        'professional' => 'Profesional',
                        'expert' => 'Experto',
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'under_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'secondary' => 'expired',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'under_review' => 'En Revisión',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'expired' => 'Expirado',
                    }),
                
                Tables\Columns\TextColumn::make('years_experience')
                    ->label('Años Exp.')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('verification_score')
                    ->label('Puntuación')
                    ->sortable()
                    ->placeholder('N/A'),
                
                Tables\Columns\TextColumn::make('verification_fee')
                    ->label('Tarifa')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Verificador')
                    ->placeholder('No asignado')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Solicitado')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Verificado')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('N/A'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('expertise_area')
                    ->label('Área de Expertise')
                    ->options([
                        'solar' => 'Energía Solar',
                        'wind' => 'Energía Eólica',
                        'legal' => 'Legal y Regulatorio',
                        'financial' => 'Financiero',
                        'technical' => 'Técnico General',
                        'installation' => 'Instalación y Mantenimiento',
                        'grid' => 'Redes Eléctricas',
                        'storage' => 'Almacenamiento',
                        'efficiency' => 'Eficiencia Energética',
                        'sustainability' => 'Sostenibilidad',
                    ]),
                
                Tables\Filters\SelectFilter::make('verification_level')
                    ->label('Nivel')
                    ->options([
                        'basic' => 'Básico',
                        'advanced' => 'Avanzado',
                        'professional' => 'Profesional',
                        'expert' => 'Experto',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'under_review' => 'En Revisión',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'expired' => 'Expirado',
                    ]),
                
                Tables\Filters\Filter::make('pending_review')
                    ->label('Pendientes de Revisión')
                    ->query(fn ($query) => $query->where('status', 'pending')),
                
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
                    ->action(function (ExpertVerification $record) {
                        $record->update([
                            'status' => 'under_review',
                            'reviewed_at' => now(),
                            'verified_by' => auth()->id(),
                        ]);
                    })
                    ->visible(fn (ExpertVerification $record) => $record->status === 'pending'),
                
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
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),
                    ])
                    ->action(function (ExpertVerification $record, array $data) {
                        $record->approve(auth()->user(), $data);
                    })
                    ->visible(fn (ExpertVerification $record) => $record->status === 'under_review'),
                
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Razón de Rechazo')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Adicionales')
                            ->rows(3),
                    ])
                    ->action(function (ExpertVerification $record, array $data) {
                        $record->reject(auth()->user(), $data['reason'], $data['notes'] ?? null);
                    })
                    ->visible(fn (ExpertVerification $record) => $record->status === 'under_review'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc');
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
            'index' => Pages\ListExpertVerifications::route('/'),
            'create' => Pages\CreateExpertVerification::route('/create'),
            'edit' => Pages\EditExpertVerification::route('/{record}/edit'),
        ];
    }
}