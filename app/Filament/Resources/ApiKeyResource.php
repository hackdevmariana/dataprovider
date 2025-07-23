<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiKeyResource\Pages;
use App\Filament\Resources\ApiKeyResource\RelationManagers;
use App\Models\ApiKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;
    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            TextInput::make('token')->required()->unique(),
            TextInput::make('scope')->nullable(),
            TextInput::make('rate_limit')->numeric()->nullable(),
            DateTimePicker::make('expires_at')->nullable(),
            Toggle::make('is_revoked'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('token')->limit(20)->copyable(),
            Tables\Columns\TextColumn::make('scope'),
            Tables\Columns\TextColumn::make('rate_limit'),
            Tables\Columns\TextColumn::make('expires_at')->dateTime(),
            Tables\Columns\IconColumn::make('is_revoked')->boolean(),
        ])
        ->filters([])
        ->defaultSort('expires_at', 'desc');
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
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
            'edit' => Pages\EditApiKey::route('/{record}/edit'),
        ];
    }
}
