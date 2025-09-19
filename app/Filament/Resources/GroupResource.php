<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GroupResource\RelationManagers\GroupArtistRelationManager;


class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'fas-music';
    protected static ?string $navigationGroup = 'Organizaciones y Empresas';
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Grupo Musical';
    protected static ?string $pluralModelLabel = 'Grupos Musicales';



    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Básica')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre del Grupo')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (URL)')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('URL amigable para el grupo (ej: mecano, heroes-del-silencio)')
                        ->columnSpan(2),
                    Forms\Components\Textarea::make('description')
                        ->label('Descripción Corta')
                        ->nullable()
                        ->maxLength(500)
                        ->columnSpan(2),
                    Forms\Components\Select::make('genre')
                        ->label('Género Musical')
                        ->options([
                            'Pop' => 'Pop',
                            'Rock' => 'Rock',
                            'Electrónica' => 'Electrónica',
                            'Hip-Hop' => 'Hip-Hop',
                            'Jazz' => 'Jazz',
                            'Clásica' => 'Clásica',
                            'Folk' => 'Folk',
                            'Reggae' => 'Reggae',
                            'Blues' => 'Blues',
                            'Country' => 'Country',
                            'R&B' => 'R&B',
                            'Soul' => 'Soul',
                            'Funk' => 'Funk',
                            'Punk' => 'Punk',
                            'Metal' => 'Metal',
                            'Indie' => 'Indie',
                            'Alternativo' => 'Alternativo',
                        ])
                        ->searchable()
                        ->columnSpan(1),
                    Forms\Components\Select::make('active_status')
                        ->label('Estado Actual')
                        ->options([
                            'active' => 'Activo',
                            'inactive' => 'Inactivo',
                            'disbanded' => 'Disuelto',
                            'on_hiatus' => 'En Pausa',
                        ])
                        ->default('active')
                        ->required()
                        ->columnSpan(1),
                ])
                ->columns(2),

            Forms\Components\Section::make('Fechas de Actividad')
                ->schema([
                    Forms\Components\DatePicker::make('formed_at')
                        ->label('Fecha de Formación')
                        ->nullable()
                        ->columnSpan(1),
                    Forms\Components\DatePicker::make('disbanded_at')
                        ->label('Fecha de Disolución')
                        ->nullable()
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('Información de Contacto y Web')
                ->schema([
                    Forms\Components\TextInput::make('website')
                        ->label('Sitio Web')
                        ->url()
                        ->nullable()
                        ->columnSpan(2),
                    Forms\Components\KeyValue::make('social_media')
                        ->label('Redes Sociales')
                        ->keyLabel('Plataforma')
                        ->valueLabel('URL')
                        ->addActionLabel('Añadir Red Social')
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('contact_email')
                        ->label('Email de Contacto')
                        ->email()
                        ->nullable()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('management_company')
                        ->label('Compañía de Gestión')
                        ->nullable()
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('Origen y Ubicación')
                ->schema([
                    Forms\Components\TextInput::make('origin_country')
                        ->label('País de Origen')
                        ->nullable()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('origin_city')
                        ->label('Ciudad de Origen')
                        ->nullable()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('current_location')
                        ->label('Ubicación Actual')
                        ->nullable()
                        ->columnSpan(2),
                    Forms\Components\Select::make('municipality_id')
                        ->label('Municipio')
                        ->relationship('municipality', 'name')
                        ->searchable()
                        ->nullable()
                        ->columnSpan(2),
                ])
                ->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('Información Musical y Comercial')
                ->schema([
                    Forms\Components\TextInput::make('record_label')
                        ->label('Sello Discográfico')
                        ->nullable()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('albums_count')
                        ->label('Número de Álbumes')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('songs_count')
                        ->label('Número de Canciones')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('search_boost')
                        ->label('Factor de Búsqueda')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(1000)
                        ->default(100)
                        ->helperText('Factor de relevancia para búsquedas (1-1000)')
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('Premios y Certificaciones')
                ->schema([
                    Forms\Components\TagsInput::make('awards')
                        ->label('Premios Recibidos')
                        ->separator(',')
                        ->columnSpan(2),
                    Forms\Components\KeyValue::make('certifications')
                        ->label('Certificaciones')
                        ->keyLabel('Álbum')
                        ->valueLabel('Certificaciones')
                        ->addActionLabel('Añadir Certificación')
                        ->columnSpan(2),
                ])
                ->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('Contenido y Metadatos')
                ->schema([
                    Forms\Components\RichEditor::make('biography')
                        ->label('Biografía Completa')
                        ->nullable()
                        ->columnSpan(2),
                    Forms\Components\Select::make('image_id')
                        ->label('Imagen Principal')
                        ->relationship('image', 'id')
                        ->searchable()
                        ->nullable()
                        ->columnSpan(1),
                    Forms\Components\TagsInput::make('tags')
                        ->label('Etiquetas')
                        ->separator(',')
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('Configuración Adicional')
                ->schema([
                    Forms\Components\TextInput::make('official_fan_club')
                        ->label('Club de Fans Oficial')
                        ->url()
                        ->nullable()
                        ->columnSpan(1),
                    Forms\Components\Toggle::make('is_verified')
                        ->label('Grupo Verificado')
                        ->default(false)
                        ->columnSpan(1),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Grupo Destacado')
                        ->default(false)
                        ->columnSpan(1),
                    Forms\Components\Select::make('source')
                        ->label('Fuente de Información')
                        ->options([
                            'manual' => 'Manual',
                            'api' => 'API Externa',
                            'scraping' => 'Web Scraping',
                            'import' => 'Importación',
                        ])
                        ->default('manual')
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\TextColumn::make('name')
                ->label('Nombre del Grupo')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->limit(30),
            
            Tables\Columns\TextColumn::make('slug')
                ->label('Slug')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\TextColumn::make('genre')
                ->label('Género')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Pop' => 'info',
                    'Rock' => 'danger',
                    'Electrónica' => 'warning',
                    'Hip-Hop' => 'success',
                    'Jazz' => 'primary',
                    'Clásica' => 'gray',
                    default => 'secondary',
                })
                ->searchable()
                ->sortable(),
            
            Tables\Columns\TextColumn::make('active_status')
                ->label('Estado')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'inactive' => 'warning',
                    'disbanded' => 'danger',
                    'on_hiatus' => 'info',
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'active' => 'Activo',
                    'inactive' => 'Inactivo',
                    'disbanded' => 'Disuelto',
                    'on_hiatus' => 'En Pausa',
                    default => $state,
                })
                ->sortable(),
            
            Tables\Columns\TextColumn::make('origin_country')
                ->label('País')
                ->searchable()
                ->sortable()
                ->toggleable(),
            
            Tables\Columns\TextColumn::make('origin_city')
                ->label('Ciudad')
                ->searchable()
                ->sortable()
                ->toggleable(),
            
            Tables\Columns\TextColumn::make('formed_at')
                ->label('Formado')
                ->date('d/m/Y')
                ->sortable()
                ->toggleable(),
            
            Tables\Columns\TextColumn::make('albums_count')
                ->label('Álbumes')
                ->numeric()
                ->sortable()
                ->toggleable(),
            
            Tables\Columns\TextColumn::make('songs_count')
                ->label('Canciones')
                ->numeric()
                ->sortable()
                ->toggleable(),
            
            Tables\Columns\TextColumn::make('search_boost')
                ->label('Relevancia')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\IconColumn::make('is_verified')
                ->label('Verificado')
                ->boolean()
                ->trueIcon('fas-check-circle')
                ->falseIcon('fas-times-circle')
                ->trueColor('success')
                ->falseColor('gray'),
            
            Tables\Columns\IconColumn::make('is_featured')
                ->label('Destacado')
                ->boolean()
                ->trueIcon('fas-star')
                ->falseIcon('fas-star')
                ->trueColor('warning')
                ->falseColor('gray'),
            
            Tables\Columns\TextColumn::make('website')
                ->label('Web')
                ->url(fn ($record) => $record->website)
                ->openUrlInNewTab()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\TextColumn::make('record_label')
                ->label('Sello')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\TextColumn::make('members_info')
                ->label('Miembros')
                ->getStateUsing(fn (Group $record): string => $record->artists()->count() . ' miembros')
                ->toggleable(),
            
            Tables\Columns\TextColumn::make('created_at')
                ->label('Creado')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('genre')
                ->label('Género Musical')
                ->options([
                    'Pop' => 'Pop',
                    'Rock' => 'Rock',
                    'Electrónica' => 'Electrónica',
                    'Hip-Hop' => 'Hip-Hop',
                    'Jazz' => 'Jazz',
                    'Clásica' => 'Clásica',
                    'Folk' => 'Folk',
                    'Reggae' => 'Reggae',
                    'Blues' => 'Blues',
                    'Country' => 'Country',
                ])
                ->multiple(),
            
            Tables\Filters\SelectFilter::make('active_status')
                ->label('Estado')
                ->options([
                    'active' => 'Activo',
                    'inactive' => 'Inactivo',
                    'disbanded' => 'Disuelto',
                    'on_hiatus' => 'En Pausa',
                ])
                ->multiple(),
            
            Tables\Filters\SelectFilter::make('origin_country')
                ->label('País de Origen')
                ->options(function () {
                    return Group::distinct()->pluck('origin_country', 'origin_country')
                        ->filter()->toArray();
                })
                ->searchable()
                ->multiple(),
            
            Tables\Filters\TernaryFilter::make('is_verified')
                ->label('Verificado'),
            
            Tables\Filters\TernaryFilter::make('is_featured')
                ->label('Destacado'),
            
            Tables\Filters\Filter::make('formed_after')
                ->label('Formado después de')
                ->form([
                    Forms\Components\DatePicker::make('formed_after')
                        ->label('Fecha'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['formed_after'],
                            fn (Builder $query, $date): Builder => $query->where('formed_at', '>=', $date),
                        );
                }),
            
            Tables\Filters\Filter::make('albums_range')
                ->label('Rango de Álbumes')
                ->form([
                    Forms\Components\TextInput::make('albums_min')
                        ->label('Mínimo')
                        ->numeric()
                        ->minValue(0),
                    Forms\Components\TextInput::make('albums_max')
                        ->label('Máximo')
                        ->numeric()
                        ->minValue(0),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['albums_min'],
                            fn (Builder $query, $min): Builder => $query->where('albums_count', '>=', $min),
                        )
                        ->when(
                            $data['albums_max'],
                            fn (Builder $query, $max): Builder => $query->where('albums_count', '<=', $max),
                        );
                }),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('verify')
                ->label('Verificar')
                ->icon('fas-check-circle')
                ->color('success')
                ->visible(fn (Group $record): bool => !$record->is_verified)
                ->action(function (Group $record): void {
                    $record->update(['is_verified' => true]);
                }),
            Tables\Actions\Action::make('unverify')
                ->label('Quitar Verificación')
                ->icon('fas-times-circle')
                ->color('gray')
                ->visible(fn (Group $record): bool => $record->is_verified)
                ->action(function (Group $record): void {
                    $record->update(['is_verified' => false]);
                }),
            Tables\Actions\Action::make('feature')
                ->label('Destacar')
                ->icon('fas-star')
                ->color('warning')
                ->visible(fn (Group $record): bool => !$record->is_featured)
                ->action(function (Group $record): void {
                    $record->update(['is_featured' => true]);
                }),
            Tables\Actions\Action::make('unfeature')
                ->label('Quitar Destacado')
                ->icon('fas-times-circle')
                ->color('gray')
                ->visible(fn (Group $record): bool => $record->is_featured)
                ->action(function (Group $record): void {
                    $record->update(['is_featured' => false]);
                }),
            Tables\Actions\Action::make('mark_disbanded')
                ->label('Marcar como Disuelto')
                ->icon('fas-times')
                ->color('danger')
                ->visible(fn (Group $record): bool => $record->active_status !== 'disbanded')
                ->action(function (Group $record): void {
                    $record->update([
                        'active_status' => 'disbanded',
                        'disbanded_at' => now(),
                    ]);
                }),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\BulkAction::make('verify')
                    ->label('Verificar Seleccionados')
                    ->icon('fas-check-circle')
                    ->action(function (\Illuminate\Support\Collection $records): void {
                        $records->each(fn (Group $record) => $record->update(['is_verified' => true]));
                    }),
                Tables\Actions\BulkAction::make('feature')
                    ->label('Destacar Seleccionados')
                    ->icon('fas-star')
                    ->color('warning')
                    ->action(function (\Illuminate\Support\Collection $records): void {
                        $records->each(fn (Group $record) => $record->update(['is_featured' => true]));
                    }),
                Tables\Actions\BulkAction::make('mark_disbanded')
                    ->label('Marcar como Disueltos')
                    ->icon('fas-times')
                    ->color('danger')
                    ->action(function (\Illuminate\Support\Collection $records): void {
                        $records->each(fn (Group $record) => $record->update([
                            'active_status' => 'disbanded',
                            'disbanded_at' => now(),
                        ]));
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ])
        ->defaultSort('search_boost', 'desc')
        ->modifyQueryUsing(fn (Builder $query) => $query->with(['artists', 'municipality', 'image']));
    }

    public static function getRelations(): array
    {
        return [
            GroupArtistRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
