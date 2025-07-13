<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamilyMemberResource\Pages;
use App\Filament\Resources\FamilyMemberResource\RelationManagers;
use App\Models\FamilyMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FamilyMemberResource extends Resource
{
    protected static ?string $model = FamilyMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'People';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('person_id')
                    ->label('Person')
                    ->relationship('person', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('relative_id')
                    ->label('Relative')
                    ->relationship('relative', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('relationship_type')
                    ->label('Relationship Type')
                    ->required()
                    ->maxLength(40),

                Forms\Components\Toggle::make('is_biological')
                    ->label('Is Biological')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('person.name')->label('Person')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('relative.name')->label('Relative')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('relationship_type')->label('Relationship Type')->sortable()->searchable(),
                Tables\Columns\BooleanColumn::make('is_biological')->label('Biological'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListFamilyMembers::route('/'),
            'create' => Pages\CreateFamilyMember::route('/create'),
            'edit' => Pages\EditFamilyMember::route('/{record}/edit'),
        ];
    }
}
