<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColorResource\Pages;
use App\Filament\Resources\ColorResource\RelationManagers;
use App\Models\Color;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ColorResource extends Resource
{
    protected static ?string $model = Color::class;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Content & Media';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('hex_code')->required(),
            Forms\Components\TextInput::make('rgb_code')->nullable(),
            Forms\Components\TextInput::make('hsl_code')->nullable(),
            Forms\Components\Toggle::make('is_primary')->label('Primary color'),
            Forms\Components\Textarea::make('description')->rows(2)->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('slug'),
            Tables\Columns\TextColumn::make('hex_code'),
            Tables\Columns\IconColumn::make('is_primary')->boolean(),
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
            'index' => Pages\ListColors::route('/'),
            'create' => Pages\CreateColor::route('/create'),
            'edit' => Pages\EditColor::route('/{record}/edit'),
        ];
    }
}
