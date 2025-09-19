<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrendingTopicResource\Pages;
use App\Filament\Resources\TrendingTopicResource\RelationManagers;
use App\Models\TrendingTopic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrendingTopicResource extends Resource
{
    protected static ?string $model = TrendingTopic::class;

    protected static ?string $navigationIcon = 'fas-fire';

    protected static ?string $navigationGroup = 'Contenido y Medios';

    protected static ?string $navigationLabel = 'Temas Trending';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Tema Trending';

    protected static ?string $pluralModelLabel = 'Temas Trending';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('topic')
                            ->required()
                            ->maxLength(255)
                            ->label('Tema Trending')
                            ->placeholder('Título del tema trending...'),
                        
                        Forms\Components\TextInput::make('trending_score')
                            ->numeric()
                            ->label('Puntuación Trending')
                            ->placeholder('Puntuación de trending...')
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('mentions_count')
                            ->numeric()
                            ->label('Número de Menciones')
                            ->placeholder('Número de menciones...'),
                        
                        Forms\Components\TextInput::make('growth_rate')
                            ->numeric()
                            ->label('Tasa de Crecimiento (%)')
                            ->placeholder('Porcentaje de crecimiento...')
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('geographic_spread')
                            ->label('Distribución Geográfica')
                            ->placeholder('Regiones donde es trending...'),
                        
                        Forms\Components\Select::make('category')
                            ->options([
                                'politics' => '🏛️ Política',
                                'technology' => '💻 Tecnología',
                                'entertainment' => '🎬 Entretenimiento',
                                'sports' => '⚽ Deportes',
                                'business' => '💼 Negocios',
                                'health' => '🏥 Salud',
                                'science' => '🔬 Ciencia',
                                'environment' => '🌍 Medio Ambiente',
                                'education' => '🎓 Educación',
                                'culture' => '🎨 Cultura',
                                'social_issues' => '🤝 Temas Sociales',
                                'crime' => '🚨 Crimen',
                                'weather' => '🌤️ Clima',
                                'travel' => '✈️ Viajes',
                                'food' => '🍕 Comida',
                                'fashion' => '👗 Moda',
                                'automotive' => '🚗 Automóviles',
                                'finance' => '💰 Finanzas',
                                'real_estate' => '🏠 Bienes Raíces',
                                'energy' => '⚡ Energía',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Categoría del Tema'),
                        
                        Forms\Components\Textarea::make('related_keywords')
                            ->maxLength(500)
                            ->label('Palabras Clave Relacionadas')
                            ->rows(2)
                            ->placeholder('Palabras clave relacionadas...'),
                        
                        Forms\Components\Textarea::make('geographic_data')
                            ->maxLength(500)
                            ->label('Datos Geográficos')
                            ->rows(2)
                            ->placeholder('Datos geográficos específicos...'),
                        
                        Forms\Components\DateTimePicker::make('peak_time')
                            ->label('Hora del Pico')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Cuándo alcanzó su pico'),
                        
                        Forms\Components\TextInput::make('peak_score')
                            ->numeric()
                            ->label('Puntuación del Pico')
                            ->placeholder('Puntuación máxima alcanzada...')
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\Textarea::make('trend_analysis')
                            ->maxLength(1000)
                            ->label('Análisis de la Tendencia')
                            ->rows(3)
                            ->placeholder('Análisis de por qué es trending...'),
                        
                        Forms\Components\Toggle::make('is_breaking')
                            ->label('Es Breaking News')
                            ->default(false)
                            ->helperText('El tema es una noticia de última hora'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('topic')
                    ->label('Tema')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'politics',
                        'success' => 'technology',
                        'warning' => 'entertainment',
                        'info' => 'sports',
                        'danger' => 'business',
                        'secondary' => 'health',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'politics' => '🏛️ Política',
                        'technology' => '💻 Tecnología',
                        'entertainment' => '🎬 Entretenimiento',
                        'sports' => '⚽ Deportes',
                        'business' => '💼 Negocios',
                        'health' => '🏥 Salud',
                        'science' => '🔬 Ciencia',
                        'environment' => '🌍 Medio Ambiente',
                        'education' => '🎓 Educación',
                        'culture' => '🎨 Cultura',
                        'social_issues' => '🤝 Temas Sociales',
                        'crime' => '🚨 Crimen',
                        'weather' => '🌤️ Clima',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'fashion' => '👗 Moda',
                        'automotive' => '🚗 Automóviles',
                        'finance' => '💰 Finanzas',
                        'real_estate' => '🏠 Bienes Raíces',
                        'energy' => '⚡ Energía',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('trending_score')
                    ->label('Puntuación')
                    ->numeric()
                    ->sortable()
                    ->suffix('/100')
                    ->color(fn (float $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'info',
                        $state >= 40 => 'warning',
                        $state >= 20 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('mentions_count')
                    ->label('Menciones')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 10000 => 'success',
                        $state >= 5000 => 'info',
                        $state >= 1000 => 'warning',
                        $state >= 100 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('growth_rate')
                    ->label('Crecimiento (%)')
                    ->numeric()
                    ->sortable()
                    ->suffix('%')
                    ->color(fn (float $state): string => match (true) {
                        $state >= 50 => 'success',
                        $state >= 25 => 'info',
                        $state >= 10 => 'warning',
                        $state >= 0 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('geographic_spread')
                    ->label('Distribución')
                    ->limit(30)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('peak_time')
                    ->label('Pico')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('peak_score')
                    ->label('Pico Score')
                    ->numeric()
                    ->sortable()
                    ->suffix('/100'),
                
                Tables\Columns\IconColumn::make('is_breaking')
                    ->label('Breaking')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'politics' => '🏛️ Política',
                        'technology' => '💻 Tecnología',
                        'entertainment' => '🎬 Entretenimiento',
                        'sports' => '⚽ Deportes',
                        'business' => '💼 Negocios',
                        'health' => '🏥 Salud',
                        'science' => '🔬 Ciencia',
                        'environment' => '🌍 Medio Ambiente',
                        'education' => '🎓 Educación',
                        'culture' => '🎨 Cultura',
                        'social_issues' => '🤝 Temas Sociales',
                        'crime' => '🚨 Crimen',
                        'weather' => '🌤️ Clima',
                        'travel' => '✈️ Viajes',
                        'food' => '🍕 Comida',
                        'fashion' => '👗 Moda',
                        'automotive' => '🚗 Automóviles',
                        'finance' => '💰 Finanzas',
                        'real_estate' => '🏠 Bienes Raíces',
                        'energy' => '⚡ Energía',
                        'other' => '❓ Otro',
                    ])
                    ->label('Categoría del Tema'),
                
                Tables\Filters\Filter::make('breaking_only')
                    ->label('Solo Breaking News')
                    ->query(fn (Builder $query): Builder => $query->where('is_breaking', true)),
                
                Tables\Filters\Filter::make('high_score')
                    ->label('Alta Puntuación (80+)')
                    ->query(fn (Builder $query): Builder => $query->where('trending_score', '>=', 80)),
                
                Tables\Filters\Filter::make('high_mentions')
                    ->label('Alto Volumen (5000+)')
                    ->query(fn (Builder $query): Builder => $query->where('mentions_count', '>=', 5000)),
                
                Tables\Filters\Filter::make('high_growth')
                    ->label('Alto Crecimiento (25%+)')
                    ->query(fn (Builder $query): Builder => $query->where('growth_rate', '>=', 25)),
                
                Tables\Filters\Filter::make('recent_trends')
                    ->label('Tendencias Recientes (24h)')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDay())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('fas-eye')
                    ->color('info'),
                
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('fas-edit')
                    ->color('warning'),
                
                Tables\Actions\Action::make('toggle_breaking')
                    ->label(fn ($record): string => $record->is_breaking ? 'Quitar Breaking' : 'Marcar Breaking')
                    ->icon(fn ($record): string => $record->is_breaking ? 'fas-fire' : 'fas-fire')
                    ->action(function ($record): void {
                        $record->update(['is_breaking' => !$record->is_breaking]);
                    })
                    ->color(fn ($record): string => $record->is_breaking ? 'danger' : 'success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('mark_breaking')
                        ->label('Marcar como Breaking')
                        ->icon('fas-fire')
                        ->action(function ($records): void {
                            $records->each->update(['is_breaking' => true]);
                        })
                        ->color('danger'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
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
            'index' => Pages\ListTrendingTopics::route('/'),
            'create' => Pages\CreateTrendingTopic::route('/create'),
            'view' => Pages\ViewTrendingTopic::route('/{record}'),
            'edit' => Pages\EditTrendingTopic::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}