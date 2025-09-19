<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceForecastResource\Pages;
use App\Models\PriceForecast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PriceForecastResource extends Resource
{
    protected static ?string $model = PriceForecast::class;

    protected static ?string $navigationIcon = 'fas-chart-line';

    protected static ?string $navigationGroup = 'Energía y Sostenibilidad';

    protected static ?string $navigationLabel = 'Pronósticos de Precios';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Pronóstico de Precio';

    protected static ?string $pluralModelLabel = 'Pronósticos de Precios';

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
                        Forms\Components\Select::make('energy_type')
                            ->options([
                                'electricity' => '⚡ Electricidad',
                                'gas' => '🔥 Gas Natural',
                                'oil' => '🛢️ Petróleo',
                                'coal' => '⛏️ Carbón',
                                'renewable' => '🌱 Renovable',
                                'nuclear' => '☢️ Nuclear',
                            ])
                            ->required()
                            ->label('Tipo de Energía'),
                        
                        Forms\Components\Select::make('zone')
                            ->options([
                                'peninsula' => '🇪🇸 Península',
                                'canarias' => '🏝️ Canarias',
                                'baleares' => '🏖️ Baleares',
                                'ceuta' => '🏛️ Ceuta',
                                'melilla' => '🏛️ Melilla',
                            ])
                            ->required()
                            ->label('Zona Geográfica'),
                        
                        Forms\Components\TextInput::make('forecast_model')
                            ->required()
                            ->maxLength(255)
                            ->label('Modelo de Pronóstico')
                            ->placeholder('ARIMA, LSTM, Prophet, etc.'),
                    ])->columns(2),

                Forms\Components\Section::make('Período del Pronóstico')
                    ->schema([
                        Forms\Components\DateTimePicker::make('forecast_time')
                            ->required()
                            ->label('Fecha del Pronóstico')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Cuándo se realizó el pronóstico'),
                        
                        Forms\Components\DateTimePicker::make('target_time')
                            ->required()
                            ->label('Fecha Objetivo')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Para cuándo es el pronóstico'),
                    ])->columns(2),

                Forms\Components\Section::make('Precios y Confianza')
                    ->schema([
                        Forms\Components\TextInput::make('predicted_price')
                            ->required()
                            ->numeric()
                            ->step(0.0001)
                            ->label('Precio Pronosticado')
                            ->suffix('EUR/MWh')
                            ->helperText('Precio pronosticado'),
                        
                        Forms\Components\TextInput::make('confidence_level')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(1)
                            ->label('Nivel de Confianza')
                            ->helperText('Entre 0 y 1 (ej: 0.85 = 85%)'),
                        
                        Forms\Components\TextInput::make('min_price')
                            ->numeric()
                            ->step(0.0001)
                            ->label('Precio Mínimo')
                            ->suffix('EUR/MWh')
                            ->helperText('Precio mínimo esperado'),
                        
                        Forms\Components\TextInput::make('max_price')
                            ->numeric()
                            ->step(0.0001)
                            ->label('Precio Máximo')
                            ->suffix('EUR/MWh')
                            ->helperText('Precio máximo esperado'),
                        
                        Forms\Components\TextInput::make('accuracy_score')
                            ->label('Puntuación de Precisión')
                            ->placeholder('0.85')
                            ->helperText('Puntuación de precisión histórica'),
                    ])->columns(2),

                Forms\Components\Section::make('Factores Considerados')
                    ->schema([
                        Forms\Components\KeyValue::make('factors')
                            ->label('Factores')
                            ->keyLabel('Factor')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Factor')
                            ->helperText('Factores que influyen en el pronóstico'),
                    ]),
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
                
                Tables\Columns\BadgeColumn::make('energy_type')
                    ->label('Tipo de Energía')
                    ->colors([
                        'warning' => 'electricity',
                        'info' => 'gas',
                        'success' => 'renewable',
                        'danger' => 'nuclear',
                        'secondary' => 'coal',
                        'primary' => 'oil',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'electricity' => '⚡ Electricidad',
                        'gas' => '🔥 Gas',
                        'oil' => '🛢️ Petróleo',
                        'coal' => '⛏️ Carbón',
                        'renewable' => '🌱 Renovable',
                        'nuclear' => '☢️ Nuclear',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('zone')
                    ->label('Zona')
                    ->colors([
                        'primary' => 'peninsula',
                        'info' => 'canarias',
                        'success' => 'baleares',
                        'warning' => 'ceuta',
                        'secondary' => 'melilla',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'peninsula' => '🇪🇸 Península',
                        'canarias' => '🏝️ Canarias',
                        'baleares' => '🏖️ Baleares',
                        'ceuta' => '🏛️ Ceuta',
                        'melilla' => '🏛️ Melilla',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('forecast_time')
                    ->label('Pronóstico')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('target_time')
                    ->label('Objetivo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('predicted_price')
                    ->label('Precio Pronosticado')
                    ->money('EUR')
                    ->sortable()
                    ->suffix('/MWh'),
                
                Tables\Columns\TextColumn::make('confidence_level')
                    ->label('Confianza')
                    ->formatStateUsing(fn (float $state): string => number_format($state * 100, 1) . '%')
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 0.9 => 'success',
                        $state >= 0.7 => 'info',
                        $state >= 0.5 => 'warning',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('forecast_model')
                    ->label('Modelo')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('min_price')
                    ->label('Mín')
                    ->money('EUR')
                    ->sortable()
                    ->suffix('/MWh'),
                
                Tables\Columns\TextColumn::make('max_price')
                    ->label('Máx')
                    ->money('EUR')
                    ->sortable()
                    ->suffix('/MWh'),
                
                Tables\Columns\TextColumn::make('accuracy_score')
                    ->label('Precisión')
                    ->formatStateUsing(fn (?string $state): string => $state ? number_format((float)$state * 100, 1) . '%' : 'N/A')
                    ->color(fn (?string $state): string => match (true) {
                        !$state => 'gray',
                        (float)$state >= 0.9 => 'success',
                        (float)$state >= 0.7 => 'info',
                        (float)$state >= 0.5 => 'warning',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('energy_type')
                    ->options([
                        'electricity' => '⚡ Electricidad',
                        'gas' => '🔥 Gas',
                        'oil' => '🛢️ Petróleo',
                        'coal' => '⛏️ Carbón',
                        'renewable' => '🌱 Renovable',
                        'nuclear' => '☢️ Nuclear',
                    ])
                    ->label('Tipo de Energía'),
                
                Tables\Filters\SelectFilter::make('zone')
                    ->options([
                        'peninsula' => '🇪🇸 Península',
                        'canarias' => '🏝️ Canarias',
                        'baleares' => '🏖️ Baleares',
                        'ceuta' => '🏛️ Ceuta',
                        'melilla' => '🏛️ Melilla',
                    ])
                    ->label('Zona'),
                
                Tables\Filters\Filter::make('high_confidence')
                    ->label('Alta Confianza (70%+)')
                    ->query(fn (Builder $query): Builder => $query->where('confidence_level', '>=', 0.7)),
                
                Tables\Filters\Filter::make('upcoming')
                    ->label('Próximos')
                    ->query(fn (Builder $query): Builder => $query->where('target_time', '>', now())),
                
                Tables\Filters\Filter::make('past')
                    ->label('Pasados')
                    ->query(fn (Builder $query): Builder => $query->where('target_time', '<', now())),
                
                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('target_time', today())),
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
            ->defaultSort('forecast_time', 'desc')
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
            'index' => Pages\ListPriceForecasts::route('/'),
            'create' => Pages\CreatePriceForecast::route('/create'),
            'view' => Pages\ViewPriceForecast::route('/{record}'),
            'edit' => Pages\EditPriceForecast::route('/{record}/edit'),
        ];
    }
}