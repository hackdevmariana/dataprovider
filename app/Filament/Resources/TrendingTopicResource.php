<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrendingTopicResource\Pages;
use App\Filament\Resources\TrendingTopicResource\RelationManagers;
use App\Models\TrendingTopic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrendingTopicResource extends Resource
{
    protected static ?string $model = TrendingTopic::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('topic')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('trending_score')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('mentions_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('growth_rate')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('geographic_spread')
                    ->required()
                    ->maxLength(255)
                    ->default('local'),
                Forms\Components\TextInput::make('category')
                    ->maxLength(255),
                Forms\Components\TextInput::make('related_keywords'),
                Forms\Components\TextInput::make('geographic_data'),
                Forms\Components\DateTimePicker::make('peak_time'),
                Forms\Components\TextInput::make('peak_score')
                    ->numeric(),
                Forms\Components\TextInput::make('trend_analysis'),
                Forms\Components\Toggle::make('is_breaking')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('topic')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trending_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mentions_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('growth_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('geographic_spread')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peak_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('peak_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_breaking')
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
            'index' => Pages\ListTrendingTopics::route('/'),
            'create' => Pages\CreateTrendingTopic::route('/create'),
            'edit' => Pages\EditTrendingTopic::route('/{record}/edit'),
        ];
    }
}
