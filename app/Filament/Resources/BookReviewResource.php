<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookReviewResource\Pages;
use App\Filament\Resources\BookReviewResource\RelationManagers;
use App\Models\BookReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookReviewResource extends Resource
{
    protected static ?string $model = BookReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('rating')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('review_text')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_verified_purchase')
                    ->required(),
                Forms\Components\Toggle::make('is_helpful')
                    ->required(),
                Forms\Components\TextInput::make('helpful_votes')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('not_helpful_votes')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('pros'),
                Forms\Components\TextInput::make('cons'),
                Forms\Components\Toggle::make('is_public')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified_purchase')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_helpful')
                    ->boolean(),
                Tables\Columns\TextColumn::make('helpful_votes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('not_helpful_votes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
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
            'index' => Pages\ListBookReviews::route('/'),
            'create' => Pages\CreateBookReview::route('/create'),
            'edit' => Pages\EditBookReview::route('/{record}/edit'),
        ];
    }
}
