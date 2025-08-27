<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('text')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('author')
                    ->maxLength(255),
                Forms\Components\TextInput::make('source')
                    ->maxLength(255),
                Forms\Components\TextInput::make('language')
                    ->required()
                    ->maxLength(255)
                    ->default('es'),
                Forms\Components\TextInput::make('category')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tags'),
                Forms\Components\TextInput::make('mood')
                    ->maxLength(255),
                Forms\Components\TextInput::make('difficulty_level')
                    ->required()
                    ->maxLength(255)
                    ->default('easy'),
                Forms\Components\TextInput::make('word_count')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('character_count')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('popularity_score')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('usage_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('translations'),
                Forms\Components\Toggle::make('is_verified')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->searchable(),
                Tables\Columns\TextColumn::make('language')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mood')
                    ->searchable(),
                Tables\Columns\TextColumn::make('difficulty_level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('word_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('character_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('popularity_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean(),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
