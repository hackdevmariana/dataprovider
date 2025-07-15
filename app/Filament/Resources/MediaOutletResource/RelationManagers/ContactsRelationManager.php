<?php

namespace App\Filament\Resources\MediaOutletResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class MediaContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'mediaContacts';

    protected static ?string $title = 'Contacts';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
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

    public function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('type')->sortable(),
            Tables\Columns\TextColumn::make('contact_name'),
            Tables\Columns\TextColumn::make('email'),
            Tables\Columns\TextColumn::make('phone'),
        ])
        ->headerActions([Tables\Actions\CreateAction::make()])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }
}
