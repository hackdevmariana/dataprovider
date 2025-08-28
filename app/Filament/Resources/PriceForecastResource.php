<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceForecastResource\Pages;
use App\Filament\Resources\PriceForecastResource\RelationManagers;
use App\Models\PriceForecast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceForecastResource extends Resource
{
    protected static ?string $model = PriceForecast::class;

    protected static ?string $navigationIcon = 'fas-chart-line';

    protected static ?string $navigationGroup = 'Energía y Precios';

    protected static ?string $navigationLabel = 'Pronósticos de Precios';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Pronóstico de Precio';

    protected static ?string $pluralModelLabel = 'Pronósticos de Precios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('forecast_code')
                            ->maxLength(100)
                            ->label('Código del Pronóstico')
                            ->placeholder('Código único del pronóstico...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción del pronóstico de precios...'),
                        
                        Forms\Components\Select::make('energy_type')
                            ->options([
                                'electricity' => '⚡ Electricidad',
                                'gas' => '🔥 Gas Natural',
                                'lpg' => '🛢️ Gas Licuado',
                                'oil' => '🛢️ Petróleo',
                                'coal' => '⛏️ Carbón',
                                'biomass' => '🌱 Biomasa',
                                'solar' => '☀️ Solar',
                                'wind' => '💨 Eólico',
                                'hydro' => '💧 Hidroeléctrico',
                                'nuclear' => '☢️ Nuclear',
                                'geothermal' => '🌋 Geotérmico',
                                'hydrogen' => '⚗️ Hidrógeno',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Energía'),
                        
                        Forms\Components\Select::make('forecast_type')
                            ->options([
                                'short_term' => '📅 Corto Plazo',
                                'medium_term' => '📆 Medio Plazo',
                                'long_term' => '📅 Largo Plazo',
                                'seasonal' => '🍂 Estacional',
                                'annual' => '📅 Anual',
                                'trend' => '📈 Tendencia',
                                'scenario' => '🎭 Escenario',
                                'baseline' => '📊 Línea Base',
                                'optimistic' => '😊 Optimista',
                                'pessimistic' => '😔 Pesimista',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Tipo de Pronóstico'),
                        
                        Forms\Components\Select::make('methodology')
                            ->options([
                                'statistical' => '📊 Estadístico',
                                'econometric' => '📈 Econométrico',
                                'machine_learning' => '🤖 Machine Learning',
                                'expert_opinion' => '👨‍💼 Opinión de Expertos',
                                'market_analysis' => '📊 Análisis de Mercado',
                                'fundamental' => '🏗️ Fundamental',
                                'technical' => '⚙️ Técnico',
                                'hybrid' => '🔄 Híbrido',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->label('Metodología'),
                    ])->columns(2),

                Forms\Components\Section::make('Período del Pronóstico')
                    ->schema([
                        Forms\Components\DatePicker::make('forecast_date')
                            ->required()
                            ->label('Fecha del Pronóstico')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha cuando se realizó el pronóstico'),
                        
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->label('Fecha de Inicio')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha de inicio del período pronosticado'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->label('Fecha de Fin')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha de fin del período pronosticado'),
                        
                        Forms\Components\TextInput::make('forecast_horizon')
                            ->maxLength(100)
                            ->label('Horizonte del Pronóstico')
                            ->placeholder('1 mes, 3 meses, 1 año...'),
                        
                        Forms\Components\TextInput::make('update_frequency')
                            ->maxLength(100)
                            ->label('Frecuencia de Actualización')
                            ->placeholder('Diaria, semanal, mensual...'),
                        
                        Forms\Components\Toggle::make('is_updated')
                            ->label('Se Actualiza')
                            ->default(false)
                            ->helperText('El pronóstico se actualiza regularmente'),
                    ])->columns(2),

                Forms\Components\Section::make('Precios y Valores')
                    ->schema([
                        Forms\Components\TextInput::make('current_price')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->label('Precio Actual')
                            ->helperText('Precio actual al momento del pronóstico'),
                        
                        Forms\Components\TextInput::make('forecasted_price')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->label('Precio Pronosticado')
                            ->helperText('Precio pronosticado para el período'),
                        
                        Forms\Components\TextInput::make('price_change')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->label('Cambio de Precio')
                            ->helperText('Cambio esperado en el precio'),
                        
                        Forms\Components\TextInput::make('price_change_percentage')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('%')
                            ->label('Cambio Porcentual')
                            ->helperText('Cambio porcentual esperado'),
                        
                        Forms\Components\Select::make('price_unit')
                            ->options([
                                '€/kWh' => '€/kWh (Euro por kilovatio-hora)',
                                '€/MWh' => '€/MWh (Euro por megavatio-hora)',
                                '€/m³' => '€/m³ (Euro por metro cúbico)',
                                '€/l' => '€/l (Euro por litro)',
                                '€/kg' => '€/kg (Euro por kilogramo)',
                                '€/therm' => '€/therm (Euro por termia)',
                                '€/GJ' => '€/GJ (Euro por gigajulio)',
                                'other' => 'Otro',
                            ])
                            ->required()
                            ->label('Unidad de Precio'),
                        
                        Forms\Components\TextInput::make('confidence_level')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->label('Nivel de Confianza')
                            ->helperText('Nivel de confianza del pronóstico'),
                    ])->columns(2),

                Forms\Components\Section::make('Factores y Variables')
                    ->schema([
                        Forms\Components\Textarea::make('key_factors')
                            ->maxLength(1000)
                            ->label('Factores Clave')
                            ->rows(3)
                            ->placeholder('Factores principales que influyen en el precio...'),
                        
                        Forms\Components\Textarea::make('assumptions')
                            ->maxLength(1000)
                            ->label('Suposiciones')
                            ->rows(3)
                            ->placeholder('Suposiciones realizadas para el pronóstico...'),
                        
                        Forms\Components\KeyValue::make('variables')
                            ->label('Variables Consideradas')
                            ->keyLabel('Variable')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Variable'),
                        
                        Forms\Components\Textarea::make('constraints')
                            ->maxLength(500)
                            ->label('Restricciones')
                            ->rows(2)
                            ->placeholder('Restricciones del modelo...'),
                        
                        Forms\Components\Textarea::make('uncertainty_factors')
                            ->maxLength(500)
                            ->label('Factores de Incertidumbre')
                            ->rows(2)
                            ->placeholder('Factores que aumentan la incertidumbre...'),
                    ])->columns(1),

                Forms\Components\Section::make('Análisis y Resultados')
                    ->schema([
                        Forms\Components\Textarea::make('analysis_summary')
                            ->maxLength(1000)
                            ->label('Resumen del Análisis')
                            ->rows(3)
                            ->placeholder('Resumen de los hallazgos principales...'),
                        
                        Forms\Components\Textarea::make('key_findings')
                            ->maxLength(1000)
                            ->label('Hallazgos Clave')
                            ->rows(3)
                            ->placeholder('Hallazgos más importantes del pronóstico...'),
                        
                        Forms\Components\Textarea::make('implications')
                            ->maxLength(500)
                            ->label('Implicaciones')
                            ->rows(2)
                            ->placeholder('Implicaciones del pronóstico...'),
                        
                        Forms\Components\Textarea::make('recommendations')
                            ->maxLength(500)
                            ->label('Recomendaciones')
                            ->rows(2)
                            ->placeholder('Recomendaciones basadas en el pronóstico...'),
                        
                        Forms\Components\KeyValue::make('scenarios')
                            ->label('Escenarios')
                            ->keyLabel('Escenario')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Escenario'),
                    ])->columns(1),

                Forms\Components\Section::make('Calidad y Validación')
                    ->schema([
                        Forms\Components\Select::make('accuracy_rating')
                            ->options([
                                'excellent' => '🟢 Excelente (95%+)',
                                'very_good' => '🟢 Muy Bueno (90-94%)',
                                'good' => '🟡 Bueno (80-89%)',
                                'fair' => '🟠 Regular (70-79%)',
                                'poor' => '🔴 Pobre (<70%)',
                                'not_assessed' => '⚫ No Evaluado',
                            ])
                            ->label('Calificación de Precisión'),
                        
                        Forms\Components\TextInput::make('historical_accuracy')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->label('Precisión Histórica')
                            ->helperText('Precisión de pronósticos anteriores'),
                        
                        Forms\Components\TextInput::make('validation_method')
                            ->maxLength(255)
                            ->label('Método de Validación')
                            ->placeholder('Backtesting, out-of-sample, etc...'),
                        
                        Forms\Components\Toggle::make('is_validated')
                            ->label('Validado')
                            ->default(false)
                            ->helperText('El pronóstico ha sido validado'),
                        
                        Forms\Components\DatePicker::make('validation_date')
                            ->label('Fecha de Validación')
                            ->displayFormat('d/m/Y')
                            ->visible(fn (callable $get): bool => $get('is_validated')),
                        
                        Forms\Components\TextInput::make('validated_by')
                            ->maxLength(255)
                            ->label('Validado por')
                            ->placeholder('Quién validó el pronóstico...')
                            ->visible(fn (callable $get): bool => $get('is_validated')),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Seguimiento')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => '📝 Borrador',
                                'active' => '✅ Activo',
                                'expired' => '❌ Expirado',
                                'superseded' => '🔄 Reemplazado',
                                'archived' => '📦 Archivado',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('draft')
                            ->label('Estado'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Pronóstico importante para destacar'),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Público')
                            ->default(false)
                            ->helperText('Accesible al público general'),
                        
                        Forms\Components\Toggle::make('requires_review')
                            ->label('Requiere Revisión')
                            ->default(false)
                            ->helperText('Necesita revisión antes de publicar'),
                        
                        Forms\Components\TextInput::make('reviewer')
                            ->maxLength(255)
                            ->label('Revisor')
                            ->placeholder('Persona responsable de la revisión...'),
                        
                        Forms\Components\DatePicker::make('review_date')
                            ->label('Fecha de Revisión')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas adicionales o comentarios...'),
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
                
                Tables\Columns\TextColumn::make('forecast_code')
                    ->label('Código')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('energy_type')
                    ->label('Tipo de Energía')
                    ->colors([
                        'warning' => 'electricity',
                        'info' => 'gas',
                        'success' => 'solar',
                        'primary' => 'wind',
                        'danger' => 'nuclear',
                        'secondary' => 'biomass',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'electricity' => '⚡ Electricidad',
                        'gas' => '🔥 Gas Natural',
                        'lpg' => '🛢️ Gas Licuado',
                        'oil' => '🛢️ Petróleo',
                        'coal' => '⛏️ Carbón',
                        'biomass' => '🌱 Biomasa',
                        'solar' => '☀️ Solar',
                        'wind' => '💨 Eólico',
                        'hydro' => '💧 Hidroeléctrico',
                        'nuclear' => '☢️ Nuclear',
                        'geothermal' => '🌋 Geotérmico',
                        'hydrogen' => '⚗️ Hidrógeno',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('forecast_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'short_term',
                        'warning' => 'medium_term',
                        'info' => 'long_term',
                        'success' => 'seasonal',
                        'danger' => 'annual',
                        'secondary' => 'trend',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'short_term' => '📅 Corto Plazo',
                        'medium_term' => '📆 Medio Plazo',
                        'long_term' => '📅 Largo Plazo',
                        'seasonal' => '🍂 Estacional',
                        'annual' => '📅 Anual',
                        'trend' => '📈 Tendencia',
                        'scenario' => '🎭 Escenario',
                        'baseline' => '📊 Línea Base',
                        'optimistic' => '😊 Optimista',
                        'pessimistic' => '😔 Pesimista',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('forecast_date')
                    ->label('Fecha Pronóstico')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('current_price')
                    ->label('Precio Actual')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('forecasted_price')
                    ->label('Precio Pronosticado')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->forecasted_price > $record->current_price ? 'danger' : 'success'
                    ),
                
                Tables\Columns\TextColumn::make('price_change_percentage')
                    ->label('Cambio %')
                    ->numeric()
                    ->suffix('%')
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state > 10 => 'danger',
                        $state > 5 => 'warning',
                        $state > 0 => 'info',
                        $state < -10 => 'success',
                        $state < -5 => 'success',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('confidence_level')
                    ->label('Confianza')
                    ->numeric()
                    ->suffix('%')
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        $state >= 60 => 'danger',
                        default => 'secondary',
                    }),
                
                Tables\Columns\BadgeColumn::make('accuracy_rating')
                    ->label('Precisión')
                    ->colors([
                        'success' => 'excellent',
                        'info' => 'very_good',
                        'warning' => 'good',
                        'danger' => 'fair',
                        'secondary' => 'poor',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'excellent' => '🟢 Excelente',
                        'very_good' => '🟢 Muy Bueno',
                        'good' => '🟡 Bueno',
                        'fair' => '🟠 Regular',
                        'poor' => '🔴 Pobre',
                        'not_assessed' => '⚫ No Evaluado',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'danger' => 'expired',
                        'warning' => 'superseded',
                        'dark' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activo',
                        'expired' => '❌ Expirado',
                        'superseded' => '🔄 Reemplazado',
                        'archived' => '📦 Archivado',
                        'other' => '❓ Otro',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_validated')
                    ->label('Validado')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
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
                        'gas' => '🔥 Gas Natural',
                        'lpg' => '🛢️ Gas Licuado',
                        'oil' => '🛢️ Petróleo',
                        'coal' => '⛏️ Carbón',
                        'biomass' => '🌱 Biomasa',
                        'solar' => '☀️ Solar',
                        'wind' => '💨 Eólico',
                        'hydro' => '💧 Hidroeléctrico',
                        'nuclear' => '☢️ Nuclear',
                        'geothermal' => '🌋 Geotérmico',
                        'hydrogen' => '⚗️ Hidrógeno',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Energía'),
                
                Tables\Filters\SelectFilter::make('forecast_type')
                    ->options([
                        'short_term' => '📅 Corto Plazo',
                        'medium_term' => '📆 Medio Plazo',
                        'long_term' => '📅 Largo Plazo',
                        'seasonal' => '🍂 Estacional',
                        'annual' => '📅 Anual',
                        'trend' => '📈 Tendencia',
                        'scenario' => '🎭 Escenario',
                        'baseline' => '📊 Línea Base',
                        'optimistic' => '😊 Optimista',
                        'pessimistic' => '😔 Pesimista',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Pronóstico'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => '📝 Borrador',
                        'active' => '✅ Activo',
                        'expired' => '❌ Expirado',
                        'superseded' => '🔄 Reemplazado',
                        'archived' => '📦 Archivado',
                        'other' => '❓ Otro',
                    ])
                    ->label('Estado'),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('public_only')
                    ->label('Solo Públicos')
                    ->query(fn (Builder $query): Builder => $query->where('is_public', true)),
                
                Tables\Filters\Filter::make('validated_only')
                    ->label('Solo Validados')
                    ->query(fn (Builder $query): Builder => $query->where('is_validated', true)),
                
                Tables\Filters\Filter::make('high_confidence')
                    ->label('Alta Confianza (80%+)')
                    ->query(fn (Builder $query): Builder => $query->where('confidence_level', '>=', 80)),
                
                Tables\Filters\Filter::make('price_increase')
                    ->label('Aumento de Precio')
                    ->query(fn (Builder $query): Builder => $query->where('price_change_percentage', '>', 0)),
                
                Tables\Filters\Filter::make('price_decrease')
                    ->label('Disminución de Precio')
                    ->query(fn (Builder $query): Builder => $query->where('price_change_percentage', '<', 0)),
                
                Tables\Filters\Filter::make('active_forecasts')
                    ->label('Pronósticos Activos')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
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
                
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn ($record): string => $record->is_featured ? 'Quitar Destacado' : 'Destacar')
                    ->icon(fn ($record): string => $record->is_featured ? 'fas-star' : 'far-star')
                    ->action(function ($record): void {
                        $record->update(['is_featured' => !$record->is_featured]);
                    })
                    ->color(fn ($record): string => $record->is_featured ? 'warning' : 'success'),
                
                Tables\Actions\Action::make('toggle_public')
                    ->label(fn ($record): string => $record->is_public ? 'Hacer Privado' : 'Hacer Público')
                    ->icon(fn ($record): string => $record->is_public ? 'fas-eye-slash' : 'fas-eye')
                    ->action(function ($record): void {
                        $record->update(['is_public' => !$record->is_public]);
                    })
                    ->color(fn ($record): string => $record->is_public ? 'success' : 'secondary'),
                
                Tables\Actions\Action::make('mark_validated')
                    ->label('Marcar como Validado')
                    ->icon('fas-check-circle')
                    ->action(function ($record): void {
                        $record->update(['is_validated' => true]);
                    })
                    ->visible(fn ($record): bool => !$record->is_validated)
                    ->color('success'),
                
                Tables\Actions\Action::make('activate_forecast')
                    ->label('Activar')
                    ->icon('fas-play')
                    ->action(function ($record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success'),
                
                Tables\Actions\Action::make('archive_forecast')
                    ->label('Archivar')
                    ->icon('fas-archive')
                    ->action(function ($record): void {
                        $record->update(['status' => 'archived']);
                    })
                    ->visible(fn ($record): bool => $record->status !== 'archived')
                    ->color('secondary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Marcar como Destacados')
                        ->icon('fas-star')
                        ->action(function ($records): void {
                            $records->each->update(['is_featured' => true]);
                        })
                        ->color('warning'),
                    
                    Tables\Actions\BulkAction::make('mark_public')
                        ->label('Marcar como Públicos')
                        ->icon('fas-eye')
                        ->action(function ($records): void {
                            $records->each->update(['is_public' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_validated')
                        ->label('Marcar como Validados')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['is_validated' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('activate_all')
                        ->label('Activar Todos')
                        ->icon('fas-play')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'active']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('archive_all')
                        ->label('Archivar Todos')
                        ->icon('fas-archive')
                        ->action(function ($records): void {
                            $records->each->update(['status' => 'archived']);
                        })
                        ->color('secondary'),
                ]),
            ])
            ->defaultSort('forecast_date', 'desc')
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
