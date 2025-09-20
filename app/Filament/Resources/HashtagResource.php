<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HashtagResource\Pages;
use App\Models\Hashtag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HashtagResource extends Resource
{
    protected static ?string $model = Hashtag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationGroup = 'Contenido y Medios';

    protected static ?string $modelLabel = 'Hashtag';

    protected static ?string $pluralModelLabel = 'Hashtags';

    protected static ?int $navigationSort = 4;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Hashtag')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->prefix('#')
                            ->helperText('Sin el símbolo #'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->maxLength(500),
                        
                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'energy' => 'Energía',
                                'technology' => 'Tecnología',
                                'solar' => 'Solar',
                                'wind' => 'Eólica',
                                'storage' => 'Almacenamiento',
                                'grid' => 'Red Eléctrica',
                                'efficiency' => 'Eficiencia',
                                'sustainability' => 'Sostenibilidad',
                                'financing' => 'Financiación',
                                'legislation' => 'Legislación',
                                'diy' => 'Hazlo Tú Mismo',
                                'community' => 'Comunidad',
                                'news' => 'Noticias',
                                'education' => 'Educación',
                                'events' => 'Eventos',
                                'general' => 'General',
                            ])
                            ->searchable(),
                        
                        Forms\Components\ColorPicker::make('color')
                            ->label('Color')
                            ->default('#3B82F6'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Visibilidad')
                    ->schema([
                        Forms\Components\Toggle::make('is_trending')
                            ->label('En Tendencia')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(4),

                Forms\Components\Section::make('Metadatos')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Añadir metadato'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Hashtag')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => "#{$state}")
                    ->copyable()
                    ->copyMessage('Hashtag copiado')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->placeholder('Sin descripción')
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'warning' => 'energy',
                        'info' => 'technology',
                        'success' => 'solar',
                        'primary' => 'wind',
                        'gray' => 'general',
                    ])
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'Sin categoría'),
                
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
                
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Usos')
                    ->sortable()
                    ->numeric(),
                
                Tables\Columns\IconColumn::make('is_trending')
                    ->label('Tendencia')
                    ->boolean()
                    ->trueIcon('heroicon-o-fire')
                    ->falseIcon('heroicon-o-fire'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star'),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-check-badge'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'energy' => 'Energía',
                        'technology' => 'Tecnología',
                        'solar' => 'Solar',
                        'wind' => 'Eólica',
                        'general' => 'General',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_trending')
                    ->label('En Tendencia'),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
                
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),
                
                Tables\Filters\Filter::make('popular')
                    ->label('Populares (>10 usos)')
                    ->query(fn ($query) => $query->where('usage_count', '>', 10)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_trending')
                    ->label(fn (Hashtag $record) => $record->is_trending ? 'Quitar Tendencia' : 'Marcar Tendencia')
                    ->icon('heroicon-o-fire')
                    ->color(fn (Hashtag $record) => $record->is_trending ? 'gray' : 'danger')
                    ->action(fn (Hashtag $record) => $record->update(['is_trending' => !$record->is_trending])),
                
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn (Hashtag $record) => $record->is_featured ? 'Quitar Destacado' : 'Destacar')
                    ->icon('heroicon-o-star')
                    ->color(fn (Hashtag $record) => $record->is_featured ? 'gray' : 'warning')
                    ->action(fn (Hashtag $record) => $record->update(['is_featured' => !$record->is_featured])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('set_category')
                        ->label('Establecer Categoría')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\Select::make('category')
                                ->label('Categoría')
                                ->options([
                                    'energy' => 'Energía',
                                    'technology' => 'Tecnología',
                                    'solar' => 'Solar',
                                    'general' => 'General',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['category' => $data['category']]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('usage_count', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHashtags::route('/'),
            'create' => Pages\CreateHashtag::route('/create'),
            'edit' => Pages\EditHashtag::route('/{record}/edit'),
        ];
    }
}