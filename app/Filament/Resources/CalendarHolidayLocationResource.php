<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarHolidayLocationResource\Pages;
use App\Filament\Resources\CalendarHolidayLocationResource\RelationManagers;
use App\Models\CalendarHolidayLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CalendarHolidayLocationResource extends Resource
{
    protected static ?string $model = CalendarHolidayLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    
    protected static ?string $navigationGroup = 'Events & Calendar';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Ubicación de Feriado';
    
    protected static ?string $pluralModelLabel = 'Ubicaciones de Feriados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Fiesta')
                    ->schema([
                        Forms\Components\Select::make('calendar_holiday_id')
                            ->label('Fiesta del Calendario')
                            ->relationship('holiday', 'name')
                            ->searchable()
                            ->required()
                            ->placeholder('Selecciona una fiesta del calendario')
                            ->helperText('La fiesta a la que se le asigna la ubicación'),
                    ]),

                Forms\Components\Section::make('Ubicación Geográfica')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->label('País')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->placeholder('Selecciona un país')
                            ->helperText('País donde se celebra la fiesta')
                            ->reactive()
                            ->afterStateUpdated(fn () => null),

                        Forms\Components\Select::make('autonomous_community_id')
                            ->label('Comunidad Autónoma')
                            ->relationship('autonomousCommunity', 'name')
                            ->searchable()
                            ->placeholder('Selecciona una comunidad autónoma')
                            ->helperText('Comunidad autónoma donde se celebra la fiesta')
                            ->reactive()
                            ->afterStateUpdated(fn () => null),

                        Forms\Components\Select::make('province_id')
                            ->label('Provincia')
                            ->relationship('province', 'name')
                            ->searchable()
                            ->placeholder('Selecciona una provincia')
                            ->helperText('Provincia donde se celebra la fiesta')
                            ->reactive()
                            ->afterStateUpdated(fn () => null),

                        Forms\Components\Select::make('municipality_id')
                            ->label('Municipio')
                            ->relationship('municipality', 'name')
                            ->searchable()
                            ->placeholder('Selecciona un municipio')
                            ->helperText('Municipio específico donde se celebra la fiesta'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información del Sistema')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),
                    ])
                    ->collapsible()
                    ->collapsed()
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
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->width(60),

                Tables\Columns\TextColumn::make('holiday.name')
                    ->label('Fiesta del Calendario')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->getStateUsing(fn ($record) => $record->holiday?->name ?? 'Sin fiesta')
                    ->tooltip(fn ($record) => $record->holiday?->name ?? 'Sin fiesta')
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('holiday.date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->getStateUsing(fn ($record) => $record->holiday?->date ?? null)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('location_hierarchy')
                    ->label('Ubicación')
                    ->getStateUsing(function ($record) {
                        $location = [];
                        if ($record->municipality) {
                            $location[] = $record->municipality->name;
                        }
                        if ($record->province) {
                            $location[] = $record->province->name;
                        }
                        if ($record->autonomousCommunity) {
                            $location[] = $record->autonomousCommunity->name;
                        }
                        if ($record->country) {
                            $location[] = $record->country->name;
                        }
                        return implode(' → ', array_reverse($location));
                    })
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($record) => self::getLocationTooltip($record))
                    ->badge()
                    ->color('success')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('location_type')
                    ->label('Tipo de Ubicación')
                    ->getStateUsing(function ($record) {
                        if ($record->municipality) return 'Municipal';
                        if ($record->province) return 'Provincial';
                        if ($record->autonomousCommunity) return 'Autonómico';
                        if ($record->country) return 'Nacional';
                        return 'Sin ubicación';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->municipality) return 'danger';
                        if ($record->province) return 'warning';
                        if ($record->autonomousCommunity) return 'info';
                        if ($record->country) return 'success';
                        return 'gray';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('country.name')
                    ->label('País')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => $record->country?->name ?? 'Sin país'),

                Tables\Columns\TextColumn::make('autonomousCommunity.name')
                    ->label('Comunidad Autónoma')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => $record->autonomousCommunity?->name ?? 'Sin comunidad'),

                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provincia')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => $record->province?->name ?? 'Sin provincia'),

                Tables\Columns\TextColumn::make('municipality.name')
                    ->label('Municipio')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => $record->municipality?->name ?? 'Sin municipio'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => $record->created_at ? $record->created_at->format('d/m/Y H:i') : 'Sin fecha'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => $record->updated_at ? $record->updated_at->format('d/m/Y H:i') : 'Sin fecha'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('holiday')
                    ->label('Fiesta del Calendario')
                    ->relationship('holiday', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Filtrar por fiesta')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('country')
                    ->label('País')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Filtrar por país')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('autonomous_community')
                    ->label('Comunidad Autónoma')
                    ->relationship('autonomousCommunity', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Filtrar por comunidad autónoma')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('province')
                    ->label('Provincia')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Filtrar por provincia')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('municipality')
                    ->label('Municipio')
                    ->relationship('municipality', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Filtrar por municipio')
                    ->multiple(),

                Tables\Filters\Filter::make('location_type')
                    ->label('Tipo de Ubicación')
                    ->form([
                        Forms\Components\CheckboxList::make('types')
                            ->label('Tipos de ubicación')
                            ->options([
                                'national' => 'Nacional (solo país)',
                                'autonomous' => 'Autonómico (país + comunidad)',
                                'provincial' => 'Provincial (país + comunidad + provincia)',
                                'municipal' => 'Municipal (país + comunidad + provincia + municipio)',
                            ])
                            ->default(['national', 'autonomous', 'provincial', 'municipal'])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['types'])) {
                            return $query;
                        }

                        $query->where(function ($q) use ($data) {
                            if (in_array('national', $data['types'])) {
                                $q->orWhere(function ($subQ) {
                                    $subQ->whereNotNull('country_id')
                                         ->whereNull('autonomous_community_id')
                                         ->whereNull('province_id')
                                         ->whereNull('municipality_id');
                                });
                            }

                            if (in_array('autonomous', $data['types'])) {
                                $q->orWhere(function ($subQ) {
                                    $subQ->whereNotNull('country_id')
                                         ->whereNotNull('autonomous_community_id')
                                         ->whereNull('province_id')
                                         ->whereNull('municipality_id');
                                });
                            }

                            if (in_array('provincial', $data['types'])) {
                                $q->orWhere(function ($subQ) {
                                    $subQ->whereNotNull('country_id')
                                         ->whereNotNull('autonomous_community_id')
                                         ->whereNotNull('province_id')
                                         ->whereNull('municipality_id');
                                });
                            }

                            if (in_array('municipal', $data['types'])) {
                                $q->orWhere(function ($subQ) {
                                    $subQ->whereNotNull('country_id')
                                         ->whereNotNull('autonomous_community_id')
                                         ->whereNotNull('province_id')
                                         ->whereNotNull('municipality_id');
                                });
                            }
                        });

                        return $query;
                    }),

                Tables\Filters\Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('holiday', function ($q) use ($data) {
                            if ($data['date_from']) {
                                $q->where('date', '>=', $data['date_from']);
                            }
                            if ($data['date_to']) {
                                $q->where('date', '<=', $data['date_to']);
                            }
                        });
                    }),

                Tables\Filters\TernaryFilter::make('has_municipality')
                    ->label('Con Municipio')
                    ->placeholder('Todas las ubicaciones')
                    ->trueLabel('Solo con municipio')
                    ->falseLabel('Sin municipio'),

                Tables\Filters\TernaryFilter::make('has_province')
                    ->label('Con Provincia')
                    ->placeholder('Todas las ubicaciones')
                    ->trueLabel('Solo con provincia')
                    ->falseLabel('Sin provincia'),

                Tables\Filters\TernaryFilter::make('has_autonomous_community')
                    ->label('Con Comunidad Autónoma')
                    ->placeholder('Todas las ubicaciones')
                    ->trueLabel('Solo con comunidad autónoma')
                    ->falseLabel('Sin comunidad autónoma'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalles')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->tooltip('Ver detalles completos de la ubicación'),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->tooltip('Editar la ubicación de la fiesta'),

                Tables\Actions\Action::make('view_holiday')
                    ->label('Ver Fiesta')
                    ->icon('heroicon-o-calendar')
                    ->color('primary')
                    ->url(fn ($record) => $record->holiday ? route('filament.admin.resources.calendar-holidays.edit', $record->holiday) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->holiday !== null)
                    ->tooltip('Ver detalles de la fiesta del calendario'),

                Tables\Actions\Action::make('view_country')
                    ->label('Ver País')
                    ->icon('heroicon-o-flag')
                    ->color('success')
                    ->url(fn ($record) => $record->country ? route('filament.admin.resources.countries.edit', $record->country) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->country !== null)
                    ->tooltip('Ver detalles del país'),

                Tables\Actions\Action::make('view_autonomous_community')
                    ->label('Ver Comunidad')
                    ->icon('heroicon-o-map')
                    ->color('info')
                    ->url(fn ($record) => $record->autonomousCommunity ? route('filament.admin.resources.autonomous-communities.edit', $record->autonomousCommunity) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->autonomousCommunity !== null)
                    ->tooltip('Ver detalles de la comunidad autónoma'),

                Tables\Actions\Action::make('view_province')
                    ->label('Ver Provincia')
                    ->icon('heroicon-o-building-office')
                    ->color('warning')
                    ->url(fn ($record) => $record->province ? route('filament.admin.resources.provinces.edit', $record->province) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->province !== null)
                    ->tooltip('Ver detalles de la provincia'),

                Tables\Actions\Action::make('view_municipality')
                    ->label('Ver Municipio')
                    ->icon('heroicon-o-home')
                    ->color('danger')
                    ->url(fn ($record) => $record->municipality ? route('filament.admin.resources.municipalities.edit', $record->municipality) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->municipality !== null)
                    ->tooltip('Ver detalles del municipio'),

                Tables\Actions\Action::make('copy_location')
                    ->label('Copiar Ubicación')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('secondary')
                    ->action(function ($record) {
                        $location = [];
                        if ($record->municipality) $location[] = $record->municipality->name;
                        if ($record->province) $location[] = $record->province->name;
                        if ($record->autonomousCommunity) $location[] = $record->autonomousCommunity->name;
                        if ($record->country) $location[] = $record->country->name;
                        
                        $locationString = implode(', ', array_reverse($location));
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Ubicación copiada')
                            ->body("Se ha copiado: {$locationString}")
                            ->success()
                            ->send();
                    })
                    ->tooltip('Copiar la ubicación al portapapeles'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar Seleccionadas')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar ubicaciones seleccionadas?')
                        ->modalDescription('Esta acción eliminará permanentemente las ubicaciones seleccionadas y no se puede deshacer.')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->modalCancelActionLabel('Cancelar')
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('export_locations')
                        ->label('Exportar Ubicaciones')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            \Filament\Notifications\Notification::make()
                                ->title('Exportación iniciada')
                                ->body('Se están exportando ' . $records->count() . ' ubicaciones de fiestas')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('assign_country')
                        ->label('Asignar País')
                        ->icon('heroicon-o-flag')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('country_id')
                                ->label('País')
                                ->relationship('country', 'name')
                                ->searchable()
                                ->required()
                                ->placeholder('Selecciona un país'),
                        ])
                        ->action(function ($records, array $data) {
                            $updated = 0;
                            foreach ($records as $record) {
                                $record->update(['country_id' => $data['country_id']]);
                                $updated++;
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('País asignado')
                                ->body("Se ha asignado el país a {$updated} ubicaciones")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('assign_autonomous_community')
                        ->label('Asignar Comunidad')
                        ->icon('heroicon-o-map')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('autonomous_community_id')
                                ->label('Comunidad Autónoma')
                                ->relationship('autonomousCommunity', 'name')
                                ->searchable()
                                ->required()
                                ->placeholder('Selecciona una comunidad autónoma'),
                        ])
                        ->action(function ($records, array $data) {
                            $updated = 0;
                            foreach ($records as $record) {
                                $record->update(['autonomous_community_id' => $data['autonomous_community_id']]);
                                $updated++;
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Comunidad asignada')
                                ->body("Se ha asignado la comunidad autónoma a {$updated} ubicaciones")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('assign_province')
                        ->label('Asignar Provincia')
                        ->icon('heroicon-o-building-office')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('province_id')
                                ->label('Provincia')
                                ->relationship('province', 'name')
                                ->searchable()
                                ->required()
                                ->placeholder('Selecciona una provincia'),
                        ])
                        ->action(function ($records, array $data) {
                            $updated = 0;
                            foreach ($records as $record) {
                                $record->update(['province_id' => $data['province_id']]);
                                $updated++;
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Provincia asignada')
                                ->body("Se ha asignado la provincia a {$updated} ubicaciones")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('assign_municipality')
                        ->label('Asignar Municipio')
                        ->icon('heroicon-o-home')
                        ->color('danger')
                        ->form([
                            Forms\Components\Select::make('municipality_id')
                                ->label('Municipio')
                                ->relationship('municipality', 'name')
                                ->searchable()
                                ->required()
                                ->placeholder('Selecciona un municipio'),
                        ])
                        ->action(function ($records, array $data) {
                            $updated = 0;
                            foreach ($records as $record) {
                                $record->update(['municipality_id' => $data['municipality_id']]);
                                $updated++;
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Municipio asignado')
                                ->body("Se ha asignado el municipio a {$updated} ubicaciones")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['holiday', 'country', 'autonomousCommunity', 'province', 'municipality']));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['holiday', 'country', 'autonomousCommunity', 'province', 'municipality']);
    }

    /**
     * Obtener el tooltip para la ubicación.
     */
    private static function getLocationTooltip($record): string
    {
        $location = [];
        if ($record->municipality) {
            $location[] = 'Municipio: ' . $record->municipality->name;
        }
        if ($record->province) {
            $location[] = 'Provincia: ' . $record->province->name;
        }
        if ($record->autonomousCommunity) {
            $location[] = 'Comunidad: ' . $record->autonomousCommunity->name;
        }
        if ($record->country) {
            $location[] = 'País: ' . $record->country->name;
        }
        return implode("\n", $location);
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
            'index' => Pages\ListCalendarHolidayLocations::route('/'),
            'create' => Pages\CreateCalendarHolidayLocation::route('/create'),
            'edit' => Pages\EditCalendarHolidayLocation::route('/{record}/edit'),
        ];
    }
}
