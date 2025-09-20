<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FestivalProgramResource\Pages;
use App\Filament\Resources\FestivalProgramResource\RelationManagers;
use App\Models\FestivalProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FestivalProgramResource extends Resource
{
    protected static ?string $model = FestivalProgram::class;
    protected static ?string $navigationGroup = 'Eventos y Cultura';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('festival_id')
                    ->relationship('festival', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('day')
                    ->required(),
                Forms\Components\TextInput::make('start_time')
                    ->required(),
                Forms\Components\TextInput::make('end_time')
                    ->required(),
                Forms\Components\TextInput::make('event_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('location')
                    ->maxLength(255),
                Forms\Components\Select::make('artist_id')
                    ->relationship('artist', 'name'),
                Forms\Components\Select::make('group_id')
                    ->relationship('group', 'name'),
                Forms\Components\TextInput::make('event_type')
                    ->required()
                    ->maxLength(255)
                    ->default('performance'),
                Forms\Components\Toggle::make('is_free')
                    ->required(),
                Forms\Components\TextInput::make('ticket_price')
                    ->numeric(),
                Forms\Components\TextInput::make('capacity')
                    ->numeric(),
                Forms\Components\TextInput::make('current_attendance')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('additional_info'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('festival.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('event_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('artist.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_type')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'performance' => 'success',
                        'workshop' => 'info',
                        'conference' => 'warning',
                        'exhibition' => 'primary',
                        'party' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('is_free')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Gratuito' : 'De pago')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('ticket_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_attendance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_status')
                    ->label('Estado')
                    ->getStateUsing(function ($record): string {
                        if (!$record->capacity) return 'Sin límite';
                        $percentage = ($record->current_attendance / $record->capacity) * 100;
                        if ($percentage >= 100) return 'Lleno';
                        if ($percentage >= 80) return 'Casi lleno';
                        return 'Disponible';
                    })
                    ->badge()
                    ->color(function ($record): string {
                        if (!$record->capacity) return 'gray';
                        $percentage = ($record->current_attendance / $record->capacity) * 100;
                        if ($percentage >= 100) return 'danger';
                        if ($percentage >= 80) return 'warning';
                        return 'success';
                    }),
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
            'index' => Pages\ListFestivalPrograms::route('/'),
            'create' => Pages\CreateFestivalProgram::route('/create'),
            'view' => Pages\ViewFestivalProgram::route('/{record}'),
            'edit' => Pages\EditFestivalProgram::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['festival', 'artist', 'group']);
    }
}
