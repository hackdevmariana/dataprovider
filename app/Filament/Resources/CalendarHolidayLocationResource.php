<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarHolidayLocationResource\Pages;
use App\Filament\Resources\CalendarHolidayLocationResource\RelationManagers;
use App\Models\CalendarHolidayLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CalendarHolidayLocationResource extends Resource
{
    protected static ?string $model = CalendarHolidayLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    
    protected static ?string $navigationGroup = 'Events & Calendar';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Ubicación de Feriado';
    
    protected static ?string $pluralModelLabel = 'Ubicaciones de Feriados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('calendar_holiday_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('municipality_id')
                    ->relationship('municipality', 'name'),
                Forms\Components\Select::make('province_id')
                    ->relationship('province', 'name'),
                Forms\Components\Select::make('autonomous_community_id')
                    ->relationship('autonomousCommunity', 'name'),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('calendar_holiday_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('municipality.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('autonomousCommunity.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
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
            'index' => Pages\ListCalendarHolidayLocations::route('/'),
            'create' => Pages\CreateCalendarHolidayLocation::route('/create'),
            'edit' => Pages\EditCalendarHolidayLocation::route('/{record}/edit'),
        ];
    }
}
