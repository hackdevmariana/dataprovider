<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmissionFactorResource\Pages;
use App\Filament\Resources\EmissionFactorResource\RelationManagers;
use App\Models\EmissionFactor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmissionFactorResource extends Resource
{
    protected static ?string $model = EmissionFactor::class;
    protected static ?string $navigationGroup = 'Sustainability';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $modelLabel = 'Factor de Emisión';
    protected static ?string $pluralModelLabel = 'Factores de Emisión';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información de la Actividad')
                ->description('Define la actividad o fuente de emisión')
                ->schema([
                    Forms\Components\TextInput::make('activity')
                        ->label('Actividad')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ej: Electricidad - Red Nacional (España)')
                        ->helperText('Descripción clara de la actividad o fuente de emisión'),
                ])
                ->collapsible(false),

            Forms\Components\Section::make('Factor de Emisión')
                ->description('Valor del factor de emisión y su unidad')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('factor_kg_co2e_per_unit')
                                ->label('Factor (kg CO2e/unit)')
                                ->required()
                                ->numeric()
                                ->step(0.0001)
                                ->minValue(0)
                                ->maxValue(1000)
                                ->placeholder('0.2500')
                                ->helperText('Factor de emisión en kilogramos de CO2 equivalente por unidad'),

                            Forms\Components\TextInput::make('unit')
                                ->label('Unidad')
                                ->required()
                                ->maxLength(50)
                                ->placeholder('kWh, litro, km, kg, m³')
                                ->helperText('Unidad de medida de la actividad'),
                        ]),
                ])
                ->collapsible(false),

            Forms\Components\Section::make('Información Adicional')
                ->description('Datos calculados automáticamente')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Placeholder::make('formatted_factor')
                                ->label('Factor Formateado')
                                ->content(fn ($record) => $record?->formatted_factor ?? 'N/A'),

                            Forms\Components\Placeholder::make('factor_tonnes')
                                ->label('Factor en Toneladas')
                                ->content(fn ($record) => $record?->formatted_factor_tonnes ?? 'N/A'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Placeholder::make('activity_category')
                                ->label('Categoría')
                                ->content(fn ($record) => $record?->activity_category ?? 'N/A'),

                            Forms\Components\Placeholder::make('category_color')
                                ->label('Color de Categoría')
                                ->content(fn ($record) => $record?->category_color ?? 'N/A'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Ejemplos de Cálculo')
                ->description('Calculadora de emisiones para diferentes cantidades')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Placeholder::make('example_100')
                                ->label('Emisiones para 100 unidades')
                                ->content(fn ($record) => $record ? 
                                    $record->calculateEmissions(100) . ' kg CO2e (' . 
                                    $record->calculateEmissionsTonnes(100) . ' t CO2e)' : 'N/A'),

                            Forms\Components\Placeholder::make('example_1000')
                                ->label('Emisiones para 1000 unidades')
                                ->content(fn ($record) => $record ? 
                                    $record->calculateEmissions(1000) . ' kg CO2e (' . 
                                    $record->calculateEmissionsTonnes(1000) . ' t CO2e)' : 'N/A'),
                        ]),
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('activity')
                    ->label('Actividad')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->activity)
                    ->wrap(),

                Tables\Columns\TextColumn::make('factor_kg_co2e_per_unit')
                    ->label('Factor de Emisión')
                    ->searchable()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 4,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix(' kg CO2e/unit'),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Unidad')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('secondary'),

                Tables\Columns\BadgeColumn::make('activity_category')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->color(fn ($record) => $record->category_color ?? 'gray')
                    ->getStateUsing(fn ($record) => $record->activity_category ?? 'N/A'),

                Tables\Columns\TextColumn::make('formatted_factor')
                    ->label('Factor Completo')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->formatted_factor ?? 'N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('factor_tonnes_co2e_per_unit')
                    ->label('Factor (t CO2e)')
                    ->searchable()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 6,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix(' t CO2e/unit')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\SelectFilter::make('activity_category')
                    ->label('Categoría')
                    ->options([
                        'Energía' => 'Energía',
                        'Transporte' => 'Transporte',
                        'Combustibles' => 'Combustibles',
                        'Agua' => 'Agua',
                        'Residuos' => 'Residuos',
                        'Alimentación' => 'Alimentación',
                        'Materiales' => 'Materiales',
                        'Servicios' => 'Servicios',
                        'Otros' => 'Otros',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('unit')
                    ->label('Unidad')
                    ->options(fn () => EmissionFactor::distinct()->pluck('unit', 'unit')->toArray())
                    ->multiple()
                    ->searchable(),

                Tables\Filters\Filter::make('high_emission')
                    ->label('Alta Emisión')
                    ->query(fn (Builder $query) => $query->where('factor_kg_co2e_per_unit', '>', 10))
                    ->toggle(),

                Tables\Filters\Filter::make('low_emission')
                    ->label('Baja Emisión')
                    ->query(fn (Builder $query) => $query->where('factor_kg_co2e_per_unit', '<', 1))
                    ->toggle(),

                Tables\Filters\Filter::make('zero_emission')
                    ->label('Cero Emisiones')
                    ->query(fn (Builder $query) => $query->where('factor_kg_co2e_per_unit', '=', 0))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),

                Tables\Actions\Action::make('calculate_emissions')
                    ->label('Calcular Emisiones')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Cantidad')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('100'),
                        Forms\Components\Select::make('unit_type')
                            ->label('Tipo de Unidad')
                            ->options([
                                'kg' => 'Kilogramos CO2e',
                                'tonnes' => 'Toneladas CO2e',
                            ])
                            ->default('kg')
                            ->required(),
                    ])
                    ->action(function (EmissionFactor $record, array $data): void {
                        $amount = $data['amount'];
                        $unitType = $data['unit_type'];
                        
                        if ($unitType === 'tonnes') {
                            $result = $record->calculateEmissionsTonnes($amount);
                            $unit = 't CO2e';
                        } else {
                            $result = $record->calculateEmissions($amount);
                            $unit = 'kg CO2e';
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Cálculo Completado')
                            ->body("Emisiones para {$amount} {$record->unit}: {$result} {$unit}")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Calcular emisiones para una cantidad específica'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Eliminar Seleccionados'),
                
                Tables\Actions\BulkAction::make('categorize_selected')
                    ->label('Categorizar Seleccionados')
                    ->icon('heroicon-o-tag')
                    ->color('info')
                    ->action(function (Collection $records): void {
                        $count = $records->count();
                        \Filament\Notifications\Notification::make()
                            ->title('Categorización Completada')
                            ->body("Se han categorizado {$count} factores de emisión")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Categorizar automáticamente los factores seleccionados'),
            ])
            ->defaultSort('activity', 'asc')
            ->striped()
            ->paginated([25, 50, 100])
            ->searchable()
            ->searchPlaceholder('Buscar por actividad, categoría o unidad...');
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
            'index' => Pages\ListEmissionFactors::route('/'),
            'create' => Pages\CreateEmissionFactor::route('/create'),
            'edit' => Pages\EditEmissionFactor::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
