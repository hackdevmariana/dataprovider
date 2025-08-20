<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoofMarketplaceResource\Pages;
use App\Filament\Resources\RoofMarketplaceResource\RelationManagers;
use App\Models\RoofMarketplace;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoofMarketplaceResource extends Resource
{
    protected static ?string $model = RoofMarketplace::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListRoofMarketplaces::route('/'),
            'create' => Pages\CreateRoofMarketplace::route('/create'),
            'edit' => Pages\EditRoofMarketplace::route('/{record}/edit'),
        ];
    }
}
