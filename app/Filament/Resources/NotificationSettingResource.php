<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationSettingResource\Pages;
use App\Filament\Resources\NotificationSettingResource\RelationManagers;
use App\Models\NotificationSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;


class NotificationSettingResource extends Resource
{
    protected static ?string $model = NotificationSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            Select::make('type')
                ->options([
                    'electricity_price' => 'Electricity Price',
                    'event' => 'Event',
                    'solar_production' => 'Solar Production',
                ])
                ->required(),

            TextInput::make('target_id')
                ->numeric()
                ->nullable(),

            TextInput::make('threshold')
                ->numeric()
                ->step(0.0001)
                ->nullable(),

            Select::make('delivery_method')
                ->options([
                    'app' => 'App',
                    'email' => 'Email',
                    'sms' => 'SMS',
                ])
                ->default('app')
                ->required(),

            Toggle::make('is_silent')
                ->label('Silent?')
                ->default(false),

            Toggle::make('active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('user.name')->label('User')->searchable(),
            TextColumn::make('type')->sortable(),
            TextColumn::make('target_id'),
            TextColumn::make('threshold'),
            TextColumn::make('delivery_method'),
            IconColumn::make('is_silent')->boolean()->label('Silent'),
            IconColumn::make('active')->boolean(),
            TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => Pages\ListNotificationSettings::route('/'),
            'create' => Pages\CreateNotificationSetting::route('/create'),
            'edit' => Pages\EditNotificationSetting::route('/{record}/edit'),
        ];
    }
}
