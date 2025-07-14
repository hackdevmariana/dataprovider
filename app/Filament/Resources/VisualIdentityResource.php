<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisualIdentityResource\Pages;
use App\Filament\Resources\VisualIdentityResource\RelationManagers;
use App\Models\VisualIdentity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisualIdentityResource extends Resource
{
    protected static ?string $model = VisualIdentity::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Design';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Textarea::make('description')->rows(2)->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('description')->limit(50),
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
            'index' => Pages\ListVisualIdentities::route('/'),
            'create' => Pages\CreateVisualIdentity::route('/create'),
            'edit' => Pages\EditVisualIdentity::route('/{record}/edit'),
        ];
    }
}
