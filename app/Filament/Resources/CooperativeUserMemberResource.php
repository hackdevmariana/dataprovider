<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CooperativeUserMemberResource\Pages;
use App\Filament\Resources\CooperativeUserMemberResource\RelationManagers;
use App\Models\CooperativeUserMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Text;

class CooperativeUserMemberResource extends Resource
{
    protected static ?string $model = CooperativeUserMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'People & Organizations';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('cooperative_id')
                ->relationship('cooperative', 'name')
                ->searchable()
                ->required(),

            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('role')->maxLength(255),

            Forms\Components\DatePicker::make('joined_at'),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('cooperative.name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('user.name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('role'),
            Tables\Columns\TextColumn::make('joined_at')->date(),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
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
            'index' => Pages\ListCooperativeUserMembers::route('/'),
            'create' => Pages\CreateCooperativeUserMember::route('/create'),
            'edit' => Pages\EditCooperativeUserMember::route('/{record}/edit'),
        ];
    }
}
