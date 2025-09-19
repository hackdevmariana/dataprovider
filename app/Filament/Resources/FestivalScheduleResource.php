<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FestivalScheduleResource\Pages;
use App\Models\FestivalSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FestivalScheduleResource extends Resource
{
    protected static ?string $model = FestivalSchedule::class;

    protected static ?string $navigationIcon = 'fas-calendar-day';

    protected static ?string $navigationGroup = 'Eventos y Cultura';

    protected static ?string $navigationLabel = 'Horarios de Festivales';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Horario de Festival';

    protected static ?string $pluralModelLabel = 'Horarios de Festivales';

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
                        Forms\Components\Select::make('festival_id')
                            ->relationship('festival', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Festival'),
                        
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->label('Fecha')
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\TimePicker::make('opening_time')
                            ->required()
                            ->label('Hora de Apertura')
                            ->displayFormat('H:i'),
                        
                        Forms\Components\TimePicker::make('closing_time')
                            ->required()
                            ->label('Hora de Cierre')
                            ->displayFormat('H:i'),
                    ])->columns(2),

                Forms\Components\Section::make('Eventos y Actividades')
                    ->schema([
                        Forms\Components\Repeater::make('main_events')
                            ->label('Eventos Principales')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre del Evento')
                                    ->required(),
                                Forms\Components\TimePicker::make('time')
                                    ->label('Hora')
                                    ->displayFormat('H:i'),
                                Forms\Components\TextInput::make('location')
                                    ->label('Ubicación'),
                            ])
                            ->columns(3)
                            ->collapsible(),

                        Forms\Components\Repeater::make('side_activities')
                            ->label('Actividades Secundarias')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre de la Actividad')
                                    ->required(),
                                Forms\Components\TimePicker::make('time')
                                    ->label('Hora')
                                    ->displayFormat('H:i'),
                                Forms\Components\TextInput::make('location')
                                    ->label('Ubicación'),
                            ])
                            ->columns(3)
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('special_notes')
                            ->label('Notas Especiales')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('weather_forecast')
                            ->label('Pronóstico del Tiempo'),
                        
                        Forms\Components\TextInput::make('expected_attendance')
                            ->label('Asistencia Esperada')
                            ->numeric()
                            ->suffix('personas'),
                    ])->columns(2),

                Forms\Components\Section::make('Transporte y Estacionamiento')
                    ->schema([
                        Forms\Components\Repeater::make('transportation_info')
                            ->label('Información de Transporte')
                            ->schema([
                                Forms\Components\TextInput::make('type')
                                    ->label('Tipo de Transporte'),
                                Forms\Components\TextInput::make('route')
                                    ->label('Ruta/Línea'),
                                Forms\Components\TextInput::make('schedule')
                                    ->label('Horario'),
                            ])
                            ->columns(3)
                            ->collapsible(),

                        Forms\Components\Repeater::make('parking_info')
                            ->label('Información de Estacionamiento')
                            ->schema([
                                Forms\Components\TextInput::make('location')
                                    ->label('Ubicación'),
                                Forms\Components\TextInput::make('capacity')
                                    ->label('Capacidad')
                                    ->numeric(),
                                Forms\Components\TextInput::make('price')
                                    ->label('Precio'),
                            ])
                            ->columns(3)
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('festival.name')
                    ->label('Festival')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('opening_time')
                    ->label('Apertura')
                    ->time('H:i'),
                
                Tables\Columns\TextColumn::make('closing_time')
                    ->label('Cierre')
                    ->time('H:i'),
                
                Tables\Columns\TextColumn::make('expected_attendance')
                    ->label('Asistencia Esperada')
                    ->numeric()
                    ->suffix(' personas')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('weather_forecast')
                    ->label('Tiempo')
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('festival_id')
                    ->relationship('festival', 'name')
                    ->label('Festival'),
                
                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', today())),
                
                Tables\Filters\Filter::make('this_week')
                    ->label('Esta Semana')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])),
                
                Tables\Filters\Filter::make('upcoming')
                    ->label('Próximos')
                    ->query(fn (Builder $query): Builder => $query->where('date', '>', now())),
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
            ->defaultSort('date', 'desc')
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
            'index' => Pages\ListFestivalSchedules::route('/'),
            'create' => Pages\CreateFestivalSchedule::route('/create'),
            'view' => Pages\ViewFestivalSchedule::route('/{record}'),
            'edit' => Pages\EditFestivalSchedule::route('/{record}/edit'),
        ];
    }
}