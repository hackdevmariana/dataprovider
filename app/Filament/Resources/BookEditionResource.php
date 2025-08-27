<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookEditionResource\Pages;
use App\Filament\Resources\BookEditionResource\RelationManagers;
use App\Models\BookEdition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookEditionResource extends Resource
{
    protected static ?string $model = BookEdition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                Forms\Components\TextInput::make('edition_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('format')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('publisher')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('publication_date')
                    ->required(),
                Forms\Components\TextInput::make('isbn')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pages')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cover_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(255)
                    ->default('EUR'),
                Forms\Components\TextInput::make('special_features'),
                Forms\Components\TextInput::make('translator')
                    ->maxLength(255),
                Forms\Components\TextInput::make('illustrator')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_limited')
                    ->required(),
                Forms\Components\TextInput::make('print_run')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('edition_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('format')
                    ->searchable(),
                Tables\Columns\TextColumn::make('publisher')
                    ->searchable(),
                Tables\Columns\TextColumn::make('publication_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pages')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cover_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('translator')
                    ->searchable(),
                Tables\Columns\TextColumn::make('illustrator')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_limited')
                    ->boolean(),
                Tables\Columns\TextColumn::make('print_run')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBookEditions::route('/'),
            'create' => Pages\CreateBookEdition::route('/create'),
            'edit' => Pages\EditBookEdition::route('/{record}/edit'),
        ];
    }
}
