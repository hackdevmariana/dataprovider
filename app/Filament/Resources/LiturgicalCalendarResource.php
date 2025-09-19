<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiturgicalCalendarResource\Pages;
use App\Filament\Resources\LiturgicalCalendarResource\RelationManagers;
use App\Models\LiturgicalCalendar;
use App\Models\CatholicSaint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LiturgicalCalendarResource extends Resource
{
    protected static ?string $model = LiturgicalCalendar::class;

    protected static ?string $navigationIcon = 'fas-calendar-alt';

    protected static ?string $navigationGroup = 'Religión y Espiritualidad';

    protected static ?string $navigationLabel = 'Calendario Litúrgico';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Evento Litúrgico';

    protected static ?string $pluralModelLabel = 'Calendario Litúrgico';

    
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
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->label('Fecha')
                            ->displayFormat('d/m/Y')
                            ->helperText('Fecha del evento litúrgico'),
                        
                        Forms\Components\Select::make('liturgical_season')
                            ->options([
                                'Advent' => '🟣 Adviento',
                                'Christmas' => '⚪ Navidad',
                                'Ordinary Time' => '🟢 Tiempo Ordinario',
                                'Lent' => '🟣 Cuaresma',
                                'Easter' => '⚪ Pascua',
                            ])
                            ->required()
                            ->label('Temporada Litúrgica'),
                        
                        Forms\Components\TextInput::make('feast_day')
                            ->required()
                            ->maxLength(255)
                            ->label('Fiesta del Día')
                            ->placeholder('Nombre de la fiesta o celebración...'),
                        
                        Forms\Components\Select::make('saint_id')
                            ->relationship('saint', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Santo')
                            ->placeholder('Seleccionar santo...'),
                        
                        Forms\Components\Select::make('celebration_level')
                            ->options([
                                'solemnity' => 'Solemnidad',
                                'feast' => 'Fiesta',
                                'memorial' => 'Memoria',
                                'optional_memorial' => 'Memoria Opcional',
                                'weekday' => 'Feria',
                            ])
                            ->required()
                            ->label('Nivel de Celebración'),
                        
                        Forms\Components\Select::make('color')
                            ->options([
                                'white' => 'Blanco',
                                'red' => 'Rojo',
                                'green' => 'Verde',
                                'purple' => 'Morado',
                                'pink' => 'Rosa',
                                'black' => 'Negro',
                            ])
                            ->required()
                            ->label('Color Litúrgico'),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción del evento litúrgico...'),
                        
                        Forms\Components\Toggle::make('is_holiday')
                            ->label('Es Festivo')
                            ->default(false)
                            ->helperText('El evento es un día festivo'),
                    ])->columns(2),

                Forms\Components\Section::make('Lecturas')
                    ->schema([
                        Forms\Components\KeyValue::make('readings')
                            ->label('Lecturas Bíblicas')
                            ->keyLabel('Tipo de Lectura')
                            ->valueLabel('Referencia Bíblica')
                            ->addActionLabel('Agregar Lectura')
                            ->helperText('Ejemplo: Primera Lectura = Isaías 9:1-6'),
                    ]),

                Forms\Components\Section::make('Oraciones')
                    ->schema([
                        Forms\Components\KeyValue::make('prayers')
                            ->label('Oraciones')
                            ->keyLabel('Tipo de Oración')
                            ->valueLabel('Texto de la Oración')
                            ->addActionLabel('Agregar Oración')
                            ->helperText('Ejemplo: Colecta = Dios todopoderoso...'),
                    ]),

                Forms\Components\Section::make('Tradiciones y Observancias')
                    ->schema([
                        Forms\Components\KeyValue::make('traditions')
                            ->label('Tradiciones')
                            ->keyLabel('Región o Tipo')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Tradición')
                            ->helperText('Ejemplo: España = Cabalgata de Reyes'),
                        
                        Forms\Components\KeyValue::make('special_observances')
                            ->label('Observancias Especiales')
                            ->keyLabel('Tipo de Observancia')
                            ->valueLabel('Descripción')
                            ->addActionLabel('Agregar Observancia')
                            ->helperText('Ejemplo: Festivo Nacional = true'),
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
                
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('feast_day')
                    ->label('Fiesta')
                    ->searchable()
                    ->limit(40)
                    ->weight('medium')
                    ->wrap(),
                
                Tables\Columns\BadgeColumn::make('liturgical_season')
                    ->label('Temporada')
                    ->colors([
                        'primary' => 'Advent',
                        'success' => 'Christmas',
                        'info' => 'Ordinary Time',
                        'warning' => 'Lent',
                        'danger' => 'Easter',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Advent' => '🟣 Adviento',
                        'Christmas' => '⚪ Navidad',
                        'Ordinary Time' => '🟢 Tiempo Ordinario',
                        'Lent' => '🟣 Cuaresma',
                        'Easter' => '⚪ Pascua',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('celebration_level')
                    ->label('Nivel')
                    ->colors([
                        'danger' => 'solemnity',
                        'warning' => 'feast',
                        'info' => 'memorial',
                        'secondary' => 'optional_memorial',
                        'success' => 'weekday',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'solemnity' => 'Solemnidad',
                        'feest' => 'Fiesta',
                        'memorial' => 'Memoria',
                        'optional_memorial' => 'Memoria Opcional',
                        'weekday' => 'Feria',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('color')
                    ->label('Color')
                    ->colors([
                        'secondary' => 'white',
                        'danger' => 'red',
                        'success' => 'green',
                        'primary' => 'purple',
                        'warning' => 'pink',
                        'dark' => 'black',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'white' => 'Blanco',
                        'red' => 'Rojo',
                        'green' => 'Verde',
                        'purple' => 'Morado',
                        'pink' => 'Rosa',
                        'black' => 'Negro',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('saint.name')
                    ->label('Santo')
                    ->searchable()
                    ->limit(25)
                    ->placeholder('Sin santo'),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable()
                    ->placeholder('Sin descripción'),
                
                Tables\Columns\IconColumn::make('is_holiday')
                    ->label('Festivo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('secondary'),
                
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Día')
                    ->getStateUsing(fn ($record): string => $record->day_of_week)
                    ->sortable(false),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('liturgical_season')
                    ->options([
                        'Advent' => '🟣 Adviento',
                        'Christmas' => '⚪ Navidad',
                        'Ordinary Time' => '🟢 Tiempo Ordinario',
                        'Lent' => '🟣 Cuaresma',
                        'Easter' => '⚪ Pascua',
                    ])
                    ->label('Temporada Litúrgica'),
                
                Tables\Filters\SelectFilter::make('celebration_level')
                    ->options([
                        'solemnity' => 'Solemnidad',
                        'feast' => 'Fiesta',
                        'memorial' => 'Memoria',
                        'optional_memorial' => 'Memoria Opcional',
                        'weekday' => 'Feria',
                    ])
                    ->label('Nivel de Celebración'),
                
                Tables\Filters\SelectFilter::make('color')
                    ->options([
                        'white' => 'Blanco',
                        'red' => 'Rojo',
                        'green' => 'Verde',
                        'purple' => 'Morado',
                        'pink' => 'Rosa',
                        'black' => 'Negro',
                    ])
                    ->label('Color Litúrgico'),
                
                Tables\Filters\Filter::make('holidays_only')
                    ->label('Solo Festivos')
                    ->query(fn (Builder $query): Builder => $query->where('is_holiday', true)),
                
                Tables\Filters\Filter::make('solemnities_only')
                    ->label('Solo Solemnidades')
                    ->query(fn (Builder $query): Builder => $query->where('celebration_level', 'solemnity')),
                
                Tables\Filters\Filter::make('with_saints')
                    ->label('Con Santos')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('saint_id')),
                
                Tables\Filters\Filter::make('this_month')
                    ->label('Este Mes')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('date', now()->month)),
                
                Tables\Filters\Filter::make('this_year')
                    ->label('Este Año')
                    ->query(fn (Builder $query): Builder => $query->whereYear('date', now()->year)),
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
                
                Tables\Actions\Action::make('toggle_holiday')
                    ->label(fn ($record): string => $record->is_holiday ? 'Quitar Festivo' : 'Marcar Festivo')
                    ->icon(fn ($record): string => $record->is_holiday ? 'fas-calendar-check' : 'fas-calendar')
                    ->action(function ($record): void {
                        $record->update(['is_holiday' => !$record->is_holiday]);
                    })
                    ->color(fn ($record): string => $record->is_holiday ? 'success' : 'warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('mark_holidays')
                        ->label('Marcar como Festivos')
                        ->icon('fas-calendar-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_holiday' => true]);
                        })
                        ->color('success'),
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
            'index' => Pages\ListLiturgicalCalendars::route('/'),
            'create' => Pages\CreateLiturgicalCalendar::route('/create'),
            'view' => Pages\ViewLiturgicalCalendar::route('/{record}'),
            'edit' => Pages\EditLiturgicalCalendar::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}