<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceAlertResource\Pages;
use App\Models\PriceAlert;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PriceAlertResource extends Resource
{
    protected static ?string $model = PriceAlert::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell'; // Changed from fas-bell
    protected static ?string $navigationGroup = 'Energía y Sostenibilidad';
    protected static ?string $navigationLabel = 'Alertas de Precios';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Alerta de Precio';
    protected static ?string $pluralModelLabel = 'Alertas de Precios';

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
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('energy_type')
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
                                'all' => '🌍 Todos',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('electricity')
                            ->label('Tipo de Energía'),

                        Forms\Components\Select::make('zone')
                            ->options([
                                'peninsula' => 'Península',
                                'canarias' => 'Canarias',
                                'baleares' => 'Baleares',
                                'ceuta' => 'Ceuta',
                                'melilla' => 'Melilla',
                                'national' => 'Nacional',
                                'international' => 'Internacional',
                                'other' => 'Otro',
                            ])
                            ->required()
                            ->default('peninsula')
                            ->label('Zona'),

                        Forms\Components\Select::make('alert_type')
                            ->options([
                                'price_drop' => '📉 Bajada de Precio',
                                'price_rise' => '📈 Subida de Precio',
                                'price_threshold' => '🎯 Umbral de Precio',
                                'volatility' => '📊 Volatilidad',
                                'spike' => '🚀 Pico de Precio',
                                'low_price' => '💚 Precio Bajo',
                                'high_price' => '🔴 Precio Alto',
                                'average_price' => '📊 Precio Promedio',
                                'forecast_change' => '🔮 Cambio de Pronóstico',
                                'market_alert' => '🏪 Alerta de Mercado',
                                'other' => '❓ Otro',
                            ])
                            ->required()
                            ->default('price_drop')
                            ->label('Tipo de Alerta'),

                        Forms\Components\TextInput::make('threshold_price')
                            ->numeric()
                            ->required()
                            ->label('Precio Umbral')
                            ->suffix('€/MWh')
                            ->placeholder('Ej: 50.00'),

                        Forms\Components\Select::make('condition')
                            ->options([
                                'below' => 'Por debajo de',
                                'above' => 'Por encima de',
                                'equals' => 'Igual a',
                                'not_equals' => 'Diferente de',
                                'between' => 'Entre',
                                'outside' => 'Fuera de',
                            ])
                            ->required()
                            ->default('below')
                            ->label('Condición'),

                        Forms\Components\Select::make('frequency')
                            ->options([
                                'once' => 'Una vez',
                                'daily' => 'Diario',
                                'weekly' => 'Semanal',
                                'monthly' => 'Mensual',
                                'realtime' => 'Tiempo Real',
                                'hourly' => 'Cada hora',
                                'custom' => 'Personalizado',
                            ])
                            ->required()
                            ->default('once')
                            ->label('Frecuencia'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true)
                            ->helperText('Indica si la alerta está activa'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración de Notificaciones')
                    ->schema([
                        Forms\Components\KeyValue::make('notification_settings')
                            ->label('Configuración de Notificaciones')
                            ->keyLabel('Configuración')
                            ->valueLabel('Valor')
                            ->helperText('Configuración avanzada de notificaciones en formato JSON.')
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('Historial de Activaciones')
                    ->schema([
                        Forms\Components\DateTimePicker::make('last_triggered')
                            ->label('Última Activación')
                            ->displayFormat('d/m/Y H:i')
                            ->nullable()
                            ->helperText('Cuándo se activó por última vez'),

                        Forms\Components\TextInput::make('trigger_count')
                            ->numeric()
                            ->label('Contador de Activaciones')
                            ->default(0)
                            ->disabled()
                            ->helperText('Número de veces que se ha activado'),
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

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('energy_type')
                    ->label('Energía')
                    ->colors(fn (string $state): array => match ($state) {
                        'electricity' => ['warning'],
                        'gas' => ['info'],
                        'oil' => ['dark'],
                        'coal' => ['secondary'],
                        'solar' => ['success'],
                        'wind' => ['info'],
                        'hydro' => ['primary'],
                        'nuclear' => ['danger'],
                        'biomass' => ['success'],
                        'geothermal' => ['warning'],
                        'hybrid' => ['primary'],
                        'all' => ['light'],
                        default => ['gray'],
                    })
                    ->formatStateUsing(fn (string $state): string => PriceAlert::make(['energy_type' => $state])->energy_type_label),

                Tables\Columns\BadgeColumn::make('alert_type')
                    ->label('Tipo')
                    ->colors(fn (string $state): array => match ($state) {
                        'price_drop' => ['success'],
                        'price_rise' => ['danger'],
                        'price_threshold' => ['warning'],
                        'volatility' => ['info'],
                        'spike' => ['danger'],
                        'low_price' => ['success'],
                        'high_price' => ['danger'],
                        'average_price' => ['info'],
                        'forecast_change' => ['warning'],
                        'market_alert' => ['primary'],
                        default => ['gray'],
                    })
                    ->formatStateUsing(fn (string $state): string => PriceAlert::make(['alert_type' => $state])->alert_type_label),

                Tables\Columns\BadgeColumn::make('zone')
                    ->label('Zona')
                    ->colors(fn (string $state): array => match ($state) {
                        'peninsula' => ['primary'],
                        'canarias' => ['warning'],
                        'baleares' => ['info'],
                        'ceuta' => ['secondary'],
                        'melilla' => ['secondary'],
                        'national' => ['success'],
                        'international' => ['danger'],
                        default => ['gray'],
                    })
                    ->formatStateUsing(fn (string $state): string => PriceAlert::make(['zone' => $state])->zone_label),

                Tables\Columns\TextColumn::make('threshold_price')
                    ->label('Umbral')
                    ->money('EUR')
                    ->suffix('/MWh')
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state <= 30 => 'success',
                        $state <= 60 => 'info',
                        $state <= 100 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('condition')
                    ->label('Condición')
                    ->formatStateUsing(fn (string $state): string => PriceAlert::make(['condition' => $state])->condition_label)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'below' => 'success',
                        'above' => 'danger',
                        'equals' => 'info',
                        'not_equals' => 'warning',
                        'between' => 'primary',
                        'outside' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('trigger_count')
                    ->label('Activaciones')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'success',
                        $state <= 5 => 'info',
                        $state <= 20 => 'warning',
                        $state <= 50 => 'danger',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('last_triggered')
                    ->label('Última Activación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->last_triggered && $record->last_triggered->diffInHours(now()) <= 24 ? 'danger' : 
                        ($record->last_triggered && $record->last_triggered->diffInHours(now()) <= 168 ? 'warning' : 'success')
                    ),

                Tables\Columns\BadgeColumn::make('frequency')
                    ->label('Frecuencia')
                    ->colors(fn (string $state): array => match ($state) {
                        'once' => ['secondary'],
                        'daily' => ['info'],
                        'weekly' => ['warning'],
                        'monthly' => ['primary'],
                        'realtime' => ['danger'],
                        'hourly' => ['success'],
                        'custom' => ['gray'],
                        default => ['gray'],
                    })
                    ->formatStateUsing(fn (string $state): string => PriceAlert::make(['frequency' => $state])->frequency_label),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('notification_channels_count')
                    ->label('Canales')
                    ->getStateUsing(fn (PriceAlert $record): int => $record->notification_channels_count)
                    ->numeric()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'secondary',
                        $state === 1 => 'info',
                        $state === 2 => 'warning',
                        $state >= 3 => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('has_email_notification')
                    ->label('Email')
                    ->getStateUsing(fn (PriceAlert $record): bool => $record->has_email_notification)
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),

                Tables\Columns\IconColumn::make('has_sms_notification')
                    ->label('SMS')
                    ->getStateUsing(fn (PriceAlert $record): bool => $record->has_sms_notification)
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('secondary'),

                Tables\Columns\IconColumn::make('has_push_notification')
                    ->label('Push')
                    ->getStateUsing(fn (PriceAlert $record): bool => $record->has_push_notification)
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('secondary'),

                Tables\Columns\IconColumn::make('has_webhook')
                    ->label('Webhook')
                    ->getStateUsing(fn (PriceAlert $record): bool => $record->has_webhook)
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('secondary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('energy_type')
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
                        'all' => '🌍 Todos',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Energía'),

                Tables\Filters\SelectFilter::make('alert_type')
                    ->options([
                        'price_drop' => '📉 Bajada de Precio',
                        'price_rise' => '📈 Subida de Precio',
                        'price_threshold' => '🎯 Umbral de Precio',
                        'volatility' => '📊 Volatilidad',
                        'spike' => '🚀 Pico de Precio',
                        'low_price' => '💚 Precio Bajo',
                        'high_price' => '🔴 Precio Alto',
                        'average_price' => '📊 Precio Promedio',
                        'forecast_change' => '🔮 Cambio de Pronóstico',
                        'market_alert' => '🏪 Alerta de Mercado',
                        'other' => '❓ Otro',
                    ])
                    ->label('Tipo de Alerta'),

                Tables\Filters\SelectFilter::make('zone')
                    ->options([
                        'peninsula' => 'Península',
                        'canarias' => 'Canarias',
                        'baleares' => 'Baleares',
                        'ceuta' => 'Ceuta',
                        'melilla' => 'Melilla',
                        'national' => 'Nacional',
                        'international' => 'Internacional',
                        'other' => 'Otro',
                    ])
                    ->label('Zona'),

                Tables\Filters\SelectFilter::make('condition')
                    ->options([
                        'below' => 'Por debajo de',
                        'above' => 'Por encima de',
                        'equals' => 'Igual a',
                        'not_equals' => 'Diferente de',
                        'between' => 'Entre',
                        'outside' => 'Fuera de',
                    ])
                    ->label('Condición'),

                Tables\Filters\SelectFilter::make('frequency')
                    ->options([
                        'once' => 'Una vez',
                        'daily' => 'Diario',
                        'weekly' => 'Semanal',
                        'monthly' => 'Mensual',
                        'realtime' => 'Tiempo Real',
                        'hourly' => 'Cada hora',
                        'custom' => 'Personalizado',
                    ])
                    ->label('Frecuencia'),

                Tables\Filters\Filter::make('is_active')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),

                Tables\Filters\Filter::make('triggered')
                    ->label('Solo Activadas')
                    ->query(fn (Builder $query): Builder => $query->where('trigger_count', '>', 0)),

                Tables\Filters\Filter::make('never_triggered')
                    ->label('Nunca Activadas')
                    ->query(fn (Builder $query): Builder => $query->where('trigger_count', 0)),

                Tables\Filters\Filter::make('recently_triggered')
                    ->label('Activadas Recientemente (7 días)')
                    ->query(fn (Builder $query): Builder => $query->where('last_triggered', '>=', now()->subDays(7))),

                Tables\Filters\Filter::make('frequently_triggered')
                    ->label('Frecuentemente Activadas (10+)')
                    ->query(fn (Builder $query): Builder => $query->where('trigger_count', '>=', 10)),

                Tables\Filters\Filter::make('low_threshold')
                    ->label('Umbral Bajo (<30€)')
                    ->query(fn (Builder $query): Builder => $query->where('threshold_price', '<', 30)),

                Tables\Filters\Filter::make('high_threshold')
                    ->label('Umbral Alto (>100€)')
                    ->query(fn (Builder $query): Builder => $query->where('threshold_price', '>', 100)),

                Tables\Filters\Filter::make('realtime_frequency')
                    ->label('Tiempo Real')
                    ->query(fn (Builder $query): Builder => $query->where('frequency', 'realtime')),
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

                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (PriceAlert $record): string => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn (PriceAlert $record): string => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->action(function (PriceAlert $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                    })
                    ->color(fn (PriceAlert $record): string => $record->is_active ? 'warning' : 'success'),

                Tables\Actions\Action::make('trigger_alert')
                    ->label('Simular Activación')
                    ->icon('heroicon-o-bolt')
                    ->action(function (PriceAlert $record): void {
                        $record->trigger();
                    })
                    ->color('primary')
                    ->visible(fn (PriceAlert $record): bool => $record->is_active),

                Tables\Actions\Action::make('reset_trigger_count')
                    ->label('Resetear Contador')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (PriceAlert $record): void {
                        $record->resetTriggerCount();
                    })
                    ->color('secondary')
                    ->visible(fn (PriceAlert $record): bool => $record->trigger_count > 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('activate_all')
                        ->label('Activar Todas')
                        ->icon('heroicon-o-play')
                        ->action(function ($records): void {
                            $records->each->activate();
                        })
                        ->color('success'),

                    Tables\Actions\BulkAction::make('deactivate_all')
                        ->label('Desactivar Todas')
                        ->icon('heroicon-o-pause')
                        ->action(function ($records): void {
                            $records->each->deactivate();
                        })
                        ->color('warning'),

                    Tables\Actions\BulkAction::make('reset_trigger_counts')
                        ->label('Resetear Contadores')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function ($records): void {
                            $records->each->resetTriggerCount();
                        })
                        ->color('secondary'),
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
            'index' => Pages\ListPriceAlerts::route('/'),
            'create' => Pages\CreatePriceAlert::route('/create'),
            'view' => Pages\ViewPriceAlert::route('/{record}'),
            'edit' => Pages\EditPriceAlert::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}