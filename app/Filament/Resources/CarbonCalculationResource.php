<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarbonCalculationResource\Pages;
use App\Filament\Resources\CarbonCalculationResource\RelationManagers;
use App\Models\CarbonCalculation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarbonCalculationResource extends Resource
{
    protected static ?string $navigationGroup = 'Energy & Environment';
    protected static ?string $model = CarbonCalculation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $label = 'Cálculo de Carbono';
    protected static ?string $pluralLabel = 'Cálculos de Carbono';
    protected static ?string $navigationLabel = 'Huella de Carbono';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cálculo')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Usuario anónimo si no está registrado'),
                        
                        Forms\Components\Select::make('carbon_equivalence_id')
                            ->label('Equivalencia de Carbono')
                            ->relationship('carbonEquivalence', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\TextInput::make('session_id')
                            ->label('ID de Sesión')
                            ->placeholder('Para usuarios anónimos')
                            ->helperText('Identificador único para cálculos sin usuario registrado'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Datos del Cálculo')
                    ->schema([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->step(0.001)
                            ->required()
                            ->helperText('Cantidad utilizada para el cálculo'),
                        
                        Forms\Components\TextInput::make('co2_result')
                            ->label('Resultado CO2 (kg)')
                            ->numeric()
                            ->step(0.001)
                            ->required()
                            ->helperText('Resultado del cálculo en kilogramos de CO2'),
                        
                        Forms\Components\TextInput::make('context')
                            ->label('Contexto')
                            ->placeholder('Descripción del contexto del cálculo')
                            ->helperText('Explicación de cuándo y por qué se realizó el cálculo'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Parámetros Adicionales')
                    ->schema([
                        Forms\Components\KeyValue::make('parameters')
                            ->label('Parámetros')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor')
                            ->helperText('Parámetros adicionales del cálculo (opcional)'),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Anónimo')
                    ->color('primary')
                    ->url(fn ($record) => $record->user_id ? route('filament.admin.resources.users.edit', $record->user_id) : null),
                
                Tables\Columns\TextColumn::make('carbonEquivalence.name')
                    ->label('Equivalencia')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('carbonEquivalence.category')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'energy' => 'info',
                        'transport' => 'warning',
                        'food' => 'success',
                        'other' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'energy' => 'Energía',
                        'transport' => 'Transporte',
                        'food' => 'Alimentación',
                        'other' => 'Otros',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->formatStateUsing(fn ($record) => $record->quantity . ' ' . $record->carbonEquivalence->unit)
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('co2_result')
                    ->label('CO2 (kg)')
                    ->numeric(
                        decimalPlaces: 3,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        $record->co2_result < 1 => 'success',
                        $record->co2_result < 5 => 'info',
                        $record->co2_result < 10 => 'warning',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('impact_level')
                    ->label('Nivel Impacto')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bajo' => 'success',
                        'medio' => 'info',
                        'alto' => 'warning',
                        'muy_alto' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bajo' => 'Bajo',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                        'muy_alto' => 'Muy Alto',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\TextColumn::make('context')
                    ->label('Contexto')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->context ?: 'Sin contexto';
                    })
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('session_id')
                    ->label('Sesión')
                    ->placeholder('Usuario registrado')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los usuarios'),
                
                Tables\Filters\SelectFilter::make('carbonEquivalence')
                    ->label('Equivalencia')
                    ->relationship('carbonEquivalence', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todas las equivalencias'),
                
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'energy' => 'Energía',
                        'transport' => 'Transporte',
                        'food' => 'Alimentación',
                        'other' => 'Otros',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['values'])) {
                            return $query->whereHas('carbonEquivalence', function ($q) use ($data) {
                                $q->whereIn('category', $data['values']);
                            });
                        }
                        return $query;
                    }),
                
                Tables\Filters\TernaryFilter::make('has_user')
                    ->label('Tipo de Usuario')
                    ->placeholder('Todos')
                    ->trueLabel('Solo registrados')
                    ->falseLabel('Solo anónimos'),
                
                Tables\Filters\Filter::make('co2_range')
                    ->label('Rango de CO2 (kg)')
                    ->form([
                        Forms\Components\TextInput::make('co2_from')
                            ->label('Desde')
                            ->numeric()
                            ->placeholder('Ej: 0'),
                        Forms\Components\TextInput::make('co2_to')
                            ->label('Hasta')
                            ->numeric()
                            ->placeholder('Ej: 10'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['co2_from'],
                                fn (Builder $query, $co2From): Builder => $query->where('co2_result', '>=', $co2From),
                            )
                            ->when(
                                $data['co2_to'],
                                fn (Builder $query, $co2To): Builder => $query->where('co2_result', '<=', $co2To),
                            );
                    }),
                
                Tables\Filters\Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_to')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $createdFrom): Builder => $query->whereDate('created_at', '>=', $createdFrom),
                            )
                            ->when(
                                $data['created_to'],
                                fn (Builder $query, $createdTo): Builder => $query->whereDate('created_at', '<=', $createdTo),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_equivalence')
                    ->label('Ver Equivalencia')
                    ->icon('heroicon-o-calculator')

                    ->url(fn ($record) => route('filament.admin.resources.carbon-equivalences.edit', $record->carbon_equivalence_id))
                    ->openUrlInNewTab()
                    ->color('success'),
                Tables\Actions\Action::make('view_user')
                    ->label('Ver Usuario')
                    ->icon('heroicon-o-user')
                    ->url(fn ($record) => $record->user_id ? route('filament.admin.resources.users.edit', $record->user_id) : null)
                    ->openUrlInNewTab()
                    ->color('info')
                    ->visible(fn ($record) => $record->user_id !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('export_calculations')
                    ->label('Exportar Cálculos')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($records) {
                        // Lógica de exportación (se puede implementar después)
                        \Filament\Notifications\Notification::make()
                            ->title('Exportación iniciada')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Exportar Cálculos')
                    ->modalDescription('¿Estás seguro de que quieres exportar estos cálculos?')
                    ->color('success'),
                Tables\Actions\BulkAction::make('calculate_total_co2')
                    ->label('Calcular Total CO2')
                    ->icon('heroicon-o-calculator')
                    ->action(function ($records) {
                        $totalCO2 = $records->sum('co2_result');
                        $averageCO2 = $records->avg('co2_result');
                        $treesNeeded = ceil($totalCO2 / 22);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Análisis de CO2 completado')
                            ->body("Total: " . round($totalCO2, 3) . " kg CO2\nPromedio: " . round($averageCO2, 3) . " kg CO2\nÁrboles necesarios: {$treesNeeded}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Calcular Total CO2')
                    ->modalDescription('¿Estás seguro de que quieres calcular el total de CO2 para estos cálculos?')
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'carbonEquivalence']));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'carbonEquivalence']);
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
            'index' => Pages\ListCarbonCalculations::route('/'),
            'create' => Pages\CreateCarbonCalculation::route('/create'),
            'edit' => Pages\EditCarbonCalculation::route('/{record}/edit'),
        ];
    }
}
