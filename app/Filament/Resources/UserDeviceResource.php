<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserDeviceResource\Pages;
use App\Filament\Resources\UserDeviceResource\RelationManagers;
use App\Models\UserDevice;
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
use Filament\Tables\Columns\ToggleColumn;

class UserDeviceResource extends Resource
{
    protected static ?string $model = UserDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationLabel = 'User Devices';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('device_name')->label('Device Name')->maxLength(255),
                TextInput::make('device_type')->label('Device Type')->maxLength(50),
                TextInput::make('platform')->label('Platform')->maxLength(50),
                TextInput::make('browser')->label('Browser')->maxLength(50),
                TextInput::make('ip_address')->label('IP Address')->maxLength(45),
                TextInput::make('token')->label('Token')->maxLength(255),
                Toggle::make('notifications_enabled')->label('Notifications Enabled'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable()->sortable(),
                TextColumn::make('device_name')->label('Device')->searchable(),
                TextColumn::make('platform')->sortable(),
                TextColumn::make('browser'),
                ToggleColumn::make('notifications_enabled')->label('Enabled'),
                TextColumn::make('ip_address'),
                TextColumn::make('created_at')->dateTime('Y-m-d H:i'),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListUserDevices::route('/'),
            'create' => Pages\CreateUserDevice::route('/create'),
            'edit' => Pages\EditUserDevice::route('/{record}/edit'),
        ];
    }
}
