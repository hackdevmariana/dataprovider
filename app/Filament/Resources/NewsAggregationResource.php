<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsAggregationResource\Pages;
use App\Filament\Resources\NewsAggregationResource\RelationManagers;
use App\Models\NewsAggregation;
use App\Models\NewsSource;
use App\Models\NewsArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class NewsAggregationResource extends Resource
{
    protected static ?string $model = NewsAggregation::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    
    protected static ?string $navigationLabel = 'Agregaciones de Noticias';
    
    protected static ?string $modelLabel = 'Agregación de Noticias';
    
    protected static ?string $pluralModelLabel = 'Agregaciones de Noticias';
    
    protected static ?string $navigationGroup = 'Contenido y Medios';
    
    protected static ?int $navigationSort = 4;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();
        
        return match (true) {
            $count >= 100 => 'success',
            $count >= 50 => 'warning',
            $count >= 20 => 'info',
            default => 'gray',
        };
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Agregación')
                    ->schema([
                        Forms\Components\Select::make('source_id')
                            ->label('Fuente de Noticias')
                            ->relationship('source', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Fuente de noticias de la cual se agregó el artículo'),
                        
                        Forms\Components\Select::make('article_id')
                            ->label('Artículo')
                            ->relationship('article', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Artículo que fue agregado'),
                        
                        Forms\Components\DateTimePicker::make('aggregated_at')
                            ->label('Fecha de Agregación')
                            ->required()
                            ->default(now())
                            ->helperText('Fecha y hora en que se agregó el artículo'),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('Estado del Procesamiento')
                    ->schema([
                        Forms\Components\Select::make('processing_status')
                            ->label('Estado del Procesamiento')
                            ->options([
                                'pending' => '⏳ Pendiente',
                                'processing' => '🔄 Procesando',
                                'completed' => '✅ Completado',
                                'failed' => '❌ Fallido',
                                'cancelled' => '🚫 Cancelado',
                            ])
                            ->required()
                            ->default('pending'),
                        
                        Forms\Components\Toggle::make('duplicate_check')
                            ->label('Verificación de Duplicados')
                            ->default(false)
                            ->helperText('Indica si se verificó si el artículo es duplicado'),
                        
                        Forms\Components\TextInput::make('quality_score')
                            ->label('Puntuación de Calidad')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.01)
                            ->placeholder('Puntuación de calidad del artículo (0.0 - 1.0)')
                            ->helperText('Puntuación de calidad del artículo procesado'),
                        
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('Fecha de Procesamiento')
                            ->placeholder('Fecha y hora en que se completó el procesamiento')
                            ->helperText('Fecha y hora en que se completó el procesamiento'),
                        
                        Forms\Components\Textarea::make('processing_notes')
                            ->label('Notas de Procesamiento')
                            ->rows(3)
                            ->placeholder('Notas sobre el procesamiento del artículo...')
                            ->helperText('Notas adicionales sobre el procesamiento'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Metadatos del Procesamiento')
                    ->schema([
                        Forms\Components\KeyValue::make('processing_metadata')
                            ->label('Metadatos del Procesamiento')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Metadato')
                            ->helperText('Metadatos adicionales del procesamiento (tiempo, idioma, sentimiento, etc.)'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('source.name')
                    ->label('Fuente')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('article.title')
                    ->label('Artículo')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                
                Tables\Columns\BadgeColumn::make('processing_status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'secondary' => 'cancelled',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'pending' => '⏳ Pendiente',
                            'processing' => '🔄 Procesando',
                            'completed' => '✅ Completado',
                            'failed' => '❌ Fallido',
                            'cancelled' => '🚫 Cancelado',
                            default => $state,
                        };
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('duplicate_check')
                    ->label('Duplicados')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('quality_score')
                    ->label('Calidad')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(function (?float $state): string {
                        if ($state === null) return 'N/A';
                        return number_format($state, 2);
                    })
                    ->color(function (?float $state): string {
                        if ($state === null) return 'gray';
                        return match (true) {
                            $state >= 0.8 => 'success',
                            $state >= 0.6 => 'warning',
                            default => 'danger',
                        };
                    }),
                
                Tables\Columns\TextColumn::make('aggregated_at')
                    ->label('Agregado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Procesado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('No procesado'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('processing_status')
                    ->label('Estado del Procesamiento')
                    ->options([
                        'pending' => '⏳ Pendiente',
                        'processing' => '🔄 Procesando',
                        'completed' => '✅ Completado',
                        'failed' => '❌ Fallido',
                        'cancelled' => '🚫 Cancelado',
                    ]),
                
                Tables\Filters\Filter::make('duplicate_check')
                    ->label('Con Verificación de Duplicados')
                    ->query(fn (Builder $query): Builder => $query->where('duplicate_check', true)),
                
                Tables\Filters\Filter::make('high_quality')
                    ->label('Alta Calidad')
                    ->query(fn (Builder $query): Builder => $query->where('quality_score', '>=', 0.8)),
                
                Tables\Filters\Filter::make('recent_aggregations')
                    ->label('Agregaciones Recientes')
                    ->query(fn (Builder $query): Builder => $query->where('aggregated_at', '>=', now()->subDays(7))),
                
                Tables\Filters\Filter::make('processed_today')
                    ->label('Procesados Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('processed_at', today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar Seleccionados'),
                ]),
            ])
            ->defaultSort('aggregated_at', 'desc')
            ->poll('30s');
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
            'index' => Pages\ListNewsAggregations::route('/'),
            'create' => Pages\CreateNewsAggregation::route('/create'),
            'view' => Pages\ViewNewsAggregation::route('/{record}'),
            'edit' => Pages\EditNewsAggregation::route('/{record}/edit'),
        ];
    }
}
