<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataSourceResource\Pages;
use App\Filament\Resources\DataSourceResource\RelationManagers;
use App\Models\DataSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{TextInput, Textarea, Toggle, Select};
use Filament\Tables\Columns\{TextColumn, BooleanColumn};

class DataSourceResource extends Resource
{
    protected static ?string $model = DataSource::class;

    protected static ?string $navigationIcon = 'heroicon-o-rss';
    protected static ?string $navigationGroup = 'Sistema y Administración';
    protected static ?int $navigationSort = 8;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('url')->url()->required()->maxLength(255),
                Textarea::make('description')->maxLength(1000),
                Toggle::make('official')->label('Is Official?'),
                TextInput::make('country_code')->maxLength(3)->helperText('ISO Alpha-2 or Alpha-3 code'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('url')->limit(30)->toggleable(),
                BooleanColumn::make('official'),
                TextColumn::make('country_code')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDataSources::route('/'),
            'create' => Pages\CreateDataSource::route('/create'),
            'edit' => Pages\EditDataSource::route('/{record}/edit'),
        ];
    }
}
