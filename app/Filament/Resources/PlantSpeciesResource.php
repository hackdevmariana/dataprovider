<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlantSpeciesResource\Pages;
use App\Filament\Resources\PlantSpeciesResource\RelationManagers;
use App\Models\PlantSpecies;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class PlantSpeciesResource extends Resource
{
    protected static ?string $model = PlantSpecies::class;
    protected static ?string $navigationIcon = 'phosphor-plant-bold';
    protected static ?string $navigationGroup = 'Sustainability';
    protected static ?string $modelLabel = 'Especie Vegetal';
    protected static ?string $pluralModelLabel = 'Especies Vegetales';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Básica')
                ->description('Datos principales de la especie')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nombre Común')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Ej: Encina, Roble, Pino...'),

                            Forms\Components\TextInput::make('scientific_name')
                                ->label('Nombre Científico')
                                ->maxLength(255)
                                ->placeholder('Ej: Quercus ilex'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('family')
                                ->label('Familia Botánica')
                                ->maxLength(255)
                                ->placeholder('Ej: Fagaceae, Pinaceae...'),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Identificador único para URLs'),
                        ]),

                    Forms\Components\Textarea::make('description')
                        ->label('Descripción')
                        ->rows(3)
                        ->placeholder('Descripción detallada de la especie...'),
                ])
                ->collapsible(false),

            Forms\Components\Section::make('Clasificación y Tamaño')
                ->description('Características físicas y taxonómicas')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('plant_type')
                                ->label('Tipo de Planta')
                                ->options([
                                    'tree' => 'Árbol',
                                    'shrub' => 'Arbusto',
                                    'herb' => 'Hierba',
                                    'grass' => 'Gramínea',
                                    'vine' => 'Enredadera',
                                    'palm' => 'Palmera',
                                    'conifer' => 'Conífera',
                                    'fern' => 'Helecho',
                                    'succulent' => 'Suculenta',
                                    'bamboo' => 'Bambú',
                                ])
                                ->default('tree')
                                ->required(),

                            Forms\Components\Select::make('size_category')
                                ->label('Categoría de Tamaño')
                                ->options([
                                    'small' => 'Pequeño',
                                    'medium' => 'Mediano',
                                    'large' => 'Grande',
                                    'giant' => 'Gigante',
                                ])
                                ->default('medium')
                                ->required(),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('height_min')
                                ->label('Altura Mínima (m)')
                                ->numeric()
                                ->step(0.1)
                                ->minValue(0)
                                ->suffix('metros'),

                            Forms\Components\TextInput::make('height_max')
                                ->label('Altura Máxima (m)')
                                ->numeric()
                                ->step(0.1)
                                ->minValue(0)
                                ->suffix('metros'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('lifespan_years')
                                ->label('Esperanza de Vida (años)')
                                ->numeric()
                                ->step(1)
                                ->minValue(1)
                                ->suffix('años'),

                            Forms\Components\TextInput::make('growth_rate_cm_year')
                                ->label('Velocidad de Crecimiento (cm/año)')
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->suffix('cm/año'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Absorción de CO2')
                ->description('Capacidad de captura de carbono')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('co2_absorption_kg_per_year')
                                ->label('Absorción CO2 (kg/año)')
                                ->numeric()
                                ->step(0.1)
                                ->minValue(0)
                                ->required()
                                ->suffix('kg CO2/año'),

                            Forms\Components\TextInput::make('co2_absorption_min')
                                ->label('Absorción Mínima (kg/año)')
                                ->numeric()
                                ->step(0.1)
                                ->minValue(0)
                                ->suffix('kg CO2/año'),

                            Forms\Components\TextInput::make('co2_absorption_max')
                                ->label('Absorción Máxima (kg/año)')
                                ->numeric()
                                ->step(0.1)
                                ->minValue(0)
                                ->suffix('kg CO2/año'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Condiciones Ambientales')
                ->description('Requisitos de clima y suelo')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\KeyValue::make('climate_zones')
                                ->label('Zonas Climáticas')
                                ->keyLabel('Zona')
                                ->valueLabel('Descripción')
                                ->addActionLabel('Añadir Zona'),

                            Forms\Components\TextInput::make('soil_types')
                                ->label('Tipos de Suelo')
                                ->maxLength(500)
                                ->placeholder('Descripción de los tipos de suelo preferidos...'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('water_needs_mm_year')
                                ->label('Necesidades Hídricas (mm/año)')
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->suffix('mm/año'),

                            Forms\Components\TextInput::make('survival_rate_percent')
                                ->label('Tasa de Supervivencia (%)')
                                ->numeric()
                                ->step(1)
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('flowering_season')
                                ->label('Época de Floración')
                                ->maxLength(100)
                                ->placeholder('Ej: Primavera, Verano...'),

                            Forms\Components\TextInput::make('fruit_season')
                                ->label('Época de Fructificación')
                                ->maxLength(100)
                                ->placeholder('Ej: Otoño, Invierno...'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Características Especiales')
                ->description('Propiedades y usos de la especie')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('drought_resistant')
                                ->label('Resistente a Sequía')
                                ->default(false),

                            Forms\Components\Toggle::make('frost_resistant')
                                ->label('Resistente a Heladas')
                                ->default(false),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('is_endemic')
                                ->label('Especie Endémica')
                                ->default(false),

                            Forms\Components\Toggle::make('is_invasive')
                                ->label('Especie Invasiva')
                                ->default(false),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('suitable_for_reforestation')
                                ->label('Apta para Reforestación')
                                ->default(true),

                            Forms\Components\Toggle::make('suitable_for_urban')
                                ->label('Apta para Zonas Urbanas')
                                ->default(false),
                        ]),

                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Toggle::make('provides_food')
                                ->label('Proporciona Alimento')
                                ->default(false),

                            Forms\Components\Toggle::make('provides_timber')
                                ->label('Proporciona Madera')
                                ->default(false),

                            Forms\Components\Toggle::make('medicinal_use')
                                ->label('Uso Medicinal')
                                ->default(false),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Información Económica')
                ->description('Costes y valor económico')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('planting_cost_eur')
                                ->label('Coste de Plantación (€)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->prefix('€'),

                            Forms\Components\TextInput::make('maintenance_cost_eur_year')
                                ->label('Coste de Mantenimiento Anual (€)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->prefix('€'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Verificación y Fuentes')
                ->description('Datos de verificación científica')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('source')
                                ->label('Fuente de Datos')
                                ->default('manual')
                                ->maxLength(100),

                            Forms\Components\TextInput::make('source_url')
                                ->label('URL de la Fuente')
                                ->url()
                                ->maxLength(500)
                                ->placeholder('https://...'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('is_verified')
                                ->label('Verificado Científicamente')
                                ->default(false),

                            Forms\Components\TextInput::make('verification_entity')
                                ->label('Entidad Verificadora')
                                ->maxLength(255)
                                ->placeholder('Ej: MITECO, Universidad...'),
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

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre Común')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->name)
                    ->wrap(),

                Tables\Columns\TextColumn::make('scientific_name')
                    ->label('Nombre Científico')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->scientific_name)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('family')
                    ->label('Familia')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('secondary')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('co2_absorption_kg_per_year')
                    ->label('Absorción CO2')
                    ->searchable()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix(' kg/año')
                    ->color('success'),

                Tables\Columns\BadgeColumn::make('plant_type')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'primary' => 'tree',
                        'success' => 'shrub',
                        'warning' => 'herb',
                        'info' => 'conifer',
                        'secondary' => 'grass',
                        'danger' => 'vine',
                    ]),

                Tables\Columns\BadgeColumn::make('size_category')
                    ->label('Tamaño')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'gray' => 'small',
                        'info' => 'medium',
                        'warning' => 'large',
                        'danger' => 'giant',
                    ]),

                Tables\Columns\TextColumn::make('height_range')
                    ->label('Altura')
                    ->getStateUsing(fn ($record) => 
                        $record->height_min && $record->height_max 
                            ? "{$record->height_min}-{$record->height_max} m"
                            : 'N/A'
                    )
                    ->sortable(query: fn (Builder $query, string $direction): Builder => 
                        $query->orderBy('height_max', $direction)
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('lifespan_years')
                    ->label('Vida (años)')
                    ->searchable()
                    ->sortable()
                    ->numeric()
                    ->suffix(' años')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('planting_cost_eur')
                    ->label('Coste Plantación')
                    ->searchable()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->prefix('€')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('drought_resistant')
                    ->label('Resistente Sequía')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_endemic')
                    ->label('Endémica')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('suitable_for_reforestation')
                    ->label('Apta Reforestación')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificada')
                    ->boolean()
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
                Tables\Filters\SelectFilter::make('plant_type')
                    ->label('Tipo de Planta')
                    ->options([
                        'tree' => 'Árbol',
                        'shrub' => 'Arbusto',
                        'herb' => 'Hierba',
                        'grass' => 'Gramínea',
                        'vine' => 'Enredadera',
                        'palm' => 'Palmera',
                        'conifer' => 'Conífera',
                        'fern' => 'Helecho',
                        'succulent' => 'Suculenta',
                        'bamboo' => 'Bambú',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('size_category')
                    ->label('Categoría de Tamaño')
                    ->options([
                        'small' => 'Pequeño',
                        'medium' => 'Mediano',
                        'large' => 'Grande',
                        'giant' => 'Gigante',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\Filter::make('high_co2_absorption')
                    ->label('Alta Absorción CO2')
                    ->query(fn (Builder $query) => $query->where('co2_absorption_kg_per_year', '>', 20))
                    ->toggle(),

                Tables\Filters\Filter::make('low_cost')
                    ->label('Bajo Coste')
                    ->query(fn (Builder $query) => $query->where('planting_cost_eur', '<', 10))
                    ->toggle(),

                Tables\Filters\Filter::make('drought_resistant_only')
                    ->label('Solo Resistentes a Sequía')
                    ->query(fn (Builder $query) => $query->where('drought_resistant', true))
                    ->toggle(),

                Tables\Filters\Filter::make('suitable_for_reforestation')
                    ->label('Aptas para Reforestación')
                    ->query(fn (Builder $query) => $query->where('suitable_for_reforestation', true))
                    ->toggle(),

                Tables\Filters\Filter::make('verified_only')
                    ->label('Solo Verificadas')
                    ->query(fn (Builder $query) => $query->where('is_verified', true))
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

                Tables\Actions\Action::make('calculate_co2_impact')
                    ->label('Calcular Impacto CO2')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('years')
                            ->label('Años de Crecimiento')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(10)
                            ->suffix('años'),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad de Plantas')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(100)
                            ->suffix('plantas'),
                    ])
                    ->action(function (PlantSpecies $record, array $data): void {
                        $years = $data['years'];
                        $quantity = $data['quantity'];
                        $totalCo2 = $record->co2_absorption_kg_per_year * $years * $quantity;
                        $totalCost = $record->planting_cost_eur * $quantity;
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Cálculo de Impacto CO2')
                            ->body("En {$years} años, {$quantity} plantas de {$record->name} absorberán {$totalCo2} kg de CO2 por un coste total de {$totalCost} €")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Calcular el impacto de CO2 y costes para un proyecto'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Eliminar Seleccionadas'),
                
                Tables\Actions\BulkAction::make('export_co2_data')
                    ->label('Exportar Datos CO2')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (Collection $records): void {
                        $count = $records->count();
                        $totalCo2 = $records->sum('co2_absorption_kg_per_year');
                        \Filament\Notifications\Notification::make()
                            ->title('Datos Exportados')
                            ->body("Se han exportado {$count} especies con una absorción total de {$totalCo2} kg CO2/año")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Exportar datos de absorción de CO2'),
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->paginated([25, 50, 100])
            ->searchable()
            ->searchPlaceholder('Buscar por nombre, familia o tipo...');
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
            'index' => Pages\ListPlantSpecies::route('/'),
            'create' => Pages\CreatePlantSpecies::route('/create'),
            'edit' => Pages\EditPlantSpecies::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
