<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SyncLogResource\Pages;
use App\Filament\Resources\SyncLogResource\RelationManagers;
use App\Models\SyncLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class SyncLogResource extends Resource
{
    protected static ?string $model = SyncLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('data_source_id')
                ->label('Data Source')
                ->relationship('dataSource', 'name')
                ->searchable()
                ->required(),
            Select::make('status')
                ->label('Status')
                ->options([
                    'success' => 'Success',
                    'failed' => 'Failed',
                ])
                ->required(),
            Forms\Components\DateTimePicker::make('started_at')
                ->label('Started At')
                ->required(),
            Forms\Components\DateTimePicker::make('finished_at')
                ->label('Finished At'),
            Forms\Components\TextInput::make('processed_items_count')
                ->label('Processed Items Count')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable()->searchable(),
            TextColumn::make('dataSource.name')->label('Data Source')->searchable()->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'success' => 'success',
                    'failed' => 'danger',
                ]),
            TextColumn::make('started_at')->label('Started At')->dateTime()->sortable(),
            TextColumn::make('finished_at')->label('Finished At')->dateTime()->sortable(),
            TextColumn::make('processed_items_count')->label('Items Count')->sortable()->badge(),
            TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'success' => 'Success',
                    'failed' => 'Failed',
                ]),
            Tables\Filters\SelectFilter::make('data_source_id')
                ->label('Data Source')
                ->relationship('dataSource', 'name'),
            Tables\Filters\Filter::make('in_progress')
                ->label('In Progress')
                ->query(fn (Builder $query): Builder => $query->whereNull('finished_at')),
            Tables\Filters\Filter::make('completed')
                ->label('Completed')
                ->query(fn (Builder $query): Builder => $query->whereNotNull('finished_at')),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ])
        ->defaultSort('started_at', 'desc');
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
            'index' => Pages\ListSyncLogs::route('/'),
            'create' => Pages\CreateSyncLog::route('/create'),
            'edit' => Pages\EditSyncLog::route('/{record}/edit'),
        ];
    }
}
