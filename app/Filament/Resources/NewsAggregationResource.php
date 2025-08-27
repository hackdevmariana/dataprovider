<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsAggregationResource\Pages;
use App\Filament\Resources\NewsAggregationResource\RelationManagers;
use App\Models\NewsAggregation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NewsAggregationResource extends Resource
{
    protected static ?string $model = NewsAggregation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('source_id')
                    ->relationship('source', 'name')
                    ->required(),
                Forms\Components\TextInput::make('article_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('aggregated_at')
                    ->required(),
                Forms\Components\TextInput::make('processing_status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\Toggle::make('duplicate_check')
                    ->required(),
                Forms\Components\TextInput::make('quality_score')
                    ->numeric(),
                Forms\Components\TextInput::make('processing_metadata'),
                Forms\Components\DateTimePicker::make('processed_at'),
                Forms\Components\Textarea::make('processing_notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('source.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('article_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aggregated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processing_status')
                    ->searchable(),
                Tables\Columns\IconColumn::make('duplicate_check')
                    ->boolean(),
                Tables\Columns\TextColumn::make('quality_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListNewsAggregations::route('/'),
            'create' => Pages\CreateNewsAggregation::route('/create'),
            'edit' => Pages\EditNewsAggregation::route('/{record}/edit'),
        ];
    }
}
