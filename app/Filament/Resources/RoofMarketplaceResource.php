<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoofMarketplaceResource\Pages;
use App\Filament\Resources\RoofMarketplaceResource\RelationManagers;
use App\Models\RoofMarketplace;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class RoofMarketplaceResource extends Resource
{
    protected static ?string $model = RoofMarketplace::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Proyectos y Monetización';
    protected static ?string $modelLabel = 'Mercado de Techos';
    protected static ?string $pluralModelLabel = 'Mercado de Techos';
    protected static ?int $navigationSort = 7;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Básica')
                ->description('Datos principales del espacio en el mercado')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Título del Anuncio')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state) {
                                        $set('slug', Str::slug($state));
                                    }
                                })
                                ->helperText('Título atractivo para el anuncio'),

                            Forms\Components\TextInput::make('slug')
                                ->label('URL Amigable')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('URL única para el anuncio'),
                        ]),

                    Forms\Components\Textarea::make('description')
                        ->label('Descripción')
                        ->required()
                        ->rows(4)
                        ->maxLength(1000)
                        ->helperText('Descripción detallada del espacio disponible'),

                    Forms\Components\Select::make('space_type')
                        ->label('Tipo de Espacio')
                        ->required()
                        ->options([
                            'residential_roof' => '🏠 Techo Residencial',
                            'commercial_roof' => '🏢 Techo Comercial',
                            'industrial_roof' => '🏭 Techo Industrial',
                            'agricultural_land' => '🌾 Terreno Agrícola',
                            'parking_lot' => '🅿️ Aparcamiento',
                            'warehouse_roof' => '🏗️ Techo de Almacén',
                            'community_space' => '🏘️ Espacio Comunitario',
                            'unused_land' => '🌱 Terreno Sin Uso',
                            'building_facade' => '🏛️ Fachada de Edificio',
                            'other' => '📋 Otro',
                        ])
                        ->searchable()
                        ->default('residential_roof'),

                    Forms\Components\Select::make('owner_id')
                        ->label('Propietario')
                        ->relationship('owner', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\TextInput::make('address')
                        ->label('Dirección Completa')
                        ->required()
                        ->maxLength(500)
                        ->helperText('Dirección exacta del espacio'),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('total_area_m2')
                                ->label('Área Total (m²)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0.01)
                                ->required()
                                ->suffix('m²'),

                            Forms\Components\TextInput::make('usable_area_m2')
                                ->label('Área Utilizable (m²)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0.01)
                                ->required()
                                ->suffix('m²'),
                        ]),

                    Forms\Components\Select::make('offering_type')
                        ->label('Tipo de Oferta')
                        ->required()
                        ->options([
                            'rent' => '💰 Alquiler del Espacio',
                            'sale' => '🏠 Venta del Espacio',
                            'partnership' => '🤝 Sociedad/Participación',
                            'free_use' => '🆓 Uso Gratuito',
                            'energy_share' => '⚡ Participación en Energía',
                            'mixed' => '🔄 Términos Mixtos',
                        ])
                        ->searchable()
                        ->default('rent'),

                    Forms\Components\Select::make('availability_status')
                        ->label('Estado de Disponibilidad')
                        ->required()
                        ->options([
                            'available' => '✅ Disponible',
                            'under_negotiation' => '🤝 En Negociación',
                            'reserved' => '📋 Reservado',
                            'contracted' => '📝 Contratado',
                            'occupied' => '🚫 Ocupado',
                            'maintenance' => '🔧 En Mantenimiento',
                            'temporarily_unavailable' => '⏸️ Temporalmente No Disponible',
                            'withdrawn' => '❌ Retirado',
                        ])
                        ->searchable()
                        ->default('available'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Anuncio Activo')
                        ->default(true),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('Anuncio Destacado')
                        ->default(false),
                ])
                ->collapsible(false),
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

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('space_type')
                    ->label('Tipo de Espacio')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'primary' => 'residential_roof',
                        'success' => 'commercial_roof',
                        'warning' => 'industrial_roof',
                        'info' => 'agricultural_land',
                        'danger' => 'other',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'residential_roof' => '🏠 Residencial',
                            'commercial_roof' => '🏢 Comercial',
                            'industrial_roof' => '🏭 Industrial',
                            'agricultural_land' => '🌾 Agrícola',
                            'parking_lot' => '🅿️ Aparcamiento',
                            'warehouse_roof' => '🏗️ Almacén',
                            'community_space' => '🏘️ Comunitario',
                            'unused_land' => '🌱 Sin Uso',
                            'building_facade' => '🏛️ Fachada',
                            'other' => '📋 Otro',
                            default => $state,
                        };
                    }),

                Tables\Columns\TextColumn::make('usable_area_m2')
                    ->label('Área Utilizable')
                    ->numeric()
                    ->sortable()
                    ->suffix(' m²')
                    ->badge(),

                Tables\Columns\BadgeColumn::make('offering_type')
                    ->label('Tipo de Oferta')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'success' => 'rent',
                        'warning' => 'sale',
                        'info' => 'partnership',
                        'primary' => 'free_use',
                        'danger' => 'energy_share',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'rent' => '💰 Alquiler',
                            'sale' => '🏠 Venta',
                            'partnership' => '🤝 Sociedad',
                            'free_use' => '🆓 Gratuito',
                            'energy_share' => '⚡ Energía',
                            'mixed' => '🔄 Mixto',
                            default => $state,
                        };
                    }),

                Tables\Columns\BadgeColumn::make('availability_status')
                    ->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'success' => 'available',
                        'warning' => 'under_negotiation',
                        'info' => 'reserved',
                        'primary' => 'contracted',
                        'danger' => 'occupied',
                        'secondary' => 'maintenance',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'available' => '✅ Disponible',
                            'under_negotiation' => '🤝 En Negociación',
                            'reserved' => '📋 Reservado',
                            'contracted' => '📝 Contratado',
                            'occupied' => '🚫 Ocupado',
                            'maintenance' => '🔧 Mantenimiento',
                            'temporarily_unavailable' => '⏸️ No Disponible',
                            'withdrawn' => '❌ Retirado',
                            default => $state,
                        };
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Propietario')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
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
                Tables\Filters\SelectFilter::make('space_type')
                    ->label('Tipo de Espacio')
                    ->options([
                        'residential_roof' => '🏠 Techo Residencial',
                        'commercial_roof' => '🏢 Techo Comercial',
                        'industrial_roof' => '🏭 Techo Industrial',
                        'agricultural_land' => '🌾 Terreno Agrícola',
                        'parking_lot' => '🅿️ Aparcamiento',
                        'warehouse_roof' => '🏗️ Techo de Almacén',
                        'community_space' => '🏘️ Espacio Comunitario',
                        'unused_land' => '🌱 Terreno Sin Uso',
                        'building_facade' => '🏛️ Fachada de Edificio',
                        'other' => '📋 Otro',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('offering_type')
                    ->label('Tipo de Oferta')
                    ->options([
                        'rent' => '💰 Alquiler',
                        'sale' => '🏠 Venta',
                        'partnership' => '🤝 Sociedad',
                        'free_use' => '🆓 Gratuito',
                        'energy_share' => '⚡ Energía',
                        'mixed' => '🔄 Mixto',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('availability_status')
                    ->label('Estado de Disponibilidad')
                    ->options([
                        'available' => '✅ Disponible',
                        'under_negotiation' => '🤝 En Negociación',
                        'reserved' => '📋 Reservado',
                        'contracted' => '📝 Contratado',
                        'occupied' => '🚫 Ocupado',
                        'maintenance' => '🔧 En Mantenimiento',
                        'temporarily_unavailable' => '⏸️ No Disponible',
                        'withdrawn' => '❌ Retirado',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\Filter::make('active_only')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query) => $query->where('is_active', true))
                    ->toggle(),

                Tables\Filters\Filter::make('featured_only')
                    ->label('Solo Destacados')
                    ->query(fn (Builder $query) => $query->where('is_featured', true))
                    ->toggle(),

                Tables\Filters\Filter::make('available_spaces')
                    ->label('Espacios Disponibles')
                    ->query(fn (Builder $query) => $query->where('availability_status', 'available'))
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

                Tables\Actions\Action::make('toggle_active')
                    ->label('Cambiar Activo')
                    ->icon('heroicon-o-power')
                    ->color(function ($record) {
                        if (!$record) return 'gray';
                        return $record->is_active ? 'danger' : 'success';
                    })
                    ->action(function (RoofMarketplace $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                        
                        $status = $record->is_active ? 'activado' : 'desactivado';
                        \Filament\Notifications\Notification::make()
                            ->title('Estado Actualizado')
                            ->body("El anuncio ha sido {$status}")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Cambiar el estado activo del anuncio'),

                Tables\Actions\Action::make('toggle_featured')
                    ->label('Cambiar Destacado')
                    ->icon('heroicon-o-star')
                    ->color(function ($record) {
                        if (!$record) return 'gray';
                        return $record->is_featured ? 'gray' : 'warning';
                    })
                    ->action(function (RoofMarketplace $record): void {
                        $record->update(['is_featured' => !$record->is_featured]);
                        
                        $status = $record->is_featured ? 'marcado como destacado' : 'desmarcado como destacado';
                        \Filament\Notifications\Notification::make()
                            ->title('Estado Actualizado')
                            ->body("El anuncio ha sido {$status}")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Cambiar el estado destacado del anuncio'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Eliminar Seleccionados'),
                
                Tables\Actions\BulkAction::make('activate_selected')
                    ->label('Activar Seleccionados')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($records): void {
                        $records->each(function ($record) {
                            $record->update(['is_active' => true]);
                        });
                        $count = $records->count();
                        \Filament\Notifications\Notification::make()
                            ->title('Anuncios Activados')
                            ->body("Se han activado {$count} anuncios")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Activar anuncios seleccionados'),
                
                Tables\Actions\BulkAction::make('deactivate_selected')
                    ->label('Desactivar Seleccionados')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($records): void {
                        $records->each(function ($record) {
                            $record->update(['is_active' => false]);
                        });
                        $count = $records->count();
                        \Filament\Notifications\Notification::make()
                            ->title('Anuncios Desactivados')
                            ->body("Se han desactivado {$count} anuncios")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Desactivar anuncios seleccionados'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([25, 50, 100])
            ->searchable()
            ->searchPlaceholder('Buscar por título, dirección o tipo de espacio...');
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
            'index' => Pages\ListRoofMarketplaces::route('/'),
            'create' => Pages\CreateRoofMarketplace::route('/create'),
            'edit' => Pages\EditRoofMarketplace::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['owner', 'municipality', 'verifiedBy']);
    }
}
