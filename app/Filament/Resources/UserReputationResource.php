<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserReputationResource\Pages;
use App\Filament\Resources\UserReputationResource\RelationManagers;
use App\Models\UserReputation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;

class UserReputationResource extends Resource
{
    protected static ?string $navigationGroup = 'Usuarios y Social';
    protected static ?string $model = UserReputation::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Reputación de Usuarios';
    protected static ?string $modelLabel = 'Reputación';
    protected static ?string $pluralModelLabel = 'Reputaciones';
    protected static ?int $navigationSort = 1;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        TextInput::make('total_reputation')
                            ->label('Reputación Total')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Reputación por Categorías y Temas')
                    ->schema([
                        KeyValue::make('category_reputation')
                            ->label('Reputación por Categoría')
                            ->keyLabel('Categoría')
                            ->valueLabel('Puntos')
                            ->addActionLabel('Añadir Categoría'),
                        
                        KeyValue::make('topic_reputation')
                            ->label('Reputación por Tema')
                            ->keyLabel('ID del Tema')
                            ->valueLabel('Puntos')
                            ->addActionLabel('Añadir Tema'),
                    ])
                    ->columns(2),

                Section::make('Métricas de Contribución')
                    ->schema([
                        TextInput::make('helpful_answers')
                            ->label('Respuestas Útiles')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('accepted_solutions')
                            ->label('Soluciones Aceptadas')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('quality_posts')
                            ->label('Posts de Calidad')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('verified_contributions')
                            ->label('Contribuciones Verificadas')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('topics_created')
                            ->label('Temas Creados')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('successful_projects')
                            ->label('Proyectos Exitosos')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('mentorship_points')
                            ->label('Puntos de Mentoría')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),

                Section::make('Sistema de Votos')
                    ->schema([
                        TextInput::make('upvotes_received')
                            ->label('Upvotes Recibidos')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('downvotes_received')
                            ->label('Downvotes Recibidos')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('upvote_ratio')
                            ->label('Ratio de Upvotes (%)')
                            ->numeric()
                            ->step(0.01)
                            ->default(0),
                    ])
                    ->columns(3),

                Section::make('Sanciones y Moderación')
                    ->schema([
                        TextInput::make('warnings_received')
                            ->label('Advertencias Recibidas')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('content_removed')
                            ->label('Contenido Eliminado')
                            ->numeric()
                            ->default(0),
                        
                        Toggle::make('is_suspended')
                            ->label('Suspendido')
                            ->default(false),
                        
                        DateTimePicker::make('suspended_until')
                            ->label('Suspendido Hasta')
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Rankings')
                    ->schema([
                        TextInput::make('global_rank')
                            ->label('Ranking Global')
                            ->numeric()
                            ->nullable(),
                        
                        TextInput::make('monthly_rank')
                            ->label('Ranking Mensual')
                            ->numeric()
                            ->nullable(),
                        
                        KeyValue::make('category_ranks')
                            ->label('Rankings por Categoría')
                            ->keyLabel('Categoría')
                            ->valueLabel('Ranking')
                            ->addActionLabel('Añadir Ranking'),
                    ])
                    ->columns(2),

                Section::make('Estado Profesional')
                    ->schema([
                        Toggle::make('is_verified_professional')
                            ->label('Profesional Verificado')
                            ->default(false),
                        
                        KeyValue::make('professional_credentials')
                            ->label('Credenciales Profesionales')
                            ->keyLabel('Tipo')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Añadir Credencial'),
                        
                        KeyValue::make('expertise_areas')
                            ->label('Áreas de Expertise')
                            ->keyLabel('Área')
                            ->valueLabel('Nivel')
                            ->addActionLabel('Añadir Área'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('total_reputation')
                    ->label('Reputación Total')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 10000 => 'success',
                        $state >= 5000 => 'warning',
                        $state >= 1000 => 'info',
                        $state >= 500 => 'primary',
                        default => 'secondary',
                    }),
                
                TextColumn::make('global_rank')
                    ->label('Ranking Global')
                    ->numeric()
                    ->sortable()
                    ->placeholder('Sin ranking'),
                
                TextColumn::make('helpful_answers')
                    ->label('Respuestas Útiles')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('accepted_solutions')
                    ->label('Soluciones Aceptadas')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('upvotes_received')
                    ->label('Upvotes')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('downvotes_received')
                    ->label('Downvotes')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('upvote_ratio')
                    ->label('Ratio Upvotes (%)')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state >= 40 => 'info',
                        default => 'danger',
                    }),
                
                IconColumn::make('is_verified_professional')
                    ->label('Profesional Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                IconColumn::make('is_suspended')
                    ->label('Suspendido')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                
                TextColumn::make('suspended_until')
                    ->label('Suspendido Hasta')
                    ->dateTime()
                    ->placeholder('No suspendido')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('warnings_received')
                    ->label('Advertencias')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('topics_created')
                    ->label('Temas Creados')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('successful_projects')
                    ->label('Proyectos Exitosos')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('mentorship_points')
                    ->label('Puntos Mentoría')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_verified_professional')
                    ->label('Profesional Verificado')
                    ->options([
                        1 => 'Sí',
                        0 => 'No',
                    ]),
                
                Tables\Filters\SelectFilter::make('is_suspended')
                    ->label('Estado de Suspensión')
                    ->options([
                        1 => 'Suspendido',
                        0 => 'Activo',
                    ]),
                
                Tables\Filters\Filter::make('reputation_range')
                    ->form([
                        TextInput::make('min_reputation')
                            ->label('Reputación Mínima')
                            ->numeric(),
                        TextInput::make('max_reputation')
                            ->label('Reputación Máxima')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_reputation'],
                                fn (Builder $query, $min): Builder => $query->where('total_reputation', '>=', $min),
                            )
                            ->when(
                                $data['max_reputation'],
                                fn (Builder $query, $max): Builder => $query->where('total_reputation', '<=', $max),
                            );
                    }),
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
            ->defaultSort('total_reputation', 'desc');
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
            'index' => Pages\ListUserReputations::route('/'),
            'create' => Pages\CreateUserReputation::route('/create'),
            'edit' => Pages\EditUserReputation::route('/{record}/edit'),
        ];
    }
}
