<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferComparisonResource\Pages;
use App\Filament\Resources\OfferComparisonResource\RelationManagers;
use App\Models\OfferComparison;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferComparisonResource extends Resource
{
    protected static ?string $model = OfferComparison::class;

    protected static ?string $navigationIcon = 'fas-balance-scale';

    protected static ?string $navigationGroup = 'Energía y Sostenibilidad';

    protected static ?string $navigationLabel = 'Comparaciones de Ofertas';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Comparación de Ofertas';

    protected static ?string $pluralModelLabel = 'Comparaciones de Ofertas';

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
                            ->relationship('user', 'name')
                            ->required()
                            ->label('Usuario')
                            ->searchable()
                            ->preload(),
                        
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
                            ->label('Tipo de Energía')
                            ->default('electricity'),
                        
                        Forms\Components\Select::make('consumption_profile')
                            ->options([
                                'residential' => '🏠 Residencial',
                                'commercial' => '🏪 Comercial',
                                'industrial' => '🏭 Industrial',
                                'low_consumption' => '📉 Bajo Consumo',
                                'medium_consumption' => '📊 Medio Consumo',
                                'high_consumption' => '📈 Alto Consumo',
                                'mixed' => '🔄 Mixto',
                            ])
                            ->required()
                            ->label('Perfil de Consumo'),
                        
                        Forms\Components\TextInput::make('best_offer_id')
                            ->required()
                            ->maxLength(255)
                            ->label('ID de la Mejor Oferta')
                            ->placeholder('ID de la oferta seleccionada como mejor'),
                    ])->columns(2),

                Forms\Components\Section::make('Ofertas y Comparación')
                    ->schema([
                        Forms\Components\KeyValue::make('offers_compared')
                            ->label('Ofertas Comparadas')
                            ->keyLabel('ID de Oferta')
                            ->valueLabel('Detalles')
                            ->addActionLabel('Agregar Oferta')
                            ->required(),
                        
                        Forms\Components\KeyValue::make('comparison_criteria')
                            ->label('Criterios de Comparación')
                            ->keyLabel('Criterio')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Criterio'),
                    ])->columns(1),

                Forms\Components\Section::make('Ahorros y Resultados')
                    ->schema([
                        Forms\Components\TextInput::make('savings_amount')
                            ->numeric()
                            ->label('Cantidad de Ahorro (EUR)')
                            ->placeholder('0.00')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('savings_percentage')
                            ->numeric()
                            ->label('Porcentaje de Ahorro (%)')
                            ->placeholder('0.00')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\DateTimePicker::make('comparison_date')
                            ->required()
                            ->label('Fecha de Comparación')
                            ->displayFormat('d/m/Y H:i')
                            ->default(now()),
                        
                        Forms\Components\Toggle::make('is_shared')
                            ->label('Compartida')
                            ->default(false)
                            ->helperText('La comparación es visible para otros usuarios'),
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
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\BadgeColumn::make('energy_type')
                    ->label('Tipo')
                    ->colors([
                        'warning' => 'electricity',
                        'info' => 'gas',
                        'success' => 'renewable',
                        'danger' => 'nuclear',
                        'secondary' => 'coal',
                        'dark' => 'oil',
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
                
                Tables\Columns\BadgeColumn::make('consumption_profile')
                    ->label('Perfil')
                    ->colors([
                        'success' => 'residential',
                        'warning' => 'commercial',
                        'danger' => 'industrial',
                        'info' => 'low_consumption',
                        'primary' => 'high_consumption',
                        'secondary' => 'mixed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'residential' => '🏠 Residencial',
                        'commercial' => '🏪 Comercial',
                        'industrial' => '🏭 Industrial',
                        'low_consumption' => '📉 Bajo',
                        'medium_consumption' => '📊 Medio',
                        'high_consumption' => '📈 Alto',
                        'mixed' => '🔄 Mixto',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('offers_count')
                    ->label('Ofertas')
                    ->getStateUsing(fn ($record): int => $record->offers_count)
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 5 => 'success',
                        $state >= 3 => 'info',
                        $state >= 2 => 'warning',
                        default => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('savings_percentage')
                    ->label('Ahorro %')
                    ->formatStateUsing(fn ($state): string => $state ? number_format($state, 1) . '%' : 'Sin ahorro')
                    ->sortable()
                    ->color(fn ($state): string => match (true) {
                        $state >= 20 => 'success',
                        $state >= 15 => 'info',
                        $state >= 10 => 'warning',
                        $state >= 5 => 'secondary',
                        $state > 0 => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('savings_amount')
                    ->label('Ahorro €')
                    ->formatStateUsing(fn ($state): string => $state ? number_format($state, 2) . ' €' : 'Sin ahorro')
                    ->sortable()
                    ->color(fn ($state): string => match (true) {
                        $state >= 1000 => 'success',
                        $state >= 500 => 'info',
                        $state >= 100 => 'warning',
                        $state > 0 => 'secondary',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('comparison_date')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_shared')
                    ->label('Compartida')
                    ->boolean()
                    ->trueColor('success')
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
                        'renewable' => '🌱 Renovable',
                        'nuclear' => '☢️ Nuclear',
                    ])
                    ->label('Tipo de Energía'),
                
                Tables\Filters\SelectFilter::make('consumption_profile')
                    ->options([
                        'residential' => '🏠 Residencial',
                        'commercial' => '🏪 Comercial',
                        'industrial' => '🏭 Industrial',
                        'low_consumption' => '📉 Bajo Consumo',
                        'medium_consumption' => '📊 Medio Consumo',
                        'high_consumption' => '📈 Alto Consumo',
                        'mixed' => '🔄 Mixto',
                    ])
                    ->label('Perfil de Consumo'),
                
                Tables\Filters\Filter::make('shared_only')
                    ->label('Solo Compartidas')
                    ->query(fn (Builder $query): Builder => $query->where('is_shared', true)),
                
                Tables\Filters\Filter::make('high_savings')
                    ->label('Ahorro Alto (15%+)')
                    ->query(fn (Builder $query): Builder => $query->where('savings_percentage', '>=', 15)),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Recientes (7 días)')
                    ->query(fn (Builder $query): Builder => $query->where('comparison_date', '>=', now()->subDays(7))),
                
                Tables\Filters\Filter::make('multiple_offers')
                    ->label('Múltiples Ofertas (3+)')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('JSON_LENGTH(offers_compared) >= 3')),
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
                
                Tables\Actions\Action::make('toggle_shared')
                    ->label(fn ($record): string => $record->is_shared ? 'Hacer Privada' : 'Compartir')
                    ->icon(fn ($record): string => $record->is_shared ? 'fas-lock' : 'fas-share')
                    ->action(function ($record): void {
                        $record->update(['is_shared' => !$record->is_shared]);
                    })
                    ->color(fn ($record): string => $record->is_shared ? 'warning' : 'success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('share_all')
                        ->label('Compartir Todas')
                        ->icon('fas-share')
                        ->action(function ($records): void {
                            $records->each->update(['is_shared' => true]);
                        })
                        ->color('success'),
                    
                    Tables\Actions\BulkAction::make('make_private')
                        ->label('Hacer Privadas')
                        ->icon('fas-lock')
                        ->action(function ($records): void {
                            $records->each->update(['is_shared' => false]);
                        })
                        ->color('warning'),
                ]),
            ])
            ->defaultSort('comparison_date', 'desc')
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
            'index' => Pages\ListOfferComparisons::route('/'),
            'create' => Pages\CreateOfferComparison::route('/create'),
            'view' => Pages\ViewOfferComparison::route('/{record}'),
            'edit' => Pages\EditOfferComparison::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}