<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AwardWinnerResource\Pages;
use App\Filament\Resources\AwardWinnerResource\RelationManagers;
use App\Models\AwardWinner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AwardWinnerResource extends Resource
{
    protected static ?string $model = AwardWinner::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Personas';



    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('person_id')
                ->relationship('person', 'name')
                ->searchable()
                ->required(),

            Forms\Components\Select::make('award_id')
                ->relationship('award', 'name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('year')
                ->numeric()
                ->minValue(1800)
                ->maxValue(now()->year + 1),

            Forms\Components\Select::make('classification')
                ->options([
                    'winner' => 'Winner',
                    'finalist' => 'Finalist',
                    'other' => 'Other',
                ])
                ->required(),

            Forms\Components\Select::make('work_id')
                ->relationship('work', 'title')
                ->searchable()
                ->label('Work (optional)')
                ->nullable(),

            Forms\Components\Select::make('municipality_id')
                ->relationship('municipality', 'name')
                ->searchable()
                ->nullable()
                ->label('Municipality'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('person.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('award.name')->searchable(),
                Tables\Columns\TextColumn::make('classification'),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('work.title')->label('Work'),
                Tables\Columns\TextColumn::make('municipality.name')->label('Municipality'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListAwardWinners::route('/'),
            'create' => Pages\CreateAwardWinner::route('/create'),
            'edit' => Pages\EditAwardWinner::route('/{record}/edit'),
        ];
    }
}
