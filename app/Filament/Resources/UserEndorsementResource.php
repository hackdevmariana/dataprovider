<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserEndorsementResource\Pages;
use App\Filament\Resources\UserEndorsementResource\RelationManagers;
use App\Models\UserEndorsement;
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

class UserEndorsementResource extends Resource
{
    protected static ?string $navigationGroup = 'Usuarios y Social';
    protected static ?string $model = UserEndorsement::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Endorsements de Usuarios';
    protected static ?string $modelLabel = 'Endorsement';
    protected static ?string $pluralModelLabel = 'Endorsements';
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
                        Select::make('endorser_id')
                            ->label('Usuario que Endorsa')
                            ->relationship('endorser', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Select::make('endorsed_id')
                            ->label('Usuario Endorsado')
                            ->relationship('endorsed', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'disputed' => 'Disputado',
                                'rejected' => 'Rechazado',
                                'pending' => 'Pendiente',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Habilidades y Evaluación')
                    ->schema([
                        TextInput::make('skill_category')
                            ->label('Categoría de Habilidad')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('specific_skill')
                            ->label('Habilidad Específica')
                            ->maxLength(255),
                        
                        TextInput::make('skill_rating')
                            ->label('Calificación de Habilidad')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(1)
                            ->maxValue(5)
                            ->default(3.0),
                        
                        Textarea::make('endorsement_text')
                            ->label('Texto del Endorsement')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Section::make('Contexto y Relación')
                    ->schema([
                        TextInput::make('relationship_context')
                            ->label('Contexto de Relación')
                            ->maxLength(255),
                        
                        TextInput::make('project_context')
                            ->label('Contexto del Proyecto')
                            ->maxLength(255),
                        
                        TextInput::make('collaboration_duration_months')
                            ->label('Duración de Colaboración (meses)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(120),
                    ])
                    ->columns(3),

                Section::make('Configuración y Visibilidad')
                    ->schema([
                        Toggle::make('is_verified')
                            ->label('Verificado')
                            ->default(false),
                        
                        Toggle::make('is_public')
                            ->label('Público')
                            ->default(true),
                        
                        Toggle::make('show_on_profile')
                            ->label('Mostrar en Perfil')
                            ->default(true),
                        
                        Toggle::make('notify_endorsed')
                            ->label('Notificar al Endorsado')
                            ->default(true),
                        
                        Toggle::make('is_mutual')
                            ->label('Mutuo')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Métricas y Votos')
                    ->schema([
                        TextInput::make('trust_score')
                            ->label('Score de Confianza')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0),
                        
                        TextInput::make('helpful_votes')
                            ->label('Votos Útiles')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        
                        TextInput::make('total_votes')
                            ->label('Total de Votos')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(3),

                Section::make('Disputas')
                    ->schema([
                        Select::make('disputed_by')
                            ->label('Disputado por')
                            ->relationship('disputedBy', 'name')
                            ->searchable()
                            ->nullable(),
                        
                        Textarea::make('dispute_reason')
                            ->label('Razón de la Disputa')
                            ->rows(2)
                            ->maxLength(500),
                        
                        DateTimePicker::make('disputed_at')
                            ->label('Fecha de Disputa')
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('endorser.name')
                    ->label('Usuario que Endorsa')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('endorsed.name')
                    ->label('Usuario Endorsado')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('skill_category')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('specific_skill')
                    ->label('Habilidad Específica')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('Sin especificar'),
                
                TextColumn::make('skill_rating')
                    ->label('Calificación')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 3.5 => 'warning',
                        $state >= 2.5 => 'info',
                        default => 'danger',
                    }),
                
                TextColumn::make('trust_score')
                    ->label('Confianza (%)')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state >= 40 => 'info',
                        default => 'danger',
                    }),
                
                TextColumn::make('helpful_votes')
                    ->label('Votos Útiles')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('total_votes')
                    ->label('Total Votos')
                    ->numeric()
                    ->sortable(),
                
                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'disputed',
                        'danger' => 'rejected',
                        'secondary' => 'pending',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'disputed' => 'Disputado',
                        'rejected' => 'Rechazado',
                        'pending' => 'Pendiente',
                        default => $state,
                    }),
                
                IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye')
                    ->trueColor('info')
                    ->falseColor('gray'),
                
                IconColumn::make('is_mutual')
                    ->label('Mutuo')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-arrow-right')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                TextColumn::make('collaboration_duration_months')
                    ->label('Colaboración (meses)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('disputedBy.name')
                    ->label('Disputado por')
                    ->placeholder('Sin disputa')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('disputed_at')
                    ->label('Fecha Disputa')
                    ->dateTime()
                    ->placeholder('Sin disputa')
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'disputed' => 'Disputado',
                        'rejected' => 'Rechazado',
                        'pending' => 'Pendiente',
                    ]),
                
                Tables\Filters\SelectFilter::make('skill_category')
                    ->label('Categoría de Habilidad')
                    ->options(function () {
                        return UserEndorsement::distinct()
                            ->pluck('skill_category', 'skill_category')
                            ->filter()
                            ->toArray();
                    }),
                
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Público'),
                
                Tables\Filters\TernaryFilter::make('is_mutual')
                    ->label('Mutuo'),
                
                Tables\Filters\Filter::make('rating_range')
                    ->form([
                        TextInput::make('min_rating')
                            ->label('Calificación Mínima')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),
                        TextInput::make('max_rating')
                            ->label('Calificación Máxima')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_rating'],
                                fn (Builder $query, $min): Builder => $query->where('skill_rating', '>=', $min),
                            )
                            ->when(
                                $data['max_rating'],
                                fn (Builder $query, $max): Builder => $query->where('skill_rating', '<=', $max),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListUserEndorsements::route('/'),
            'create' => Pages\CreateUserEndorsement::route('/create'),
            'edit' => Pages\EditUserEndorsement::route('/{record}/edit'),
        ];
    }
}
