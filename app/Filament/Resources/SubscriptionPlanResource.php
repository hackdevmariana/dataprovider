<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Projects & Monetization';

    protected static ?string $modelLabel = 'Plan de Suscripción';

    protected static ?string $pluralModelLabel = 'Planes de Suscripción';

    protected static ?int $navigationSort = 1;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'individual' => 'Individual',
                                'cooperative' => 'Cooperativa',
                                'business' => 'Empresa',
                                'enterprise' => 'Enterprise',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('billing_cycle')
                            ->label('Ciclo de Facturación')
                            ->options([
                                'monthly' => 'Mensual',
                                'yearly' => 'Anual',
                                'one_time' => 'Pago único',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Precios y Configuración')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Precio')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->required(),
                        
                        Forms\Components\TextInput::make('setup_fee')
                            ->label('Tarifa de Configuración')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->default(0),
                        
                        Forms\Components\TextInput::make('trial_days')
                            ->label('Días de Prueba')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\TextInput::make('commission_rate')
                            ->label('Tasa de Comisión')
                            ->numeric()
                            ->step(0.0001)
                            ->suffix('%')
                            ->helperText('Ejemplo: 0.05 = 5%')
                            ->default(0.05),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Límites')
                    ->schema([
                        Forms\Components\TextInput::make('max_projects')
                            ->label('Máximo Proyectos')
                            ->numeric()
                            ->helperText('Dejar vacío para ilimitado'),
                        
                        Forms\Components\TextInput::make('max_cooperatives')
                            ->label('Máximo Cooperativas')
                            ->numeric()
                            ->helperText('Dejar vacío para ilimitado'),
                        
                        Forms\Components\TextInput::make('max_investments')
                            ->label('Máximo Inversiones')
                            ->numeric()
                            ->helperText('Dejar vacío para ilimitado'),
                        
                        Forms\Components\TextInput::make('max_consultations')
                            ->label('Máximo Consultas')
                            ->numeric()
                            ->helperText('Dejar vacío para ilimitado'),
                    ])->columns(2),

                Forms\Components\Section::make('Características Premium')
                    ->schema([
                        Forms\Components\Toggle::make('priority_support')
                            ->label('Soporte Prioritario'),
                        
                        Forms\Components\Toggle::make('verified_badge')
                            ->label('Badge Verificado'),
                        
                        Forms\Components\Toggle::make('analytics_access')
                            ->label('Acceso a Analytics'),
                        
                        Forms\Components\Toggle::make('api_access')
                            ->label('Acceso a API'),
                        
                        Forms\Components\Toggle::make('white_label')
                            ->label('Marca Blanca'),
                    ])->columns(3),

                Forms\Components\Section::make('Características y Estado')
                    ->schema([
                        Forms\Components\TagsInput::make('features')
                            ->label('Características')
                            ->helperText('Lista de características incluidas'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'individual',
                        'success' => 'cooperative',
                        'warning' => 'business',
                        'danger' => 'enterprise',
                    ]),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('billing_cycle')
                    ->label('Ciclo')
                    ->colors([
                        'primary' => 'monthly',
                        'success' => 'yearly',
                        'warning' => 'one_time',
                    ]),
                
                Tables\Columns\TextColumn::make('activeSubscriptions_count')
                    ->label('Suscripciones Activas')
                    ->counts('activeSubscriptions')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'individual' => 'Individual',
                        'cooperative' => 'Cooperativa',
                        'business' => 'Empresa',
                        'enterprise' => 'Enterprise',
                    ]),
                
                Tables\Filters\SelectFilter::make('billing_cycle')
                    ->label('Ciclo de Facturación')
                    ->options([
                        'monthly' => 'Mensual',
                        'yearly' => 'Anual',
                        'one_time' => 'Pago único',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información del Plan')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),
                        
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'individual' => 'primary',
                                'cooperative' => 'success',
                                'business' => 'warning',
                                'enterprise' => 'danger',
                            }),
                        
                        Infolists\Components\TextEntry::make('price')
                            ->label('Precio')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('billing_cycle')
                            ->label('Ciclo de Facturación')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'monthly' => 'Mensual',
                                'yearly' => 'Anual',
                                'one_time' => 'Pago único',
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('Estadísticas')
                    ->schema([
                        Infolists\Components\TextEntry::make('activeSubscriptions_count')
                            ->label('Suscripciones Activas')
                            ->formatStateUsing(fn ($record) => $record->activeSubscriptions()->count()),
                        
                        Infolists\Components\TextEntry::make('total_revenue')
                            ->label('Ingresos Totales')
                            ->formatStateUsing(fn ($record) => '€' . number_format($record->getStats()['monthly_revenue'] + $record->getStats()['yearly_revenue'], 2)),
                        
                        Infolists\Components\TextEntry::make('churn_rate')
                            ->label('Tasa de Cancelación')
                            ->formatStateUsing(fn ($record) => number_format($record->getStats()['churn_rate'], 1) . '%'),
                    ])->columns(3),

                Infolists\Components\Section::make('Características')
                    ->schema([
                        Infolists\Components\TextEntry::make('features')
                            ->label('Características Incluidas')
                            ->listWithLineBreaks()
                            ->bulleted(),
                    ]),
            ]);
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}