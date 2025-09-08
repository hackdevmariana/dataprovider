<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillSimulatorResource\Pages;
use App\Filament\Resources\BillSimulatorResource\RelationManagers;
use App\Models\BillSimulator;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class BillSimulatorResource extends Resource
{
    protected static ?string $model = BillSimulator::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    
    protected static ?string $navigationLabel = 'Simuladores de Facturas';
    
    protected static ?string $modelLabel = 'Simulador de Factura';
    
    protected static ?string $pluralModelLabel = 'Simuladores de Facturas';
    
    protected static ?string $navigationGroup = 'Energía';
    
    protected static ?int $navigationSort = 3;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();
        
        return match (true) {
            $count >= 50 => 'success',
            $count >= 20 => 'warning',
            $count >= 10 => 'info',
            default => 'gray',
        };
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Usuario')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('Configuración de la Simulación')
                    ->schema([
                        Forms\Components\Select::make('energy_type')
                            ->label('Tipo de Energía')
                            ->options([
                                'electricity' => 'Electricidad',
                                'gas' => 'Gas Natural',
                            ])
                            ->required()
                            ->default('electricity')
                            ->reactive(),
                        
                        Forms\Components\Select::make('zone')
                            ->label('Zona Geográfica')
                            ->options([
                                'peninsula' => 'Península',
                                'canary_islands' => 'Islas Canarias',
                                'balearic_islands' => 'Islas Baleares',
                            ])
                            ->required()
                            ->default('peninsula'),
                        
                        Forms\Components\Select::make('contract_type')
                            ->label('Tipo de Contrato')
                            ->options([
                                'fixed' => 'Tarifa Fija',
                                'variable' => 'Tarifa Variable',
                            ])
                            ->required()
                            ->default('fixed'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Consumo y Potencia')
                    ->schema([
                        Forms\Components\TextInput::make('monthly_consumption')
                            ->label('Consumo Mensual')
                            ->required()
                            ->numeric()
                            ->suffix(fn (Forms\Get $get) => $get('consumption_unit') ?? 'kWh')
                            ->helperText('Consumo promedio mensual'),
                        
                        Forms\Components\Select::make('consumption_unit')
                            ->label('Unidad de Consumo')
                            ->options([
                                'kWh' => 'kWh (kilovatios hora)',
                                'm³' => 'm³ (metros cúbicos)',
                            ])
                            ->required()
                            ->default('kWh')
                            ->reactive(),
                        
                        Forms\Components\TextInput::make('power_contracted')
                            ->label('Potencia Contratada (kW)')
                            ->numeric()
                            ->suffix('kW')
                            ->helperText('Solo para electricidad')
                            ->visible(fn (Forms\Get $get) => $get('energy_type') === 'electricity'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Detalles de la Tarifa')
                    ->schema([
                        Forms\Components\KeyValue::make('tariff_details')
                            ->label('Detalles de la Tarifa')
                            ->keyLabel('Concepto')
                            ->valueLabel('Valor')
                            ->helperText('Información específica de la tarifa aplicada'),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('Resultados de la Simulación')
                    ->schema([
                        Forms\Components\TextInput::make('estimated_monthly_bill')
                            ->label('Factura Mensual Estimada')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Importe estimado mensual'),
                        
                        Forms\Components\TextInput::make('estimated_annual_bill')
                            ->label('Factura Anual Estimada')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Importe estimado anual'),
                        
                        Forms\Components\KeyValue::make('breakdown')
                            ->label('Desglose de Costos')
                            ->keyLabel('Concepto')
                            ->valueLabel('Importe (€)')
                            ->helperText('Desglose detallado de los costos'),
                        
                        Forms\Components\DateTimePicker::make('simulation_date')
                            ->label('Fecha de Simulación')
                            ->required()
                            ->default(now())
                            ->helperText('Fecha en que se realizó la simulación'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Supuestos y Metadatos')
                    ->schema([
                        Forms\Components\KeyValue::make('assumptions')
                            ->label('Supuestos de la Simulación')
                            ->keyLabel('Supuesto')
                            ->valueLabel('Valor')
                            ->helperText('Supuestos utilizados en el cálculo'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\BadgeColumn::make('energy_type')
                    ->label('Tipo de Energía')
                    ->colors([
                        'success' => 'electricity',
                        'warning' => 'gas',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'electricity' => '⚡ Electricidad',
                            'gas' => '🔥 Gas Natural',
                            default => $state,
                        };
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('zone')
                    ->label('Zona')
                    ->colors([
                        'primary' => 'peninsula',
                        'secondary' => 'canary_islands',
                        'info' => 'balearic_islands',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'peninsula' => '🏔️ Península',
                            'canary_islands' => '🏝️ Canarias',
                            'balearic_islands' => '🏖️ Baleares',
                            default => $state,
                        };
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('monthly_consumption')
                    ->label('Consumo Mensual')
                    ->numeric()
                    ->sortable()
                    ->suffix(fn ($record) => ' ' . $record->consumption_unit)
                    ->weight('medium'),
                
                Tables\Columns\BadgeColumn::make('contract_type')
                    ->label('Contrato')
                    ->colors([
                        'success' => 'fixed',
                        'warning' => 'variable',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'fixed' => '🔒 Fijo',
                            'variable' => '📈 Variable',
                            default => $state,
                        };
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('power_contracted')
                    ->label('Potencia (kW)')
                    ->numeric()
                    ->sortable()
                    ->suffix(' kW')
                    ->placeholder('N/A')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('estimated_monthly_bill')
                    ->label('Factura Mensual')
                    ->money('EUR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('estimated_annual_bill')
                    ->label('Factura Anual')
                    ->money('EUR')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('simulation_date')
                    ->label('Fecha Simulación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                
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
                Tables\Filters\SelectFilter::make('energy_type')
                    ->label('Tipo de Energía')
                    ->options([
                        'electricity' => 'Electricidad',
                        'gas' => 'Gas Natural',
                    ]),
                
                Tables\Filters\SelectFilter::make('zone')
                    ->label('Zona Geográfica')
                    ->options([
                        'peninsula' => 'Península',
                        'canary_islands' => 'Islas Canarias',
                        'balearic_islands' => 'Islas Baleares',
                    ]),
                
                Tables\Filters\SelectFilter::make('contract_type')
                    ->label('Tipo de Contrato')
                    ->options([
                        'fixed' => 'Tarifa Fija',
                        'variable' => 'Tarifa Variable',
                    ]),
                
                Tables\Filters\Filter::make('high_consumption')
                    ->label('Alto Consumo')
                    ->query(fn (Builder $query): Builder => $query->where('monthly_consumption', '>', 400)),
                
                Tables\Filters\Filter::make('recent_simulations')
                    ->label('Simulaciones Recientes')
                    ->query(fn (Builder $query): Builder => $query->where('simulation_date', '>=', now()->subDays(7))),
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
            ->defaultSort('simulation_date', 'desc')
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
            'index' => Pages\ListBillSimulators::route('/'),
            'create' => Pages\CreateBillSimulator::route('/create'),
            'view' => Pages\ViewBillSimulator::route('/{record}'),
            'edit' => Pages\EditBillSimulator::route('/{record}/edit'),
        ];
    }
}
