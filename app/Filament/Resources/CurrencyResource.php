<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Filament\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationGroup = 'Configuración';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Moneda';
    
    protected static ?string $pluralModelLabel = 'Monedas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('iso_code')
                            ->label('Código ISO')
                            ->required()
                            ->unique(Currency::class, 'iso_code', ignoreRecord: true)
                            ->maxLength(3)
                            ->placeholder('EUR, USD, BTC...'),
                        Forms\Components\TextInput::make('symbol')
                            ->label('Símbolo')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('€, $, ₿...'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Euro, Dólar Estadounidense...'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_crypto')
                            ->label('Es Criptomoneda')
                            ->default(false),
                        Forms\Components\Toggle::make('is_supported_by_app')
                            ->label('Soportada por la App')
                            ->default(true),
                        Forms\Components\Toggle::make('exchangeable_in_calculator')
                            ->label('Intercambiable en Calculadora')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('iso_code')
                    ->label('Código ISO')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('symbol')
                    ->label('Símbolo')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_crypto')
                    ->label('Crypto')
                    ->boolean()
                    ->trueIcon('heroicon-o-cpu-chip')
                    ->falseIcon('heroicon-o-banknotes'),
                Tables\Columns\IconColumn::make('is_supported_by_app')
                    ->label('Soportada')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('exchangeable_in_calculator')
                    ->label('Intercambiable')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_crypto')
                    ->label('Tipo de moneda')
                    ->trueLabel('Solo criptomonedas')
                    ->falseLabel('Solo monedas tradicionales')
                    ->native(false),
                Tables\Filters\TernaryFilter::make('is_supported_by_app')
                    ->label('Soportada por la app')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('iso_code');
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
