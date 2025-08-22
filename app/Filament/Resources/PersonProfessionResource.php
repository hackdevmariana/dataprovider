<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonProfessionResource\Pages;
use App\Filament\Resources\PersonProfessionResource\RelationManagers;
use App\Models\PersonProfession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonProfessionResource extends Resource
{
    protected static ?string $navigationGroup = 'People & Organizations';
    protected static ?string $model = PersonProfession::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $label = 'Relación Persona-Profesión';
    protected static ?string $pluralLabel = 'Relaciones Persona-Profesión';
    protected static ?string $navigationLabel = 'Profesiones de Personas';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Relación')
                    ->schema([
                        Forms\Components\Select::make('person_id')
                            ->label('Persona')
                            ->relationship('person', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('profession_id')
                            ->label('Profesión')
                            ->relationship('profession', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Período de Actividad')
                    ->schema([
                        Forms\Components\TextInput::make('start_year')
                            ->label('Año de Inicio')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 10)
                            ->placeholder('Ej: 2010'),
                        
                        Forms\Components\TextInput::make('end_year')
                            ->label('Año de Fin')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 10)
                            ->placeholder('Ej: 2020 (dejar vacío si es actual)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Estado y Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_primary')
                            ->label('Profesión Principal')
                            ->helperText('Marcar si es la profesión principal de la persona'),
                        
                        Forms\Components\Toggle::make('is_current')
                            ->label('Actualmente Activo')
                            ->helperText('Marcar si la persona sigue ejerciendo esta profesión'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->placeholder('Información adicional sobre la profesión')
                            ->rows(3),
                    ])
                    ->columns(2),
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
                
                Tables\Columns\TextColumn::make('person.name')
                    ->label('Persona')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.people.edit', $record->person_id))
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('profession.name')
                    ->label('Profesión')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('period')
                    ->label('Período')
                    ->getStateUsing(function ($record) {
                        if ($record->start_year && $record->end_year) {
                            return "{$record->start_year} - {$record->end_year}";
                        } elseif ($record->start_year && !$record->end_year) {
                            return "{$record->start_year} - Presente";
                        } elseif (!$record->start_year && $record->end_year) {
                            return "? - {$record->end_year}";
                        } else {
                            return "Sin fechas";
                        }
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('start_year', $direction);
                    }),
                
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Principal')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                Tables\Columns\IconColumn::make('is_current')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->notes ?: 'Sin notas';
                    }),
                
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
                Tables\Filters\SelectFilter::make('person')
                    ->label('Persona')
                    ->relationship('person', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('profession')
                    ->label('Profesión')
                    ->relationship('profession', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label('Profesión Principal')
                    ->placeholder('Todas')
                    ->trueLabel('Solo principales')
                    ->falseLabel('Solo secundarias'),
                
                Tables\Filters\TernaryFilter::make('is_current')
                    ->label('Estado Actual')
                    ->placeholder('Todas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
                
                Tables\Filters\Filter::make('period')
                    ->label('Período de Actividad')
                    ->form([
                        Forms\Components\TextInput::make('start_year_from')
                            ->label('Desde año')
                            ->numeric()
                            ->placeholder('Ej: 2000'),
                        Forms\Components\TextInput::make('start_year_to')
                            ->label('Hasta año')
                            ->numeric()
                            ->placeholder('Ej: 2020'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_year_from'],
                                fn (Builder $query, $startYear): Builder => $query->where('start_year', '>=', $startYear),
                            )
                            ->when(
                                $data['start_year_to'],
                                fn (Builder $query, $endYear): Builder => $query->where('start_year', '<=', $endYear),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_person')
                    ->label('Ver Persona')
                    ->icon('heroicon-o-user')
                    ->url(fn ($record) => route('filament.admin.resources.people.edit', $record->person_id))
                    ->openUrlInNewTab()
                    ->color('info'),
                Tables\Actions\Action::make('toggle_primary')
                    ->label('Cambiar Principal')
                    ->icon('heroicon-o-star')
                    ->action(function ($record) {
                        // Desmarcar todas las profesiones principales de esta persona
                        PersonProfession::where('person_id', $record->person_id)
                            ->where('is_primary', true)
                            ->update(['is_primary' => false]);
                        
                        // Marcar esta como principal
                        $record->update(['is_primary' => true]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Profesión marcada como principal')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cambiar Profesión Principal')
                    ->modalDescription('¿Estás seguro de que quieres marcar esta profesión como principal? Se desmarcarán las demás.')
                    ->color('warning')
                    ->visible(fn ($record) => !$record->is_primary),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('mark_as_primary')
                    ->label('Marcar como Principal')
                    ->icon('heroicon-o-star')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            // Desmarcar todas las profesiones principales de esta persona
                            PersonProfession::where('person_id', $record->person_id)
                                ->where('is_primary', true)
                                ->update(['is_primary' => false]);
                            
                            // Marcar esta como principal
                            $record->update(['is_primary' => true]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Profesiones marcadas como principales')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Marcar como Principal')
                    ->modalDescription('¿Estás seguro de que quieres marcar estas profesiones como principales? Se desmarcarán las demás de cada persona.')
                    ->color('warning'),
                Tables\Actions\BulkAction::make('toggle_current_status')
                    ->label('Cambiar Estado Actual')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update(['is_current' => !$record->is_current]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Estado actual actualizado')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cambiar Estado Actual')
                    ->modalDescription('¿Estás seguro de que quieres cambiar el estado actual de estas profesiones?')
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['person', 'profession']));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['person', 'profession']);
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
            'index' => Pages\ListPersonProfessions::route('/'),
            'create' => Pages\CreatePersonProfession::route('/create'),
            'edit' => Pages\EditPersonProfession::route('/{record}/edit'),
        ];
    }
}
