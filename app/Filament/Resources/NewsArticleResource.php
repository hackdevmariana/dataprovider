<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsArticleResource\Pages;
use App\Filament\Resources\NewsArticleResource\RelationManagers;
use App\Models\NewsArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{TextInput, Textarea, Toggle, DateTimePicker, Select, TagsInput};
use Filament\Tables\Columns\{TextColumn, BooleanColumn, BadgeColumn, DateTimeColumn};
use Filament\Tables\Columns\IconColumn;

class NewsArticleResource extends Resource
{
    protected static ?string $model = NewsArticle::class;

    protected static ?string $navigationIcon = 'heroicon-s-newspaper';
    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->maxLength(255),
            TextInput::make('summary')->maxLength(500),
            Textarea::make('content')->rows(8),
            TextInput::make('source_url')->url()->maxLength(255),

            DateTimePicker::make('published_at'),
            DateTimePicker::make('featured_start'),
            DateTimePicker::make('featured_end'),

            Select::make('media_outlet_id')->relationship('mediaOutlet', 'name')->searchable(),
            Select::make('author_id')->relationship('author', 'name')->searchable(),
            Select::make('municipality_id')->relationship('municipality', 'name')->searchable(),
            Select::make('language_id')->relationship('language', 'name')->searchable(),
            Select::make('image_id')->relationship('image', 'file_name')->searchable(),
            Select::make('tag_id')->relationship('tag', 'name')->searchable(),

            TagsInput::make('tags'),

            Toggle::make('is_outstanding'),
            Toggle::make('is_verified'),
            Toggle::make('is_scraped'),
            Toggle::make('is_translated'),

            TextInput::make('visibility')->default('public'),
            TextInput::make('views_count')->numeric()->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mediaOutlet.name')
                    ->label('Outlet')
                    ->sortable(),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),

                IconColumn::make('is_outstanding')
                    ->boolean()
                    ->label('Outstanding'),

                IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Verified'),

                IconColumn::make('is_scraped')
                    ->boolean()
                    ->label('Scraped'),

                TextColumn::make('views_count')
                    ->sortable(),
            ])

            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListNewsArticles::route('/'),
            'create' => Pages\CreateNewsArticle::route('/create'),
            'edit' => Pages\EditNewsArticle::route('/{record}/edit'),
        ];
    }
}
