<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyAnniversaryResource\Pages;
use App\Filament\Resources\DailyAnniversaryResource\RelationManagers;
use App\Models\DailyAnniversary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DailyAnniversaryResource extends Resource
{
    protected static ?string $model = DailyAnniversary::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('years_ago')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('original_date')
                    ->required(),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255)
                    ->default('anniversary'),
                Forms\Components\TextInput::make('related_people'),
                Forms\Components\TextInput::make('related_places'),
                Forms\Components\TextInput::make('significance')
                    ->required()
                    ->maxLength(255)
                    ->default('moderate'),
                Forms\Components\Toggle::make('is_recurring')
                    ->required(),
                Forms\Components\TextInput::make('celebration_info'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('years_ago')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('significance')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_recurring')
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
            'index' => Pages\ListDailyAnniversaries::route('/'),
            'create' => Pages\CreateDailyAnniversary::route('/create'),
            'edit' => Pages\EditDailyAnniversary::route('/{record}/edit'),
        ];
    }
}
