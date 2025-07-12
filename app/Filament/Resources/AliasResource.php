<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AliasResource\Pages;
use App\Filament\Resources\AliasResource\RelationManagers;
use App\Models\Alias;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AliasResource extends Resource
{
    protected static ?string $model = Alias::class;

    protected static ?string $navigationGroup = 'People';
    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Select::make('type')->options([
                    'nickname' => 'Nickname',
                    'stage_name' => 'Stage Name',
                    'birth_name' => 'Birth Name',
                ]),
                Forms\Components\Toggle::make('is_primary'),
                Forms\Components\Select::make('person_id')->relationship('person', 'name')->searchable()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\IconColumn::make('is_primary')->boolean(),
                Tables\Columns\TextColumn::make('person.name')->label('Person'),
            ])
            ->filters([])
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
            'index' => Pages\ListAliases::route('/'),
            'create' => Pages\CreateAlias::route('/create'),
            'edit' => Pages\EditAlias::route('/{record}/edit'),
        ];
    }
}
