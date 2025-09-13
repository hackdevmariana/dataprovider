<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ElectricityOfferResource\Pages;
use App\Filament\Resources\ElectricityOfferResource\RelationManagers;
use App\Models\ElectricityOffer;
use App\Models\EnergyCompany;
use App\Models\PriceUnit;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ElectricityOfferResource extends Resource
{
    protected static ?string $model = ElectricityOffer::class;
    protected static ?string $navigationGroup = 'Energy & Environment';
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $modelLabel = 'Oferta Eléctrica';
    protected static ?string $pluralModelLabel = 'Ofertas Eléctricas';
    protected static ?int $navigationSort = 20;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información de la Oferta')
                ->description('Datos básicos de la oferta eléctrica')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('energy_company_id')
                                ->label('Compañía Energética')
                                ->relationship('energyCompany', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->placeholder('Seleccionar compañía'),

                            TextInput::make('name')
                                ->label('Nombre de la Oferta')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Oferta Verde Premium'),

                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('oferta-verde-premium')
                                ->helperText('Identificador único para la URL'),

                            Select::make('offer_type')
                                ->label('Tipo de Oferta')
                                ->options([
                                    'fixed' => 'Tarifa Fija',
                                    'variable' => 'Tarifa Variable',
                                    'mixed' => 'Tarifa Mixta',
                                    'time_of_use' => 'Tarifa por Horario',
                                    'green' => 'Tarifa Verde',
                                    'premium' => 'Tarifa Premium',
                                ])
                                ->required()
                                ->placeholder('Seleccionar tipo'),
                        ]),
                ])
                ->collapsible(),

            Section::make('Precios y Condiciones')
                ->description('Información de precios y condiciones contractuales')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('price_fixed_eur_month')
                                ->label('Precio Fijo Mensual (€)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->placeholder('15.50')
                                ->helperText('Cargo fijo mensual en euros'),

                            TextInput::make('price_variable_eur_kwh')
                                ->label('Precio Variable por kWh (€)')
                                ->numeric()
                                ->step(0.0001)
                                ->minValue(0)
                                ->placeholder('0.1250')
                                ->helperText('Precio por kilovatio-hora consumido'),

                            Select::make('price_unit_id')
                                ->label('Unidad de Precio')
                                ->relationship('priceUnit', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Seleccionar unidad'),

                            TextInput::make('contract_length_months')
                                ->label('Duración del Contrato (meses)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(120)
                                ->placeholder('12')
                                ->helperText('Duración mínima del contrato'),
                        ]),
                ])
                ->collapsible(),

            Section::make('Fechas de Validez')
                ->description('Período de validez de la oferta')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('valid_from')
                                ->label('Válida Desde')
                                ->required()
                                ->placeholder('2025-01-01')
                                ->helperText('Fecha de inicio de validez'),

                            DatePicker::make('valid_until')
                                ->label('Válida Hasta')
                                ->nullable()
                                ->placeholder('2025-12-31')
                                ->helperText('Fecha de fin de validez (opcional)'),
                        ]),
                ])
                ->collapsible(),

            Section::make('Características Técnicas')
                ->description('Requisitos y certificaciones de la oferta')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Toggle::make('requires_smart_meter')
                                ->label('Requiere Contador Inteligente')
                                ->helperText('La oferta requiere un contador inteligente'),

                            Toggle::make('renewable_origin_certified')
                                ->label('Origen Renovable Certificado')
                                ->helperText('La energía tiene certificado de origen renovable'),
                        ]),
                ])
                ->collapsible(),

            Section::make('Información Adicional')
                ->description('Enlaces y descripción detallada')
                ->schema([
                    Textarea::make('description')
                        ->label('Descripción')
                        ->nullable()
                        ->maxLength(1000)
                        ->placeholder('Descripción detallada de la oferta...')
                        ->rows(3),

                    TextInput::make('conditions_url')
                        ->label('URL de Condiciones')
                        ->url()
                        ->nullable()
                        ->placeholder('https://ejemplo.com/condiciones')
                        ->helperText('Enlace a las condiciones completas de la oferta'),
                ])
                ->collapsible(),
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

                TextColumn::make('energyCompany.name')
                    ->label('Compañía')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->energyCompany?->name ?? 'Sin compañía')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->name),

                TextColumn::make('offer_type')
                    ->label('Tipo')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => self::getOfferTypeLabel($record->offer_type))
                    ->badge()
                    ->color('info'),

                TextColumn::make('price_fixed_eur_month')
                    ->label('Precio Fijo')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->price_fixed_eur_month ? number_format($record->price_fixed_eur_month, 2) . ' €/mes' : 'No especificado')
                    ->badge()
                    ->color('success'),

                TextColumn::make('price_variable_eur_kwh')
                    ->label('Precio Variable')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->price_variable_eur_kwh ? number_format($record->price_variable_eur_kwh, 4) . ' €/kWh' : 'No especificado')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('contract_length_months')
                    ->label('Duración')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->contract_length_months ? $record->contract_length_months . ' meses' : 'No especificado')
                    ->badge()
                    ->color('secondary'),

                TextColumn::make('valid_from')
                    ->label('Válida Desde')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('valid_until')
                    ->label('Válida Hasta')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('requires_smart_meter')
                    ->label('Contador Inteligente')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('renewable_origin_certified')
                    ->label('Origen Renovable')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
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
                SelectFilter::make('energy_company')
                    ->label('Compañía')
                    ->relationship('energyCompany', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todas las compañías'),

                SelectFilter::make('offer_type')
                    ->label('Tipo de Oferta')
                    ->options([
                        'fixed' => 'Tarifa Fija',
                        'variable' => 'Tarifa Variable',
                        'mixed' => 'Tarifa Mixta',
                        'time_of_use' => 'Tarifa por Horario',
                        'green' => 'Tarifa Verde',
                        'premium' => 'Tarifa Premium',
                    ])
                    ->placeholder('Todos los tipos'),

                SelectFilter::make('price_unit')
                    ->label('Unidad de Precio')
                    ->relationship('priceUnit', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todas las unidades'),

                TernaryFilter::make('requires_smart_meter')
                    ->label('Requiere Contador Inteligente')
                    ->placeholder('Todas las ofertas')
                    ->trueLabel('Solo con contador inteligente')
                    ->falseLabel('Solo sin contador inteligente'),

                TernaryFilter::make('renewable_origin_certified')
                    ->label('Origen Renovable Certificado')
                    ->placeholder('Todas las ofertas')
                    ->trueLabel('Solo renovables certificadas')
                    ->falseLabel('Solo no renovables'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListElectricityOffers::route('/'),
            'create' => Pages\CreateElectricityOffer::route('/create'),
            'edit' => Pages\EditElectricityOffer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['energyCompany', 'priceUnit']);
    }

    public static function getOfferTypeLabel(?string $type): string
    {
        if (!$type) {
            return 'No especificado';
        }

        return match($type) {
            'fixed' => 'Tarifa Fija',
            'variable' => 'Tarifa Variable',
            'mixed' => 'Tarifa Mixta',
            'time_of_use' => 'Tarifa por Horario',
            'green' => 'Tarifa Verde',
            'premium' => 'Tarifa Premium',
            default => ucfirst($type),
        };
    }
}
