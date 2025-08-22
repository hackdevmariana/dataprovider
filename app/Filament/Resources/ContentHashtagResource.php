<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentHashtagResource\Pages;
use App\Filament\Resources\ContentHashtagResource\RelationManagers;
use App\Models\ContentHashtag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContentHashtagResource extends Resource
{
    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $model = ContentHashtag::class;

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
            'index' => Pages\ListContentHashtags::route('/'),
            'create' => Pages\CreateContentHashtag::route('/create'),
            'edit' => Pages\EditContentHashtag::route('/{record}/edit'),
        ];
    }
}
