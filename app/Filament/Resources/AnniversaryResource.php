<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnniversaryResource\Pages;
use App\Filament\Resources\AnniversaryResource\RelationManagers;
use App\Models\Anniversary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnniversaryResource extends Resource
{
    protected static ?string $model = Anniversary::class;

    protected static ?string $navigationGroup = 'People';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('day')->required()->numeric()->minValue(1)->maxValue(31),
                        Forms\Components\TextInput::make('year')->numeric()->nullable(),
                    ]),
                Forms\Components\Textarea::make('description')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('day')->sortable(),
                Tables\Columns\TextColumn::make('year')->sortable()->default('-'),
                Tables\Columns\TextColumn::make('slug'),
            ])
            ->filters([]);
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
            'index' => Pages\ListAnniversaries::route('/'),
            'create' => Pages\CreateAnniversary::route('/create'),
            'edit' => Pages\EditAnniversary::route('/{record}/edit'),
        ];
    }
}
