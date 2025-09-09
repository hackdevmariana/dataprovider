<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnergyServiceResource\Pages;
use App\Filament\Resources\EnergyServiceResource\RelationManagers;
use App\Models\EnergyService;
use App\Models\EnergyCompany;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnergyServiceResource extends Resource
{
    protected static ?string $model = EnergyService::class;

    protected static ?string $navigationIcon = 'fas-bolt';

    protected static ?string $navigationGroup = 'Energía y Precios';

    protected static ?string $navigationLabel = 'Servicios Energéticos';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Servicio Energético';

    protected static ?string $pluralModelLabel = 'Servicios Energéticos';

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
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->label('Empresa')
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\TextInput::make('service_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre del Servicio')
                            ->placeholder('Nombre del servicio energético...'),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada del servicio...'),
                        
                        Forms\Components\Select::make('service_type')
                            ->options([
                                'supply' => '⚡ Suministro',
                                'distribution' => '📡 Distribución',
                                'generation' => '🔋 Generación',
                                'storage' => '💾 Almacenamiento',
                                'consulting' => '💼 Consultoría',
                                'maintenance' => '🔧 Mantenimiento',
                                'installation' => '🛠️ Instalación',
                                'monitoring' => '📊 Monitoreo',
                                'billing' => '📋 Facturación',
                                'support' => '🎧 Soporte',
                                'energy_audit' => '🔍 Auditoría Energética',
                                'efficiency' => '📈 Eficiencia Energética',
                                'renewable' => '🌱 Energías Renovables',
                                'smart_home' => '🏠 Hogar Inteligente',
                                'electric_vehicle' => '🚗 Vehículo Eléctrico',
                            ])
                            ->required()
                            ->label('Tipo de Servicio'),
                        
                        Forms\Components\Select::make('energy_source')
                            ->options([
                                'electricity' => '⚡ Electricidad',
                                'gas' => '🔥 Gas Natural',
                                'oil' => '🛢️ Petróleo',
                                'coal' => '⛏️ Carbón',
                                'solar' => '☀️ Solar',
                                'wind' => '💨 Eólico',
                                'hydro' => '💧 Hidroeléctrico',
                                'nuclear' => '☢️ Nuclear',
                                'biomass' => '🌱 Biomasa',
                                'geothermal' => '🌋 Geotérmico',
                                'hybrid' => '🔄 Híbrido',
                                'all' => '🌐 Todos',
                            ])
                            ->label('Fuente de Energía'),
                    ])->columns(2),

                Forms\Components\Section::make('Características y Requisitos')
                    ->schema([
                        Forms\Components\KeyValue::make('features')
                            ->label('Características')
                            ->keyLabel('Característica')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Característica'),
                        
                        Forms\Components\KeyValue::make('requirements')
                            ->label('Requisitos')
                            ->keyLabel('Requisito')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Requisito'),
                    ])->columns(1),

                Forms\Components\Section::make('Precios y Contratos')
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->numeric()
                            ->label('Precio Base (EUR)')
                            ->placeholder('0.00')
                            ->step(0.01),
                        
                        Forms\Components\Select::make('pricing_model')
                            ->options([
                                'fixed' => 'Precio Fijo',
                                'variable' => 'Precio Variable',
                                'tiered' => 'Por Escalones',
                                'subscription' => 'Suscripción',
                                'pay_per_use' => 'Pago por Uso',
                                'contract' => 'Contrato',
                                'free' => 'Gratuito',
                                'custom' => 'Personalizado',
                            ])
                            ->label('Modelo de Precios'),
                        
                        Forms\Components\KeyValue::make('pricing_details')
                            ->label('Detalles de Precios')
                            ->keyLabel('Concepto')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Detalle'),
                        
                        Forms\Components\TextInput::make('contract_duration')
                            ->maxLength(255)
                            ->label('Duración del Contrato')
                            ->placeholder('1 mes, 1 año, indefinido...'),
                        
                        Forms\Components\KeyValue::make('terms_conditions')
                            ->label('Términos y Condiciones')
                            ->keyLabel('Término')
                            ->valueLabel('Condición')
                            ->addActionLabel('Agregar Término'),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Popularidad')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Disponible')
                            ->default(true)
                            ->helperText('El servicio está disponible para contratar'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false)
                            ->helperText('Servicio importante para destacar'),
                        
                        Forms\Components\TextInput::make('popularity_score')
                            ->numeric()
                            ->label('Puntuación de Popularidad')
                            ->placeholder('0')
                            ->minValue(0)
                            ->maxValue(1000)
                            ->helperText('Puntuación de 0 a 1000 basada en demanda'),
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
                
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('service_name')
                    ->label('Servicio')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('service_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'supply',
                        'info' => 'distribution',
                        'success' => 'generation',
                        'warning' => 'storage',
                        'secondary' => 'consulting',
                        'dark' => 'maintenance',
                        'light' => 'installation',
                        'danger' => 'monitoring',
                        'gray' => 'billing',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'supply' => '⚡ Suministro',
                        'distribution' => '📡 Distribución',
                        'generation' => '🔋 Generación',
                        'storage' => '💾 Almacenamiento',
                        'consulting' => '💼 Consultoría',
                        'maintenance' => '🔧 Mantenimiento',
                        'installation' => '🛠️ Instalación',
                        'monitoring' => '📊 Monitoreo',
                        'billing' => '📋 Facturación',
                        'support' => '🎧 Soporte',
                        'energy_audit' => '🔍 Auditoría',
                        'efficiency' => '📈 Eficiencia',
                        'renewable' => '🌱 Renovable',
                        'smart_home' => '🏠 Hogar Inteligente',
                        'electric_vehicle' => '🚗 Vehículo Eléctrico',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('energy_source')
                    ->label('Fuente')
                    ->colors([
                        'warning' => 'electricity',
                        'info' => 'gas',
                        'success' => 'solar',
                        'primary' => 'wind',
                        'danger' => 'nuclear',
                        'secondary' => 'biomass',
                        'dark' => 'oil',
                        'gray' => 'coal',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'electricity' => '⚡ Electricidad',
                        'gas' => '🔥 Gas',
                        'oil' => '🛢️ Petróleo',
                        'coal' => '⛏️ Carbón',
                        'solar' => '☀️ Solar',
                        'wind' => '💨 Eólico',
                        'hydro' => '💧 Hidro',
                        'nuclear' => '☢️ Nuclear',
                        'biomass' => '🌱 Biomasa',
                        'geothermal' => '🌋 Geotérmico',
                        'hybrid' => '🔄 Híbrido',
                        'all' => '🌐 Todos',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Precio Base')
                    ->formatStateUsing(fn ($state): string => $state ? number_format($state, 2) . ' €' : 'Consultar')
                    ->sortable()
                    ->color(fn ($state): string => match (true) {
                        $state <= 50 => 'success',
                        $state <= 100 => 'info',
                        $state <= 200 => 'warning',
                        $state <= 500 => 'secondary',
                        default => 'danger',
                    }),
                
                Tables\Columns\BadgeColumn::make('pricing_model')
                    ->label('Modelo')
                    ->colors([
                        'success' => 'fixed',
                        'warning' => 'variable',
                        'info' => 'tiered',
                        'primary' => 'subscription',
                        'secondary' => 'pay_per_use',
                        'dark' => 'contract',
                        'light' => 'free',
                        'gray' => 'custom',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fixed' => 'Precio Fijo',
                        'variable' => 'Precio Variable',
                        'tiered' => 'Por Escalones',
                        'subscription' => 'Suscripción',
                        'pay_per_use' => 'Pago por Uso',
                        'contract' => 'Contrato',
                        'free' => 'Gratuito',
                        'custom' => 'Personalizado',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('features_count')
                    ->label('Características')
                    ->getStateUsing(fn ($record): int => $record->features_count)
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 5 => 'success',
                        $state >= 3 => 'info',
                        $state >= 1 => 'warning',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('popularity_score')
                    ->label('Popularidad')
                    ->sortable()
                    ->color(fn ($state): string => match (true) {
                        $state >= 700 => 'success',
                        $state >= 400 => 'info',
                        $state >= 200 => 'warning',
                        $state >= 100 => 'secondary',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Disponible')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_type')
                    ->options([
                        'supply' => '⚡ Suministro',
                        'distribution' => '📡 Distribución',
                        'generation' => '🔋 Generación',
                        'storage' => '💾 Almacenamiento',
                        'consulting' => '💼 Consultoría',
                        'maintenance' => '🔧 Mantenimiento',
                        'installation' => '🛠️ Instalación',
                        'monitoring' => '📊 Monitoreo',
                        'billing' => '📋 Facturación',
                        'support' => '🎧 Soporte',
                        'energy_audit' => '🔍 Auditoría Energética',
                        'efficiency' => '📈 Eficiencia Energética',
                        'renewable' => '🌱 Energías Renovables',
                        'smart_home' => '🏠 Hogar Inteligente',
                        'electric_vehicle' => '🚗 Vehículo Eléctrico',
                    ])
                    ->label('Tipo de Servicio'),
                
                Tables\Filters\SelectFilter::make('energy_source')
                    ->options([
                        'electricity' => '⚡ Electricidad',
                        'gas' => '🔥 Gas Natural',
                        'oil' => '🛢️ Petróleo',
                        'coal' => '⛏️ Carbón',
                        'solar' => '☀️ Solar',
                        'wind' => '💨 Eólico',
                        'hydro' => '💧 Hidroeléctrico',
                        'nuclear' => '☢️ Nuclear',
                        'biomass' => '🌱 Biomasa',
                        'geothermal' => '🌋 Geotérmico',
                        'hybrid' => '🔄 Híbrido',
                        'all' => '🌐 Todos',
                    ])
                    ->label('Fuente de Energía'),
                
                Tables\Filters\SelectFilter::make('pricing_model')
                    ->options([
                        'fixed' => 'Precio Fijo',
                        'variable' => 'Precio Variable',
                        'tiered' => 'Por Escalones',
                        'subscription' => 'Suscripción',
                        'pay_per_use' => 'Pago por Uso',
                        'contract' => 'Contrato',
                        'free' => 'Gratuito',
                        'custom' => 'Personalizado',
                    ])
                    ->label('Modelo de Precios'),
                
                Tables\Filters\Filter::make('available_only')
                    ->label('Solo Disponibles')
                    ->query(fn (Builder $query): Builder => $query->where('is_available', true)),
                
                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('popular')
                    ->label('Populares (200+)')
                    ->query(fn (Builder $query): Builder => $query->where('popularity_score', '>=', 200)),
                
                Tables\Filters\Filter::make('high_demand')
                    ->label('Alta Demanda (700+)')
                    ->query(fn (Builder $query): Builder => $query->where('popularity_score', '>=', 700)),
                
                Tables\Filters\Filter::make('renewable_energy')
                    ->label('Solo Renovables')
                    ->query(fn (Builder $query): Builder => $query->whereIn('energy_source', ['solar', 'wind', 'hydro', 'biomass', 'geothermal'])),
                
                Tables\Filters\Filter::make('low_price')
                    ->label('Precio Bajo (≤50€)')
                    ->query(fn (Builder $query): Builder => $query->where('base_price', '<=', 50)),
                
                Tables\Filters\Filter::make('free_services')
                    ->label('Servicios Gratuitos')
                    ->query(fn (Builder $query): Builder => $query->where('pricing_model', 'free')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
                
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn ($record): string => $record->is_featured ? 'Quitar Destacado' : 'Destacar')
                    ->icon(fn ($record): string => $record->is_featured ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->action(function ($record): void {
                        $record->update(['is_featured' => !$record->is_featured]);
                    })
                    ->color(fn ($record): string => $record->is_featured ? 'warning' : 'success'),
                
                Tables\Actions\Action::make('toggle_available')
                    ->label(fn ($record): string => $record->is_available ? 'Desactivar' : 'Activar')
                    ->icon(fn ($record): string => $record->is_available ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->action(function ($record): void {
                        $record->update(['is_available' => !$record->is_available]);
                    })
                    ->color(fn ($record): string => $record->is_available ? 'warning' : 'success'),
                
                Tables\Actions\Action::make('increase_popularity')
                    ->label('Aumentar Popularidad')
                    ->icon('heroicon-o-arrow-up')
                    ->action(function ($record): void {
                        $record->update(['popularity_score' => min(1000, $record->popularity_score + 50)]);
                    })
                    ->visible(fn ($record): bool => $record->popularity_score < 1000)
                    ->color('info'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Marcar como Destacados')
                        ->icon('heroicon-s-star')
                        ->action(function ($records): void {
                            $records->each->update(['is_featured' => true]);
                        })
                        ->color('warning'),
                    
                    Tables\Actions\BulkAction::make('mark_available')
                        ->label('Marcar como Disponibles')
                        ->icon('heroicon-o-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_available' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('increase_popularity')
                        ->label('Aumentar Popularidad')
                        ->icon('heroicon-o-arrow-up')
                        ->action(function ($records): void {
                            $records->each(function ($record) {
                                $record->update(['popularity_score' => min(1000, $record->popularity_score + 50)]);
                            });
                        })
                        ->color('info'),
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
            'index' => Pages\ListEnergyServices::route('/'),
            'create' => Pages\CreateEnergyService::route('/create'),
            'view' => Pages\ViewEnergyService::route('/{record}'),
            'edit' => Pages\EditEnergyService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}