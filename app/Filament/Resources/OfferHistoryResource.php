<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferHistoryResource\Pages;
use App\Filament\Resources\OfferHistoryResource\RelationManagers;
use App\Models\OfferHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferHistoryResource extends Resource
{
    protected static ?string $model = OfferHistory::class;
    protected static ?string $navigationGroup = 'Energía y Precios';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('company_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('offer_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('offer_details')
                    ->required(),
                Forms\Components\DateTimePicker::make('valid_from')
                    ->required(),
                Forms\Components\DateTimePicker::make('valid_until'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(255)
                    ->default('EUR'),
                Forms\Components\TextInput::make('unit')
                    ->required()
                    ->maxLength(255)
                    ->default('MWh'),
                Forms\Components\TextInput::make('terms_conditions'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('active'),
                Forms\Components\TextInput::make('restrictions'),
                Forms\Components\Toggle::make('is_featured')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('offer_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('valid_from')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListOfferHistories::route('/'),
            'create' => Pages\CreateOfferHistory::route('/create'),
            'edit' => Pages\EditOfferHistory::route('/{record}/edit'),
        ];
    }
}
