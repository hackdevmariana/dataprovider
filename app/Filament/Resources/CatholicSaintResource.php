<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatholicSaintResource\Pages;
use App\Filament\Resources\CatholicSaintResource\RelationManagers;
use App\Models\CatholicSaint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CatholicSaintResource extends Resource
{
    protected static ?string $model = CatholicSaint::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Events & Calendar';
    protected static ?string $label = 'Santo Católico';
    protected static ?string $pluralLabel = 'Santos Católicos';
    protected static ?string $navigationLabel = 'Santoral';
    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Básica')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre del Santo')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ej: San Francisco de Asís')
                        ->helperText('Nombre completo del santo'),
                    
                    Forms\Components\TextInput::make('canonical_name')
                        ->label('Nombre Canónico')
                        ->maxLength(255)
                        ->placeholder('Ej: Sanctus Franciscus Assisiensis')
                        ->helperText('Nombre en latín o forma canónica'),
                    
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->placeholder('san-francisco-de-asis')
                        ->helperText('URL amigable del santo'),
                    
                    Forms\Components\Textarea::make('description')
                        ->label('Descripción Breve')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Descripción corta del santo')
                        ->helperText('Resumen de la vida y obra del santo'),
                    
                    Forms\Components\Textarea::make('biography')
                        ->label('Biografía Completa')
                        ->rows(8)
                        ->placeholder('Biografía detallada del santo')
                        ->helperText('Historia completa de la vida del santo'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Fechas Importantes')
                ->schema([
                    Forms\Components\DatePicker::make('birth_date')
                        ->label('Fecha de Nacimiento')
                        ->placeholder('Cuándo nació el santo'),
                    
                    Forms\Components\DatePicker::make('death_date')
                        ->label('Fecha de Muerte/Tránsito')
                        ->placeholder('Cuándo falleció el santo'),
                    
                    Forms\Components\DatePicker::make('canonization_date')
                        ->label('Fecha de Canonización')
                        ->placeholder('Cuándo fue canonizado'),
                    
                    Forms\Components\DatePicker::make('feast_date')
                        ->label('Fecha de Celebración')
                        ->required()
                        ->placeholder('Fecha principal de celebración litúrgica'),
                    
                    Forms\Components\DatePicker::make('feast_date_optional')
                        ->label('Fecha Alternativa')
                        ->placeholder('Fecha secundaria de celebración'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Clasificación Litúrgica')
                ->schema([
                    Forms\Components\Select::make('category')
                        ->label('Categoría')
                        ->options([
                            'martyr' => 'Mártir',
                            'confessor' => 'Confesor',
                            'virgin' => 'Virgen',
                            'virgin_martyr' => 'Virgen y Mártir',
                            'bishop' => 'Obispo',
                            'pope' => 'Papa',
                            'religious' => 'Religioso/a',
                            'lay_person' => 'Laico/a',
                            'founder' => 'Fundador/a',
                            'doctor' => 'Doctor de la Iglesia',
                            'apostle' => 'Apóstol',
                            'evangelist' => 'Evangelista',
                            'prophet' => 'Profeta',
                            'patriarch' => 'Patriarca',
                            'other' => 'Otros',
                        ])
                        ->default('other')
                        ->required()
                        ->searchable()
                        ->placeholder('Seleccionar categoría'),
                    
                    Forms\Components\Select::make('feast_type')
                        ->label('Tipo de Celebración')
                        ->options([
                            'solemnity' => 'Solemnidad',
                            'feast' => 'Fiesta',
                            'memorial' => 'Memoria',
                            'optional_memorial' => 'Memoria Opcional',
                            'commemoration' => 'Conmemoración',
                        ])
                        ->default('memorial')
                        ->required()
                        ->searchable()
                        ->placeholder('Seleccionar tipo'),
                    
                    Forms\Components\Select::make('liturgical_color')
                        ->label('Color Litúrgico')
                        ->options([
                            'white' => 'Blanco',
                            'red' => 'Rojo',
                            'green' => 'Verde',
                            'purple' => 'Morado',
                            'pink' => 'Rosa',
                            'gold' => 'Dorado',
                            'black' => 'Negro',
                        ])
                        ->searchable()
                        ->placeholder('Seleccionar color'),
                    
                    Forms\Components\TextInput::make('liturgical_rank')
                        ->label('Rango Litúrgico')
                        ->maxLength(255)
                        ->placeholder('Ej: Santo Mayor, Santo Menor'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Patronazgos y Especialidades')
                ->schema([
                    Forms\Components\Textarea::make('patron_of')
                        ->label('Patrono de')
                        ->rows(3)
                        ->placeholder('Ej: Agricultores, Madrid, Iglesia Universal')
                        ->helperText('De qué o quién es patrono el santo'),
                    
                    Forms\Components\Toggle::make('is_patron')
                        ->label('Es Patrono')
                        ->helperText('Marcar si es patrono de algún lugar o causa'),
                    
                    Forms\Components\KeyValue::make('patronages')
                        ->label('Patronazgos Específicos')
                        ->keyLabel('Tipo')
                        ->valueLabel('Descripción')
                        ->addActionLabel('Agregar Patronazgo')
                        ->helperText('Lista detallada de patronazgos'),
                    
                    Forms\Components\Textarea::make('specialties')
                        ->label('Especialidades')
                        ->rows(3)
                        ->placeholder('Ej: Pobreza, ecología, paz, amor a la creación')
                        ->helperText('Virtudes o especialidades del santo'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Información Geográfica')
                ->schema([
                    Forms\Components\Select::make('birth_place_id')
                        ->label('Lugar de Nacimiento')
                        ->relationship('birthPlace', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar municipio'),
                    
                    Forms\Components\Select::make('death_place_id')
                        ->label('Lugar de Muerte')
                        ->relationship('deathPlace', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar municipio'),
                    
                    Forms\Components\Select::make('municipality_id')
                        ->label('Municipio Patrono')
                        ->relationship('municipality', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar municipio donde es patrono'),
                    
                    Forms\Components\TextInput::make('region')
                        ->label('Región')
                        ->maxLength(255)
                        ->placeholder('Ej: Madrid, Cataluña, Castilla y León'),
                    
                    Forms\Components\TextInput::make('country')
                        ->label('País')
                        ->maxLength(255)
                        ->placeholder('Ej: España, Italia, Polonia'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Recursos Litúrgicos')
                ->schema([
                    Forms\Components\Textarea::make('prayers')
                        ->label('Oraciones')
                        ->rows(4)
                        ->placeholder('Oraciones asociadas al santo')
                        ->helperText('Oraciones específicas del santo'),
                    
                    Forms\Components\Textarea::make('hymns')
                        ->label('Himnos')
                        ->rows(4)
                        ->placeholder('Himnos asociados al santo')
                        ->helperText('Himnos en honor al santo'),
                    
                    Forms\Components\KeyValue::make('attributes')
                        ->label('Atributos y Símbolos')
                        ->keyLabel('Atributo')
                        ->valueLabel('Descripción')
                        ->addActionLabel('Agregar Atributo')
                        ->helperText('Símbolos y atributos visuales del santo'),
                ])
                ->columns(1),

            Forms\Components\Section::make('Estado y Configuración')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Santo Activo')
                        ->helperText('Si está activo en el calendario litúrgico'),
                    
                    Forms\Components\Toggle::make('is_universal')
                        ->label('Celebrado Universalmente')
                        ->helperText('Si se celebra en toda la Iglesia'),
                    
                    Forms\Components\Toggle::make('is_local')
                        ->label('Solo Local')
                        ->helperText('Si solo se celebra localmente'),
                    
                    Forms\Components\TextInput::make('popularity_score')
                        ->label('Puntuación de Popularidad')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(10)
                        ->default(0)
                        ->helperText('Puntuación del 0 al 10'),
                    
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas Adicionales')
                        ->rows(3)
                        ->placeholder('Información adicional o notas especiales'),
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed(),
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
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->tooltip(function ($record) {
                        return $record->name;
                    }),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'martyr' => 'danger',
                        'confessor' => 'success',
                        'virgin' => 'warning',
                        'virgin_martyr' => 'danger',
                        'bishop' => 'info',
                        'pope' => 'primary',
                        'religious' => 'success',
                        'lay_person' => 'gray',
                        'founder' => 'warning',
                        'doctor' => 'primary',
                        'apostle' => 'success',
                        'evangelist' => 'info',
                        'prophet' => 'warning',
                        'patriarch' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'martyr' => 'Mártir',
                        'confessor' => 'Confesor',
                        'virgin' => 'Virgen',
                        'virgin_martyr' => 'Virgen y Mártir',
                        'bishop' => 'Obispo',
                        'pope' => 'Papa',
                        'religious' => 'Religioso/a',
                        'lay_person' => 'Laico/a',
                        'founder' => 'Fundador/a',
                        'doctor' => 'Doctor',
                        'apostle' => 'Apóstol',
                        'evangelist' => 'Evangelista',
                        'prophet' => 'Profeta',
                        'patriarch' => 'Patriarca',
                        default => 'Otros',
                    }),
                
                Tables\Columns\TextColumn::make('feast_date')
                    ->label('Fecha de Celebración')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('feast_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'solemnity' => 'primary',
                        'feast' => 'success',
                        'memorial' => 'info',
                        'optional_memorial' => 'warning',
                        'commemoration' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'solemnity' => 'Solemnidad',
                        'feast' => 'Fiesta',
                        'memorial' => 'Memoria',
                        'optional_memorial' => 'Memoria Opcional',
                        'commemoration' => 'Conmemoración',
                        default => 'Memoria',
                    }),
                
                Tables\Columns\TextColumn::make('liturgical_color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'white' => 'white',
                        'red' => 'danger',
                        'green' => 'success',
                        'purple' => 'warning',
                        'pink' => 'warning',
                        'gold' => 'warning',
                        'black' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'white' => 'Blanco',
                        'red' => 'Rojo',
                        'green' => 'Verde',
                        'purple' => 'Morado',
                        'pink' => 'Rosa',
                        'gold' => 'Dorado',
                        'black' => 'Negro',
                        default => 'No especificado',
                    }),
                
                Tables\Columns\TextColumn::make('municipality.name')
                    ->label('Municipio Patrono')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('Universal'),
                
                Tables\Columns\IconColumn::make('is_patron')
                    ->label('Es Patrono')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('patron_of')
                    ->label('Patrono de')
                    ->limit(40)
                    ->searchable()
                    ->placeholder('No especificado'),
                
                Tables\Columns\TextColumn::make('popularity_score')
                    ->label('Popularidad')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        ($record->popularity_score ?? 0) >= 8 => 'success',
                        ($record->popularity_score ?? 0) >= 5 => 'info',
                        ($record->popularity_score ?? 0) >= 3 => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('is_universal')
                    ->label('Universal')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-home')
                    ->trueColor('success')
                    ->falseColor('info'),
                
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
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'martyr' => 'Mártir',
                        'confessor' => 'Confesor',
                        'virgin' => 'Virgen',
                        'virgin_martyr' => 'Virgen y Mártir',
                        'bishop' => 'Obispo',
                        'pope' => 'Papa',
                        'religious' => 'Religioso/a',
                        'lay_person' => 'Laico/a',
                        'founder' => 'Fundador/a',
                        'doctor' => 'Doctor de la Iglesia',
                        'apostle' => 'Apóstol',
                        'evangelist' => 'Evangelista',
                        'prophet' => 'Profeta',
                        'patriarch' => 'Patriarca',
                        'other' => 'Otros',
                    ])
                    ->multiple()
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('feast_type')
                    ->label('Tipo de Celebración')
                    ->options([
                        'solemnity' => 'Solemnidad',
                        'feast' => 'Fiesta',
                        'memorial' => 'Memoria',
                        'optional_memorial' => 'Memoria Opcional',
                        'commemoration' => 'Conmemoración',
                    ])
                    ->multiple()
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('liturgical_color')
                    ->label('Color Litúrgico')
                    ->options([
                        'white' => 'Blanco',
                        'red' => 'Rojo',
                        'green' => 'Verde',
                        'purple' => 'Morado',
                        'pink' => 'Rosa',
                        'gold' => 'Dorado',
                        'black' => 'Negro',
                    ])
                    ->multiple()
                    ->searchable(),
                
                Tables\Filters\TernaryFilter::make('is_patron')
                    ->label('Es Patrono')
                    ->placeholder('Todos los santos')
                    ->trueLabel('Solo patronos')
                    ->falseLabel('No patronos'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Santo Activo')
                    ->placeholder('Todos los santos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
                
                Tables\Filters\TernaryFilter::make('is_universal')
                    ->label('Celebrado Universalmente')
                    ->placeholder('Todos los santos')
                    ->trueLabel('Solo universales')
                    ->falseLabel('Solo locales'),
                
                Tables\Filters\SelectFilter::make('municipality')
                    ->label('Municipio Patrono')
                    ->relationship('municipality', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los municipios'),
                
                Tables\Filters\Filter::make('popularity_range')
                    ->label('Rango de Popularidad')
                    ->form([
                        Forms\Components\TextInput::make('min_score')
                            ->label('Puntuación mínima')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10),
                        Forms\Components\TextInput::make('max_score')
                            ->label('Puntuación máxima')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_score'],
                                fn (Builder $query, $score): Builder => $query->where('popularity_score', '>=', $score),
                            )
                            ->when(
                                $data['max_score'],
                                fn (Builder $query, $score): Builder => $query->where('popularity_score', '<=', $score),
                            );
                    }),
                
                Tables\Filters\Filter::make('feast_date_range')
                    ->label('Rango de Fechas de Celebración')
                    ->form([
                        Forms\Components\DatePicker::make('feast_date_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('feast_date_to')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['feast_date_from'],
                                fn (Builder $query, $date): Builder => $query->where('feast_date', '>=', $date),
                            )
                            ->when(
                                $data['feast_date_to'],
                                fn (Builder $query, $date): Builder => $query->where('feast_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver Santo')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\EditAction::make()
                    ->label('Editar Santo')
                    ->icon('heroicon-o-pencil'),
                
                Tables\Actions\Action::make('view_municipality')
                    ->label('Ver Municipio')
                    ->icon('heroicon-o-map-pin')
                    ->url(fn ($record) => $record->municipality_id ? route('filament.admin.resources.municipalities.edit', $record->municipality_id) : null)
                    ->openUrlInNewTab()
                    ->color('info')
                    ->visible(fn ($record) => $record->municipality_id !== null),
                
                Tables\Actions\Action::make('today_saint')
                    ->label('¿Santo del Día?')
                    ->icon('heroicon-o-calendar')
                    ->color(fn ($record) => $record->is_today_saint ? 'success' : 'gray')
                    ->badge(fn ($record) => $record->is_today_saint ? 'SÍ' : 'NO')
                    ->badgeColor(fn ($record) => $record->is_today_saint ? 'success' : 'gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('mark_as_active')
                    ->label('Marcar como Activos')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update(['is_active' => true]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Santos actualizados')
                            ->body('Los santos han sido marcados como activos.')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\BulkAction::make('mark_as_universal')
                    ->label('Marcar como Universales')
                    ->icon('heroicon-o-globe-alt')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update(['is_universal' => true]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Santos actualizados')
                            ->body('Los santos han sido marcados como universales.')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\BulkAction::make('calculate_popularity')
                    ->label('Calcular Popularidad')
                    ->icon('heroicon-o-star')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            // Lógica simple de cálculo de popularidad
                            $score = 0;
                            if ($record->is_patron) $score += 2;
                            if ($record->is_universal) $score += 3;
                            if ($record->is_active) $score += 1;
                            if ($record->feast_type === 'solemnity') $score += 2;
                            if ($record->category === 'apostle' || $record->category === 'doctor') $score += 1;
                            
                            $record->update(['popularity_score' => min($score, 10)]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Popularidad calculada')
                            ->body('La popularidad de los santos ha sido recalculada.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('feast_date', 'asc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['municipality', 'birthPlace', 'deathPlace']));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['municipality', 'birthPlace', 'deathPlace']);
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
            'index' => Pages\ListCatholicSaints::route('/'),
            'create' => Pages\CreateCatholicSaint::route('/create'),
            'edit' => Pages\EditCatholicSaint::route('/{record}/edit'),
        ];
    }
}
