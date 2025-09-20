<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class CategoryResource extends Resource
{
    protected static ?string $navigationGroup = 'Sistema y Administración';
    protected static ?string $model = Category::class;
    protected static ?string $navigationLabel = 'Categorías';
    protected static ?string $modelLabel = 'Categoría';
    protected static ?string $pluralModelLabel = 'Categorías';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 8;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->helperText('Se genera automáticamente si está vacío')
                            ->live(onBlur: true),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\Select::make('type')
                            ->label('Tipo de Categoría')
                            ->options([
                                'news' => 'Noticias',
                                'event' => 'Eventos',
                                'profession' => 'Profesiones',
                                'cooperative' => 'Cooperativas',
                                'energy' => 'Energía',
                            ])
                            ->required()
                            ->searchable(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Apariencia')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Icono')
                            ->maxLength(50)
                            ->helperText('Nombre del icono de Heroicons (ej: sun, wind, cog)'),
                        Forms\Components\ColorPicker::make('color')
                            ->label('Color')
                            ->default('#3B82F6'),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Imagen de Portada')
                            ->image()
                            ->directory('categories/covers'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Categoría Padre')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Sin categoría padre'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden de Clasificación')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacada'),
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor')
                            ->helperText('Información adicional en formato clave-valor'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'news' => 'info',
                        'event' => 'success',
                        'profession' => 'warning',
                        'cooperative' => 'primary',
                        'energy' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icono')
                    ->icon(fn (string $state): string => 'heroicon-o-' . $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Categoría Padre')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable()
                    ->badge()
                    ->color('secondary'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Categoría')
                    ->options([
                        'news' => 'Noticias',
                        'event' => 'Eventos',
                        'profession' => 'Profesiones',
                        'cooperative' => 'Cooperativas',
                        'energy' => 'Energía',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado Activo'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacada'),
                Tables\Filters\Filter::make('has_parent')
                    ->label('Con Categoría Padre')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('parent_id')),
                Tables\Filters\Filter::make('no_parent')
                    ->label('Sin Categoría Padre')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),
                Tables\Filters\Filter::make('recent')
                    ->label('Recientes')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (Category $record): string => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn (Category $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Category $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (Category $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                        \Filament\Notifications\Notification::make()
                            ->title($record->is_active ? 'Categoría activada' : 'Categoría desactivada')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn (Category $record): string => $record->is_featured ? 'Quitar Destacada' : 'Marcar Destacada')
                    ->icon(fn (Category $record): string => $record->is_featured ? 'heroicon-o-star' : 'heroicon-o-star')
                    ->color(fn (Category $record): string => $record->is_featured ? 'warning' : 'success')
                    ->action(function (Category $record): void {
                        $record->update(['is_featured' => !$record->is_featured]);
                        \Filament\Notifications\Notification::make()
                            ->title($record->is_featured ? 'Categoría marcada como destacada' : 'Categoría quitada de destacadas')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar Seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records): void {
                            $records->each(fn (Category $record) => $record->update(['is_active' => true]));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' categorías activadas')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar Seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->action(function (Collection $records): void {
                            $records->each(fn (Category $record) => $record->update(['is_active' => false]));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' categorías desactivadas')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('toggle_featured')
                        ->label('Cambiar Estado Destacada')
                        ->icon('heroicon-o-star')
                        ->action(function (Collection $records): void {
                            $records->each(fn (Category $record) => $record->update(['is_featured' => !$record->is_featured]));
                            \Filament\Notifications\Notification::make()
                                ->title('Estado destacada de ' . $records->count() . ' categorías actualizado')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['parent']));
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
