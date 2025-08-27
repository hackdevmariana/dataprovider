<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RealTimePriceResource\Pages;
use App\Filament\Resources\RealTimePriceResource\RelationManagers;
use App\Models\RealTimePrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RealTimePriceResource extends Resource
{
    protected static ?string $model = RealTimePrice::class;

    protected static ?string $navigationIcon = 'fas-bolt';

    protected static ?string $navigationGroup = 'Energía y Precios';

    protected static ?string $navigationLabel = 'Precios en Tiempo Real';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Precio en Tiempo Real';

    protected static ?string $pluralModelLabel = 'Precios en Tiempo Real';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Precio')
                    ->schema([
                        Forms\Components\Select::make('energy_type')
                            ->options([
                                'electricity' => '⚡ Electricidad',
                                'gas' => '🔥 Gas',
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
                            ->label('Zona'),
                        
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->step(0.0001)
                            ->prefix('€')
                            ->suffix('/MWh')
                            ->label('Precio')
                            ->helperText('Precio por megavatio-hora'),
                    ])->columns(3),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\DateTimePicker::make('timestamp')
                            ->required()
                            ->label('Fecha y Hora')
                            ->seconds(false)
                            ->default(now()),
                        
                        Forms\Components\Select::make('currency')
                            ->options([
                                'EUR' => '🇪🇺 Euro (EUR)',
                                'USD' => '🇺🇸 Dólar (USD)',
                                'GBP' => '🇬🇧 Libra (GBP)',
                            ])
                            ->default('EUR')
                            ->label('Moneda'),
                        
                        Forms\Components\Select::make('unit')
                            ->options([
                                'MWh' => 'MWh (Megavatio-hora)',
                                'kWh' => 'kWh (Kilovatio-hora)',
                                'GJ' => 'GJ (Gigajulio)',
                                'therm' => 'Therm',
                            ])
                            ->default('MWh')
                            ->label('Unidad'),
                    ])->columns(3),

                Forms\Components\Section::make('Origen y Calidad')
                    ->schema([
                        Forms\Components\TextInput::make('source')
                            ->maxLength(255)
                            ->label('Fuente de Datos')
                            ->placeholder('Nombre de la empresa o plataforma'),
                        
                        Forms\Components\Select::make('data_quality')
                            ->options([
                                'high' => '🟢 Alta Calidad',
                                'medium' => '🟡 Calidad Media',
                                'low' => '🔴 Baja Calidad',
                                'estimated' => '🔵 Estimado',
                            ])
                            ->default('high')
                            ->label('Calidad de los Datos'),
                    ])->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\KeyValue::make('additional_data')
                            ->label('Datos Adicionales')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Campo'),
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
                    ->label('Tipo')
                    ->colors([
                        'warning' => 'electricity',
                        'info' => 'gas',
                        'dark' => 'oil',
                        'secondary' => 'coal',
                        'success' => 'renewable',
                        'danger' => 'nuclear',
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
                        'success' => 'canarias',
                        'warning' => 'baleares',
                        'info' => 'ceuta',
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
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('EUR')
                    ->suffix('/MWh')
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state < 50 => 'success',
                        $state < 100 => 'info',
                        $state < 150 => 'warning',
                        $state < 200 => 'danger',
                        default => 'dark',
                    }),
                
                Tables\Columns\TextColumn::make('timestamp')
                    ->label('Fecha/Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('time_ago')
                    ->label('Hace')
                    ->formatStateUsing(fn ($record): string => $record->time_ago)
                    ->color('secondary'),
                
                Tables\Columns\BadgeColumn::make('data_quality')
                    ->label('Calidad')
                    ->colors([
                        'success' => 'high',
                        'warning' => 'medium',
                        'danger' => 'low',
                        'info' => 'estimated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'high' => '🟢 Alta',
                        'medium' => '🟡 Media',
                        'low' => '🔴 Baja',
                        'estimated' => '🔵 Estimada',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('source')
                    ->label('Fuente')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\IconColumn::make('is_recent')
                    ->label('Reciente')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\IconColumn::make('is_today')
                    ->label('Hoy')
                    ->boolean()
                    ->trueColor('info')
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
                
                Tables\Filters\SelectFilter::make('data_quality')
                    ->options([
                        'high' => '🟢 Alta Calidad',
                        'medium' => '🟡 Calidad Media',
                        'low' => '🔴 Baja Calidad',
                        'estimated' => '🔵 Estimado',
                    ])
                    ->label('Calidad de Datos'),
                
                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('timestamp', today())),
                
                Tables\Filters\Filter::make('this_hour')
                    ->label('Esta Hora')
                    ->query(fn (Builder $query): Builder => $query->where('timestamp', '>=', now()->startOfHour())),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Última Hora')
                    ->query(fn (Builder $query): Builder => $query->where('timestamp', '>=', now()->subHour())),
                
                Tables\Filters\Filter::make('high_price')
                    ->label('Precios Altos')
                    ->query(fn (Builder $query): Builder => $query->where('price', '>=', 150)),
                
                Tables\Filters\Filter::make('low_price')
                    ->label('Precios Bajos')
                    ->query(fn (Builder $query): Builder => $query->where('price', '<=', 50)),
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
                
                Tables\Actions\Action::make('refresh_price')
                    ->label('Actualizar Precio')
                    ->icon('fas-sync-alt')
                    ->action(function ($record): void {
                        // Aquí se podría implementar la lógica para actualizar el precio
                        $record->update(['timestamp' => now()]);
                    })
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('mark_high_quality')
                        ->label('Marcar como Alta Calidad')
                        ->icon('fas-check-circle')
                        ->action(function ($records): void {
                            $records->each->update(['data_quality' => 'high']);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('mark_estimated')
                        ->label('Marcar como Estimado')
                        ->icon('fas-question-circle')
                        ->action(function ($records): void {
                            $records->each->update(['data_quality' => 'estimated']);
                        })
                        ->color('info'),
                ]),
            ])
            ->defaultSort('timestamp', 'desc')
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
            'index' => Pages\ListRealTimePrices::route('/'),
            'create' => Pages\CreateRealTimePrice::route('/create'),
            'view' => Pages\ViewRealTimePrice::route('/{record}'),
            'edit' => Pages\EditRealTimePrice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
