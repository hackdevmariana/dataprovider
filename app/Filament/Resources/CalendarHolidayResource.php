<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarHolidayResource\Pages;
use App\Filament\Resources\CalendarHolidayResource\RelationManagers;
use App\Models\CalendarHoliday;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CalendarHolidayResource extends Resource
{
    protected static ?string $model = CalendarHoliday::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Events & Calendar';

    protected static ?string $label = 'Holiday';
    protected static ?string $pluralLabel = 'Holidays';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('date')
                    ->required(),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('municipality_id')
                    ->relationship('municipality', 'name')
                    ->searchable()
                    ->label('Municipality')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('date')->sortable(),
                Tables\Columns\TextColumn::make('municipality.name')->label('Municipality')->searchable(),
            ])
            ->defaultSort('date')
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CalendarHolidayLocationRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendarHolidays::route('/'),
            'create' => Pages\CreateCalendarHoliday::route('/create'),
            'edit' => Pages\EditCalendarHoliday::route('/{record}/edit'),
        ];
    }
}
