<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectProposalResource\Pages;
use App\Models\ProjectProposal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectProposalResource extends Resource
{
    protected static ?string $model = ProjectProposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Projects & Monetization';

    protected static ?string $modelLabel = 'Propuesta de Proyecto';

    protected static ?string $pluralModelLabel = 'Propuestas de Proyectos';

    protected static ?int $navigationSort = 1;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(4),
                        
                        Forms\Components\Select::make('proposer_id')
                            ->label('Proponente')
                            ->relationship('proposer', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('cooperative_id')
                            ->label('Cooperativa')
                            ->relationship('cooperative', 'name')
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles del Proyecto')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'shared_installation' => 'Instalación Compartida',
                                'community_project' => 'Proyecto Comunitario',
                                'research' => 'Investigación',
                                'education' => 'Educación',
                                'infrastructure' => 'Infraestructura',
                                'other' => 'Otro',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'submitted' => 'Enviado',
                                'under_review' => 'En Revisión',
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                                'in_progress' => 'En Progreso',
                                'completed' => 'Completado',
                                'cancelled' => 'Cancelado',
                            ])
                            ->default('draft')
                            ->required(),
                        
                        Forms\Components\TextInput::make('estimated_cost')
                            ->label('Costo Estimado')
                            ->numeric()
                            ->prefix('€'),
                        
                        Forms\Components\TextInput::make('target_amount')
                            ->label('Cantidad Objetivo')
                            ->numeric()
                            ->prefix('€'),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Fin'),
                        
                        Forms\Components\DateTimePicker::make('submission_deadline')
                            ->label('Fecha Límite de Envío'),
                    ])->columns(3),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->label('Público')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('accepts_investments')
                            ->label('Acepta Inversiones')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('proposer.name')
                    ->label('Proponente')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'shared_installation',
                        'success' => 'community_project',
                        'info' => 'research',
                        'warning' => 'education',
                        'gray' => 'other',
                    ]),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'submitted',
                        'info' => 'under_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'secondary' => 'cancelled',
                    ]),
                
                Tables\Columns\TextColumn::make('estimated_cost')
                    ->label('Costo')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('investments_count')
                    ->label('Inversiones')
                    ->counts('investments')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'shared_installation' => 'Instalación Compartida',
                        'community_project' => 'Proyecto Comunitario',
                        'research' => 'Investigación',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Borrador',
                        'approved' => 'Aprobado',
                        'in_progress' => 'En Progreso',
                        'completed' => 'Completado',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectProposals::route('/'),
            'create' => Pages\CreateProjectProposal::route('/create'),
            'edit' => Pages\EditProjectProposal::route('/{record}/edit'),
        ];
    }
}