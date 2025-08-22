<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectUpdateResource\Pages;
use App\Models\ProjectUpdate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectUpdateResource extends Resource
{
    protected static ?string $model = ProjectUpdate::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Projects & Monetization';

    protected static ?string $modelLabel = 'Actualización';

    protected static ?string $pluralModelLabel = 'Actualizaciones';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Actualización')
                    ->schema([
                        Forms\Components\Select::make('project_proposal_id')
                            ->label('Proyecto')
                            ->relationship('projectProposal', 'title')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('author_id')
                            ->label('Autor')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Contenido')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Tipo y Categorización')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'progress' => 'Progreso',
                                'milestone' => 'Hito',
                                'financial' => 'Financiero',
                                'technical' => 'Técnico',
                                'announcement' => 'Anuncio',
                                'issue' => 'Incidencia',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('priority')
                            ->label('Prioridad')
                            ->options([
                                'low' => 'Baja',
                                'medium' => 'Media',
                                'high' => 'Alta',
                                'urgent' => 'Urgente',
                            ])
                            ->default('medium'),
                        
                        Forms\Components\Select::make('visibility')
                            ->label('Visibilidad')
                            ->options([
                                'public' => 'Público',
                                'investors' => 'Solo Inversores',
                                'team' => 'Solo Equipo',
                                'private' => 'Privado',
                            ])
                            ->default('public')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Métricas del Proyecto')
                    ->schema([
                        Forms\Components\TextInput::make('progress_percentage')
                            ->label('Porcentaje de Progreso')
                            ->numeric()
                            ->suffix('%')
                            ->min(0)
                            ->max(100)
                            ->step(0.1),
                        
                        Forms\Components\TextInput::make('current_production_kwh')
                            ->label('Producción Actual (kWh)')
                            ->numeric()
                            ->suffix('kWh')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('financial_impact')
                            ->label('Impacto Financiero')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                    ])->columns(3),

                Forms\Components\Section::make('Hito y Estado')
                    ->schema([
                        Forms\Components\Toggle::make('is_milestone')
                            ->label('Es un Hito')
                            ->default(false)
                            ->reactive(),
                        
                        Forms\Components\TextInput::make('milestone_description')
                            ->label('Descripción del Hito')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('is_milestone')),
                        
                        Forms\Components\Toggle::make('affected_timeline')
                            ->label('Afecta Cronograma')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Metadatos y Organización')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->placeholder('Añadir etiquetas'),
                        
                        Forms\Components\KeyValue::make('metrics')
                            ->label('Métricas Adicionales')
                            ->keyLabel('Métrica')
                            ->valueLabel('Valor'),
                        
                        Forms\Components\Repeater::make('next_steps')
                            ->label('Próximos Pasos')
                            ->schema([
                                Forms\Components\TextInput::make('step')
                                    ->label('Paso')
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Fecha Límite'),
                            ])
                            ->collapsible()
                            ->addActionLabel('Añadir Paso'),
                        
                        Forms\Components\Repeater::make('images')
                            ->label('Imágenes del Progreso')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Imagen')
                                    ->image()
                                    ->required(),
                                Forms\Components\TextInput::make('caption')
                                    ->label('Descripción'),
                            ])
                            ->collapsible()
                            ->addActionLabel('Añadir Imagen'),
                    ]),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación')
                            ->default(now()),
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
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Autor')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'info' => 'progress',
                        'success' => 'milestone',
                        'warning' => 'financial',
                        'primary' => 'technical',
                        'gray' => 'announcement',
                        'danger' => 'issue',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'progress' => 'Progreso',
                        'milestone' => 'Hito',
                        'financial' => 'Financiero',
                        'technical' => 'Técnico',
                        'announcement' => 'Anuncio',
                        'issue' => 'Incidencia',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Prioridad')
                    ->colors([
                        'gray' => 'low',
                        'info' => 'medium',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progreso')
                    ->suffix('%')
                    ->sortable()
                    ->placeholder('N/A'),
                
                Tables\Columns\IconColumn::make('is_milestone')
                    ->label('Hito')
                    ->boolean()
                    ->trueIcon('heroicon-o-flag')
                    ->falseIcon('heroicon-o-flag'),
                
                Tables\Columns\BadgeColumn::make('visibility')
                    ->label('Visibilidad')
                    ->colors([
                        'success' => 'public',
                        'warning' => 'investors',
                        'info' => 'team',
                        'gray' => 'private',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => 'Público',
                        'investors' => 'Inversores',
                        'team' => 'Equipo',
                        'private' => 'Privado',
                        default => ucfirst($state),
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_proposal_id')
                    ->label('Proyecto')
                    ->relationship('projectProposal', 'title')
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'progress' => 'Progreso',
                        'milestone' => 'Hito',
                        'financial' => 'Financiero',
                        'technical' => 'Técnico',
                        'announcement' => 'Anuncio',
                        'issue' => 'Incidencia',
                    ]),
                
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                    ]),
                
                Tables\Filters\SelectFilter::make('visibility')
                    ->options([
                        'public' => 'Público',
                        'investors' => 'Solo Inversores',
                        'team' => 'Solo Equipo',
                        'private' => 'Privado',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_milestone')
                    ->label('Es Hito'),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Recientes (7 días)')
                    ->query(fn ($query) => $query->where('published_at', '>=', now()->subWeek())),
                
                Tables\Filters\Filter::make('with_progress')
                    ->label('Con Progreso')
                    ->query(fn ($query) => $query->whereNotNull('progress_percentage')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectUpdates::route('/'),
            'create' => Pages\CreateProjectUpdate::route('/create'),
            'edit' => Pages\EditProjectUpdate::route('/{record}/edit'),
        ];
    }
}