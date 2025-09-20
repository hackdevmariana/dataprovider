<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FontResource\Pages;
use App\Filament\Resources\FontResource\RelationManagers;
use App\Models\Font;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FontResource extends Resource
{
    protected static ?string $model = Font::class;

    protected static ?string $navigationIcon = 'heroicon-m-at-symbol';
    protected static ?string $navigationGroup = 'Contenido y Medios';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('family')->nullable(),
            Forms\Components\TextInput::make('style')->required(),
            Forms\Components\TextInput::make('weight')->numeric()->nullable(),
            Forms\Components\TextInput::make('license')->nullable(),
            Forms\Components\TextInput::make('source_url')->url()->required(),
            Forms\Components\Toggle::make('is_default')->label('Default font'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('style'),
            Tables\Columns\IconColumn::make('is_default')->boolean(),
        ])
            ->defaultSort('name')
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
            'index' => Pages\ListFonts::route('/'),
            'create' => Pages\CreateFont::route('/create'),
            'edit' => Pages\EditFont::route('/{record}/edit'),
        ];
    }
}
