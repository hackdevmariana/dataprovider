<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageResource\Pages;
use App\Filament\Resources\ImageResource\RelationManagers;
use App\Models\Image;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImageResource extends Resource
{
    protected static ?string $model = Image::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'General & Stats';
    protected static ?string $label = 'Image';
    protected static ?string $pluralLabel = 'Images';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('url')
                    ->label('Image URL')
                    ->required()
                    ->url(),

                Forms\Components\TextInput::make('alt_text')
                    ->label('Alt text')
                    ->maxLength(255),

                Forms\Components\TextInput::make('source')
                    ->label('Source')
                    ->maxLength(255),

                Forms\Components\TextInput::make('width')
                    ->label('Width (px)')
                    ->numeric()
                    ->minValue(1),

                Forms\Components\TextInput::make('height')
                    ->label('Height (px)')
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('url')
                    ->label('Preview')
                    ->size(50),

                Tables\Columns\TextColumn::make('alt_text')->label('Alt Text')->limit(20),
                Tables\Columns\TextColumn::make('source')->label('Source')->limit(20),
                Tables\Columns\TextColumn::make('width')->label('W'),
                Tables\Columns\TextColumn::make('height')->label('H'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImage::route('/create'),
            'edit' => Pages\EditImage::route('/{record}/edit'),
        ];
    }
}
