<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CooperativeResource\Pages;
use App\Filament\Resources\CooperativeResource\RelationManagers;
use App\Models\Cooperative;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{TextInput, Select, DatePicker, Toggle, Textarea, RichEditor};

class CooperativeResource extends Resource
{
    protected static ?string $model = Cooperative::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Organizaciones y Empresas';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                TextInput::make('legal_name')->maxLength(255),
                Select::make('cooperative_type')
                    ->required()
                    ->options([
                        'energy' => 'Energy',
                        'housing' => 'Housing',
                        'agriculture' => 'Agriculture',
                        'etc' => 'Etc',
                    ]),
                Select::make('scope')
                    ->required()
                    ->options([
                        'local' => 'Local',
                        'regional' => 'Regional',
                        'national' => 'National',
                    ]),
                TextInput::make('nif')->maxLength(20),
                DatePicker::make('founded_at'),
                TextInput::make('phone')->required(),
                TextInput::make('email')->required()->email(),
                TextInput::make('website')->required()->url(),
                TextInput::make('logo_url')->url()->label('Logo URL'),
                Select::make('image_id')->relationship('image', 'id')->searchable(),
                Select::make('municipality_id')->relationship('municipality', 'name')->required()->searchable(),
                TextInput::make('address')->required(),
                TextInput::make('latitude')->numeric()->step(0.000001),
                TextInput::make('longitude')->numeric()->step(0.000001),
                Textarea::make('description'),
                TextInput::make('number_of_members')->numeric(),
                TextInput::make('main_activity')->required(),
                Toggle::make('is_open_to_new_members'),
                Select::make('source')->options([
                    'manual' => 'Manual',
                    'cnmc' => 'CNMC',
                ]),
                Select::make('data_source_id')->relationship('dataSource', 'name')->searchable(),
                Toggle::make('has_energy_market_access'),
                TextInput::make('legal_form'),
                TextInput::make('statutes_url')->url()->label('Statutes URL'),
                Toggle::make('accepts_new_installations'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('cooperative_type')->sortable(),
                Tables\Columns\TextColumn::make('scope')->sortable(),
                Tables\Columns\IconColumn::make('is_open_to_new_members')->boolean(),
                Tables\Columns\IconColumn::make('has_energy_market_access')->boolean(),
                Tables\Columns\IconColumn::make('accepts_new_installations')->boolean(),
                Tables\Columns\TextColumn::make('municipality.name')->label('Municipality'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => Pages\ListCooperatives::route('/'),
            'create' => Pages\CreateCooperative::route('/create'),
            'edit' => Pages\EditCooperative::route('/{record}/edit'),
        ];
    }
}
