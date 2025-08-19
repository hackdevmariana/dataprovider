<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserBadgeResource\Pages;
use App\Models\UserBadge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class UserBadgeResource extends Resource
{
    protected static ?string $model = UserBadge::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Sistema Social';

    protected static ?string $modelLabel = 'Insignia de Usuario';

    protected static ?string $pluralModelLabel = 'Insignias de Usuarios';

    protected static ?int $navigationSort = 1;

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
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Insignia')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\Select::make('badge_type')
                            ->label('Tipo de Insignia')
                            ->options([
                                'bronze' => 'Bronce',
                                'silver' => 'Plata',
                                'gold' => 'Oro',
                                'platinum' => 'Platino',
                                'diamond' => 'Diamante',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'energy_saver' => 'Ahorrador de Energía',
                                'community_leader' => 'Líder Comunitario',
                                'expert_contributor' => 'Contribuidor Experto',
                                'project_creator' => 'Creador de Proyectos',
                                'helpful_member' => 'Miembro Útil',
                                'early_adopter' => 'Adoptador Temprano',
                                'sustainability_champion' => 'Campeón de Sostenibilidad',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Visual')
                    ->schema([
                        Forms\Components\TextInput::make('icon_url')
                            ->label('URL del Icono')
                            ->url()
                            ->maxLength(255),
                        
                        Forms\Components\ColorPicker::make('color')
                            ->label('Color')
                            ->default('#3B82F6'),
                        
                        Forms\Components\TextInput::make('points_awarded')
                            ->label('Puntos Otorgados')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columns(3),

                Forms\Components\Section::make('Criterios y Metadatos')
                    ->schema([
                        Forms\Components\KeyValue::make('criteria')
                            ->label('Criterios de Obtención')
                            ->keyLabel('Criterio')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir criterio'),
                        
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos Adicionales')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir metadato'),
                    ]),

                Forms\Components\Section::make('Fechas y Configuración')
                    ->schema([
                        Forms\Components\DateTimePicker::make('earned_at')
                            ->label('Fecha de Obtención')
                            ->required()
                            ->default(now()),
                        
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->helperText('Dejar vacío si la insignia no expira'),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Visible Públicamente')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada en Perfil')
                            ->default(false),
                    ])->columns(2),
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
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Insignia')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('badge_type')
                    ->label('Tipo')
                    ->colors([
                        'secondary' => 'bronze',
                        'gray' => 'silver',
                        'warning' => 'gold',
                        'info' => 'platinum',
                        'primary' => 'diamond',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bronze' => 'Bronce',
                        'silver' => 'Plata',
                        'gold' => 'Oro',
                        'platinum' => 'Platino',
                        'diamond' => 'Diamante',
                    }),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'success' => 'energy_saver',
                        'warning' => 'community_leader',
                        'primary' => 'expert_contributor',
                        'danger' => 'project_creator',
                        'info' => 'helpful_member',
                        'gray' => 'early_adopter',
                        'indigo' => 'sustainability_champion',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'energy_saver' => 'Ahorrador',
                        'community_leader' => 'Líder',
                        'expert_contributor' => 'Experto',
                        'project_creator' => 'Creador',
                        'helpful_member' => 'Útil',
                        'early_adopter' => 'Pionero',
                        'sustainability_champion' => 'Campeón',
                    }),
                
                Tables\Columns\TextColumn::make('points_awarded')
                    ->label('Puntos')
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Pública')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('earned_at')
                    ->label('Obtenida')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Nunca')
                    ->color(fn ($record) => $record && $record->isExpired() ? 'danger' : null),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('badge_type')
                    ->label('Tipo de Insignia')
                    ->options([
                        'bronze' => 'Bronce',
                        'silver' => 'Plata',
                        'gold' => 'Oro',
                        'platinum' => 'Platino',
                        'diamond' => 'Diamante',
                    ]),
                
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'energy_saver' => 'Ahorrador de Energía',
                        'community_leader' => 'Líder Comunitario',
                        'expert_contributor' => 'Contribuidor Experto',
                        'project_creator' => 'Creador de Proyectos',
                        'helpful_member' => 'Miembro Útil',
                        'early_adopter' => 'Adoptador Temprano',
                        'sustainability_champion' => 'Campeón de Sostenibilidad',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacada'),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Pública'),
                
                Tables\Filters\Filter::make('expired')
                    ->label('Expiradas')
                    ->query(fn ($query) => $query->whereNotNull('expires_at')->where('expires_at', '<', now())),
                
                Tables\Filters\Filter::make('valid')
                    ->label('Válidas')
                    ->query(fn ($query) => $query->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('earned_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Insignia')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario'),
                        
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),
                        
                        Infolists\Components\TextEntry::make('badge_type')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'bronze' => 'secondary',
                                'silver' => 'gray',
                                'gold' => 'warning',
                                'platinum' => 'info',
                                'diamond' => 'primary',
                            }),
                        
                        Infolists\Components\TextEntry::make('category')
                            ->label('Categoría')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'energy_saver' => 'Ahorrador de Energía',
                                'community_leader' => 'Líder Comunitario',
                                'expert_contributor' => 'Contribuidor Experto',
                                'project_creator' => 'Creador de Proyectos',
                                'helpful_member' => 'Miembro Útil',
                                'early_adopter' => 'Adoptador Temprano',
                                'sustainability_champion' => 'Campeón de Sostenibilidad',
                            }),
                        
                        Infolists\Components\TextEntry::make('points_awarded')
                            ->label('Puntos Otorgados'),
                    ])->columns(2),

                Infolists\Components\Section::make('Estado y Fechas')
                    ->schema([
                        Infolists\Components\TextEntry::make('earned_at')
                            ->label('Fecha de Obtención')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->dateTime()
                            ->placeholder('Nunca expira'),
                        
                        Infolists\Components\IconEntry::make('is_valid')
                            ->label('Estado')
                            ->formatStateUsing(fn ($record) => $record->isValid() ? 'Válida' : 'Expirada')
                            ->icon(fn ($record) => $record->isValid() ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->color(fn ($record) => $record->isValid() ? 'success' : 'danger'),
                        
                        Infolists\Components\IconEntry::make('is_featured')
                            ->label('Destacada')
                            ->boolean(),
                        
                        Infolists\Components\IconEntry::make('is_public')
                            ->label('Pública')
                            ->boolean(),
                    ])->columns(3),

                Infolists\Components\Section::make('Criterios de Obtención')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('criteria')
                            ->label('Criterios'),
                    ]),

                Infolists\Components\Section::make('Metadatos')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('metadata')
                            ->label('Información Adicional'),
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
            'index' => Pages\ListUserBadges::route('/'),
            'create' => Pages\CreateUserBadge::route('/create'),
            'edit' => Pages\EditUserBadge::route('/{record}/edit'),
        ];
    }
}