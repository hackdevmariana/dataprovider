<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformResource\Pages;
use App\Filament\Resources\PlatformResource\RelationManagers;
use App\Models\Platform;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlatformResource extends Resource
{
    protected static ?string $navigationGroup = 'Sistema y Administración';
    protected static ?string $model = Platform::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Plataformas';
    protected static ?string $modelLabel = 'Plataforma';
    protected static ?string $pluralModelLabel = 'Plataformas';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre de la Plataforma'),
                        
                        Select::make('type')
                            ->required()
                            ->label('Tipo')
                            ->options([
                                'energy_trading' => 'Intercambio Energético',
                                'grid_operator' => 'Operador de Red',
                                'weather_service' => 'Servicio Meteorológico',
                                'energy_market' => 'Mercado Energético',
                                'regulatory' => 'Regulatorio',
                                'energy_agency' => 'Agencia Energética',
                                'iot_platform' => 'Plataforma IoT',
                                'data_provider' => 'Proveedor de Datos',
                                'api_service' => 'Servicio API',
                                'other' => 'Otro',
                            ])
                            ->searchable(),
                        
                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3),
                        
                        Select::make('status')
                            ->required()
                            ->label('Estado')
                            ->options([
                                'active' => 'Activa',
                                'inactive' => 'Inactiva',
                                'maintenance' => 'Mantenimiento',
                                'deprecated' => 'Deprecada',
                            ])
                            ->default('active'),
                    ])->columns(2),

                Section::make('URLs y Endpoints')
                    ->schema([
                        TextInput::make('url')
                            ->url()
                            ->maxLength(255)
                            ->label('URL Principal'),
                        
                        TextInput::make('api_endpoint')
                            ->url()
                            ->maxLength(255)
                            ->label('Endpoint de API'),
                        
                        TextInput::make('documentation_url')
                            ->url()
                            ->maxLength(255)
                            ->label('URL de Documentación'),
                        
                        TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email de Contacto'),
                    ])->columns(2),

                Section::make('Configuración Técnica')
                    ->schema([
                        TextInput::make('country')
                            ->maxLength(255)
                            ->label('País')
                            ->default('ES'),
                        
                        TextInput::make('license')
                            ->maxLength(255)
                            ->label('Licencia'),
                        
                        TextInput::make('rate_limit')
                            ->numeric()
                            ->label('Límite de Rate (requests/min)'),
                        
                        Toggle::make('requires_auth')
                            ->label('Requiere Autenticación')
                            ->default(false),
                        
                        Toggle::make('is_official')
                            ->label('Es Oficial')
                            ->default(false),
                    ])->columns(3),

                Section::make('Características y Tipos de Datos')
                    ->schema([
                        KeyValue::make('features')
                            ->label('Características')
                            ->keyLabel('Característica')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Característica'),
                        
                        KeyValue::make('data_types')
                            ->label('Tipos de Datos')
                            ->keyLabel('Tipo')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Tipo de Dato'),
                    ])->columns(1),

                Section::make('Notas Adicionales')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                
                BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'energy_trading',
                        'success' => 'grid_operator',
                        'warning' => 'weather_service',
                        'info' => 'energy_market',
                        'danger' => 'regulatory',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'energy_trading' => 'Intercambio Energético',
                        'grid_operator' => 'Operador de Red',
                        'weather_service' => 'Servicio Meteorológico',
                        'energy_market' => 'Mercado Energético',
                        'regulatory' => 'Regulatorio',
                        'energy_agency' => 'Agencia Energética',
                        'iot_platform' => 'Plataforma IoT',
                        'data_provider' => 'Proveedor de Datos',
                        'api_service' => 'Servicio API',
                        'other' => 'Otro',
                        default => $state,
                    }),
                
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                
                TextColumn::make('country')
                    ->label('País')
                    ->searchable()
                    ->sortable(),
                
                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'maintenance',
                        'gray' => 'deprecated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activa',
                        'inactive' => 'Inactiva',
                        'maintenance' => 'Mantenimiento',
                        'deprecated' => 'Deprecada',
                        default => $state,
                    }),
                
                IconColumn::make('is_official')
                    ->label('Oficial')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                IconColumn::make('requires_auth')
                    ->label('Requiere Auth')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('success'),
                
                TextColumn::make('rate_limit')
                    ->label('Rate Limit')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state ? "{$state}/min" : '-'),
                
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(30)
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('api_endpoint')
                    ->label('API Endpoint')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'energy_trading' => 'Intercambio Energético',
                        'grid_operator' => 'Operador de Red',
                        'weather_service' => 'Servicio Meteorológico',
                        'energy_market' => 'Mercado Energético',
                        'regulatory' => 'Regulatorio',
                        'energy_agency' => 'Agencia Energética',
                        'iot_platform' => 'Plataforma IoT',
                        'data_provider' => 'Proveedor de Datos',
                        'api_service' => 'Servicio API',
                        'other' => 'Otro',
                    ]),
                
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'inactive' => 'Inactiva',
                        'maintenance' => 'Mantenimiento',
                        'deprecated' => 'Deprecada',
                    ]),
                
                SelectFilter::make('country')
                    ->label('País')
                    ->options(function () {
                        return Platform::distinct()->pluck('country', 'country')->toArray();
                    }),
                
                TernaryFilter::make('is_official')
                    ->label('Oficial')
                    ->boolean()
                    ->trueLabel('Solo oficiales')
                    ->falseLabel('Solo no oficiales')
                    ->native(false),
                
                TernaryFilter::make('requires_auth')
                    ->label('Requiere Autenticación')
                    ->boolean()
                    ->trueLabel('Solo con auth')
                    ->falseLabel('Solo sin auth')
                    ->native(false),
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
            ->defaultSort('name');
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
            'index' => Pages\ListPlatforms::route('/'),
            'create' => Pages\CreatePlatform::route('/create'),
            'edit' => Pages\EditPlatform::route('/{record}/edit'),
        ];
    }
}
