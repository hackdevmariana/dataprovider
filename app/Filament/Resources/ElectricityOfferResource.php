<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ElectricityOfferResource\Pages;
use App\Filament\Resources\ElectricityOfferResource\RelationManagers;
use App\Models\ElectricityOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ElectricityOfferResource extends Resource
{
    protected static ?string $model = ElectricityOffer::class;
    protected static ?string $navigationGroup = 'Mercado energético';

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('company_id')
                ->relationship('company', 'name')
                ->required(),

            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
            Forms\Components\TextInput::make('tariff_code')->nullable()->maxLength(50),
            Forms\Components\Textarea::make('description')->nullable()->maxLength(1000),
            Forms\Components\Textarea::make('conditions')->nullable()->maxLength(1000),
            Forms\Components\TextInput::make('price')->numeric()->nullable(),
            Forms\Components\TextInput::make('duration_months')->numeric()->nullable(),
            Forms\Components\TextInput::make('url')->url()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('company.name')->label('Company')->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('tariff_code')->sortable(),
            Tables\Columns\TextColumn::make('price')->sortable(),
            Tables\Columns\TextColumn::make('duration_months')->sortable(),
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
            'index' => Pages\ListElectricityOffers::route('/'),
            'create' => Pages\CreateElectricityOffer::route('/create'),
            'edit' => Pages\EditElectricityOffer::route('/{record}/edit'),
        ];
    }
}
