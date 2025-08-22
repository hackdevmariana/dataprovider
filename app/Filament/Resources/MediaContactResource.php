<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaContactResource\Pages;
use App\Filament\Resources\MediaContactResource\RelationManagers;
use App\Models\MediaContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MediaContactResource extends Resource
{
    protected static ?string $model = MediaContact::class;

    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $label = 'Contacto del medio de comunicación';
    protected static ?string $pluralLabel = 'Contactos del medio de comunicación';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('media_outlet_id')
                ->relationship('mediaOutlet', 'name')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('type')
                ->options([
                    'editorial' => 'Editorial',
                    'commercial' => 'Commercial',
                    'general' => 'General',
                ])
                ->required(),
            Forms\Components\TextInput::make('contact_name')->nullable(),
            Forms\Components\TextInput::make('phone')->nullable(),
            Forms\Components\TextInput::make('email')->email()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mediaOutlet.name')->label('Media Outlet'),
                Tables\Columns\TextColumn::make('type')->sortable(),
                Tables\Columns\TextColumn::make('contact_name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
            ])
            ->defaultSort('mediaOutlet.name');
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
            'index' => Pages\ListMediaContacts::route('/'),
            'create' => Pages\CreateMediaContact::route('/create'),
            'edit' => Pages\EditMediaContact::route('/{record}/edit'),
        ];
    }
}
