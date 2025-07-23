<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyTypeResource\Pages;
use App\Filament\Resources\CompanyTypeResource\RelationManagers;
use App\Models\CompanyType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyTypeResource extends Resource
{
    protected static ?string $model = CompanyType::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Sociedades';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->sortable(),
        ])->filters([])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
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
            'index' => Pages\ListCompanyTypes::route('/'),
            'create' => Pages\CreateCompanyType::route('/create'),
            'edit' => Pages\EditCompanyType::route('/{record}/edit'),
        ];
    }
}
