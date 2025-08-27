<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferComparisonResource\Pages;
use App\Filament\Resources\OfferComparisonResource\RelationManagers;
use App\Models\OfferComparison;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferComparisonResource extends Resource
{
    protected static ?string $model = OfferComparison::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('energy_type')
                    ->required()
                    ->maxLength(255)
                    ->default('electricity'),
                Forms\Components\TextInput::make('consumption_profile')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('offers_compared')
                    ->required(),
                Forms\Components\TextInput::make('best_offer_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('savings_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('savings_percentage')
                    ->numeric(),
                Forms\Components\TextInput::make('comparison_criteria'),
                Forms\Components\DateTimePicker::make('comparison_date')
                    ->required(),
                Forms\Components\Toggle::make('is_shared')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('energy_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('consumption_profile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('best_offer_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('savings_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('savings_percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comparison_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_shared')
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
            'index' => Pages\ListOfferComparisons::route('/'),
            'create' => Pages\CreateOfferComparison::route('/create'),
            'edit' => Pages\EditOfferComparison::route('/{record}/edit'),
        ];
    }
}
