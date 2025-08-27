<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyCertificationResource\Pages;
use App\Filament\Resources\CompanyCertificationResource\RelationManagers;
use App\Models\CompanyCertification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyCertificationResource extends Resource
{
    protected static ?string $model = CompanyCertification::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('certification_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('issuing_body')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('certification_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('issued_date')
                    ->required(),
                Forms\Components\DatePicker::make('expiry_date'),
                Forms\Components\TextInput::make('certificate_number')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('scope'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('active'),
                Forms\Components\TextInput::make('requirements_met'),
                Forms\Components\Toggle::make('is_verified')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('certification_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issuing_body')
                    ->searchable(),
                Tables\Columns\TextColumn::make('certification_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('certificate_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean(),
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
            'index' => Pages\ListCompanyCertifications::route('/'),
            'create' => Pages\CreateCompanyCertification::route('/create'),
            'edit' => Pages\EditCompanyCertification::route('/{record}/edit'),
        ];
    }
}
