<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonWorkResource\Pages;
use App\Filament\Resources\PersonWorkResource\RelationManagers;
use App\Models\PersonWork;
use App\Models\Person;
use App\Models\Work;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonWorkResource extends Resource
{
    protected static ?string $navigationGroup = 'Organizaciones y Empresas';
    protected static ?string $model = PersonWork::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Personas y Obras';
    protected static ?string $pluralModelLabel = 'Relaciones Persona-Obra';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('person_id')
                    ->label('Persona')
                    ->relationship('person', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                    ]),

                Forms\Components\Select::make('work_id')
                    ->label('Obra')
                    ->relationship('work', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'book' => 'Libro',
                                'movie' => 'Película',
                                'tv_show' => 'Serie de TV',
                                'theatre_play' => 'Obra de Teatro',
                                'article' => 'Artículo',
                            ])
                            ->required(),
                    ]),

                Forms\Components\TextInput::make('role')
                    ->label('Rol')
                    ->required()
                    ->maxLength(60)
                    ->placeholder('ej: actor, director, escritor, compositor'),

                Forms\Components\TextInput::make('character_name')
                    ->label('Nombre del Personaje')
                    ->maxLength(120)
                    ->placeholder('ej: Don Quijote, Tony Stark'),

                Forms\Components\TextInput::make('credited_as')
                    ->label('Acreditado como')
                    ->maxLength(120)
                    ->placeholder('Nombre con el que aparece acreditado'),

                Forms\Components\TextInput::make('billing_order')
                    ->label('Orden de Facturación')
                    ->numeric()
                    ->placeholder('1 = protagonista, 2 = coprotagonista'),

                Forms\Components\TextInput::make('contribution_pct')
                    ->label('Porcentaje de Contribución')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('%')
                    ->placeholder('33.33'),

                Forms\Components\Toggle::make('is_primary')
                    ->label('Rol Principal')
                    ->default(false),

                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->placeholder('Notas adicionales sobre la relación'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('person.name')
                    ->label('Persona')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('work.title')
                    ->label('Obra')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('work.type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'book' => 'Libro',
                        'movie' => 'Película',
                        'tv_show' => 'Serie TV',
                        'theatre_play' => 'Teatro',
                        'article' => 'Artículo',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'book' => 'success',
                        'movie' => 'info',
                        'tv_show' => 'warning',
                        'theatre_play' => 'primary',
                        'article' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('character_name')
                    ->label('Personaje')
                    ->searchable()
                    ->limit(20)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('billing_order')
                    ->label('Orden')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('contribution_pct')
                    ->label('Contribución')
                    ->suffix('%')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Principal')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'actor' => 'Actor',
                        'actriz' => 'Actriz',
                        'director' => 'Director',
                        'escritor' => 'Escritor',
                        'compositor' => 'Compositor',
                        'pintor' => 'Pintor',
                        'productor' => 'Productor',
                        'guionista' => 'Guionista',
                        'músico' => 'Músico',
                        'científico' => 'Científico',
                        'dramaturgo' => 'Dramaturgo',
                        'poeta' => 'Poeta',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('work.type')
                    ->label('Tipo de Obra')
                    ->relationship('work', 'type')
                    ->options([
                        'book' => 'Libro',
                        'movie' => 'Película',
                        'tv_show' => 'Serie de TV',
                        'theatre_play' => 'Obra de Teatro',
                        'article' => 'Artículo',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label('Rol Principal')
                    ->placeholder('Todos')
                    ->trueLabel('Solo principales')
                    ->falseLabel('Solo secundarios'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPersonWorks::route('/'),
            'create' => Pages\CreatePersonWork::route('/create'),
            'edit' => Pages\EditPersonWork::route('/{record}/edit'),
        ];
    }
}
