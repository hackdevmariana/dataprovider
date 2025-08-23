<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarbonSavingRequestResource\Pages;
use App\Filament\Resources\CarbonSavingRequestResource\RelationManagers;
use App\Models\CarbonSavingRequest;
use App\Models\Province;
use App\Models\Municipality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class CarbonSavingRequestResource extends Resource
{
    protected static ?string $navigationGroup = 'Energy & Environment';
    protected static ?string $model = CarbonSavingRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Solicitud de Ahorro de Carbono';
    protected static ?string $pluralModelLabel = 'Solicitudes de Ahorro de Carbono';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Instalación')
                    ->description('Datos básicos de la instalación de energía renovable')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('installation_power_kw')
                                    ->label('Potencia de Instalación (kW)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->maxValue(100000)
                                    ->step(0.01)
                                    ->suffix('kW')
                                    ->placeholder('5.00')
                                    ->helperText('Potencia nominal de la instalación en kilovatios'),
                                
                                TextInput::make('production_kwh')
                                    ->label('Producción (kWh)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10000000)
                                    ->step(0.01)
                                    ->suffix('kWh')
                                    ->placeholder('4200')
                                    ->helperText('Producción real o estimada. Si se deja vacío, se calculará automáticamente'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Configuración del Período')
                    ->description('Definir el período de cálculo y fechas')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('period')
                                    ->label('Período de Cálculo')
                                    ->required()
                                    ->options([
                                        'annual' => 'Anual (8760 horas)',
                                        'monthly' => 'Mensual (730 horas)',
                                        'daily' => 'Diario (24 horas)',
                                    ])
                                    ->default('annual')
                                    ->helperText('Período para el que se realizará el cálculo'),
                                
                                DatePicker::make('start_date')
                                    ->label('Fecha de Inicio')
                                    ->placeholder('2025-01-01')
                                    ->helperText('Fecha de inicio del período (opcional)'),
                                
                                DatePicker::make('end_date')
                                    ->label('Fecha de Fin')
                                    ->placeholder('2025-12-31')
                                    ->helperText('Fecha de fin del período (opcional)'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Factores Regionales')
                    ->description('Ubicación para aplicar factores regionales específicos')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('province_id')
                                    ->label('Provincia')
                                    ->options(Province::pluck('name', 'id'))
                                    ->searchable()
                                    ->placeholder('Seleccionar provincia (opcional)')
                                    ->helperText('Provincia para aplicar factores regionales'),
                                
                                Select::make('municipality_id')
                                    ->label('Municipio')
                                    ->options(Municipality::pluck('name', 'id'))
                                    ->searchable()
                                    ->placeholder('Seleccionar municipio (opcional)')
                                    ->helperText('Municipio específico para factores más precisos'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Factores de Eficiencia')
                    ->description('Parámetros de eficiencia y pérdidas de la instalación')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('efficiency_ratio')
                                    ->label('Ratio de Eficiencia')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(1)
                                    ->step(0.0001)
                                    ->placeholder('0.85')
                                    ->suffix('%')
                                    ->helperText('Ratio de eficiencia de la instalación (0.0000 a 1.0000)'),
                                
                                TextInput::make('loss_factor')
                                    ->label('Factor de Pérdidas')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(1)
                                    ->step(0.0001)
                                    ->placeholder('0.05')
                                    ->suffix('%')
                                    ->helperText('Factor de pérdidas del sistema (0.0000 a 1.0000)'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Cálculos Automáticos')
                    ->description('Resultados calculados automáticamente')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('estimated_production')
                                    ->label('Producción Estimada')
                                    ->content(fn ($record) => $record ? $record->getFormattedProduction() : 'N/A'),
                                
                                Placeholder::make('carbon_savings')
                                    ->label('Ahorro de CO2')
                                    ->content(fn ($record) => $record ? $record->getFormattedCarbonSavings() : 'N/A'),
                                
                                Placeholder::make('regional_info')
                                    ->label('Información Regional')
                                    ->content(fn ($record) => $record ? $record->getRegionalInfo() : 'N/A'),
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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('installation_power_kw')
                    ->label('Potencia')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->getFormattedPower())
                    ->badge()
                    ->color('primary'),

                TextColumn::make('period')
                    ->label('Período')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->getPeriodLabel())
                    ->badge()
                    ->color('info'),

                TextColumn::make('production_kwh')
                    ->label('Producción (kWh)')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->production_kwh ? number_format($record->production_kwh, 2) : 'Calculada')
                    ->badge()
                    ->color('success'),

                TextColumn::make('estimated_production')
                    ->label('Producción Estimada')
                    ->getStateUsing(fn ($record) => $record->getFormattedProduction())
                    ->badge()
                    ->color('warning'),

                TextColumn::make('carbon_savings')
                    ->label('Ahorro CO2')
                    ->getStateUsing(fn ($record) => $record->getFormattedCarbonSavings())
                    ->badge()
                    ->color('success'),

                TextColumn::make('efficiency_ratio')
                    ->label('Eficiencia')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->getFormattedEfficiencyRatio())
                    ->badge()
                    ->color('info'),

                TextColumn::make('loss_factor')
                    ->label('Pérdidas')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->getFormattedLossFactor())
                    ->badge()
                    ->color('warning'),

                TextColumn::make('province.name')
                    ->label('Provincia')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->province?->name ?? 'Sin provincia')
                    ->badge()
                    ->color('secondary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('municipality.name')
                    ->label('Municipio')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->municipality?->name ?? 'Sin municipio')
                    ->badge()
                    ->color('secondary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('start_date')
                    ->label('Fecha Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('end_date')
                    ->label('Fecha Fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Período')
                    ->options([
                        'annual' => 'Anual',
                        'monthly' => 'Mensual',
                        'daily' => 'Diario',
                    ])
                    ->placeholder('Todos los períodos'),

                SelectFilter::make('province')
                    ->label('Provincia')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todas las provincias'),

                SelectFilter::make('municipality')
                    ->label('Municipio')
                    ->relationship('municipality', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los municipios'),

                TernaryFilter::make('has_regional_factors')
                    ->label('Con Factores Regionales')
                    ->placeholder('Todas las solicitudes')
                    ->trueLabel('Solo con factores regionales')
                    ->falseLabel('Solo sin factores regionales'),

                TernaryFilter::make('has_production')
                    ->label('Con Producción Específica')
                    ->placeholder('Todas las solicitudes')
                    ->trueLabel('Solo con producción específica')
                    ->falseLabel('Solo con producción calculada'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil'),

                Action::make('calculate_production')
                    ->label('Recalcular Producción')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->action(function ($record) {
                        $record->production_kwh = null; // Resetear para recalcular
                        $record->save();
                        
                        Notification::make()
                            ->title('Producción recalculada')
                            ->body('La producción se recalculará automáticamente')
                            ->success()
                            ->send();
                    })
                    ->tooltip('Recalcular la producción estimada'),

                Action::make('view_calculations')
                    ->label('Ver Cálculos')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->modalHeading('Detalles de Cálculos')
                    ->modalContent(view('filament.resources.carbon-saving-request.calculations', [
                        'request' => fn ($record) => $record
                    ]))
                    ->tooltip('Ver detalles de los cálculos realizados'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('recalculate_all')
                        ->label('Recalcular Todas')
                        ->icon('heroicon-o-calculator')
                        ->color('warning')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->production_kwh = null;
                                $record->save();
                                $count++;
                            }
                            
                            Notification::make()
                                ->title('Producción recalculada')
                                ->body("Se recalculó la producción de {$count} solicitudes")
                                ->success()
                                ->send();
                        })
                        ->tooltip('Recalcular la producción de todas las solicitudes seleccionadas'),
                ]),
            ])
            ->defaultSort('id', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListCarbonSavingRequests::route('/'),
            'create' => Pages\CreateCarbonSavingRequest::route('/create'),
            'edit' => Pages\EditCarbonSavingRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['province', 'municipality']);
    }
}
