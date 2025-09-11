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
use Illuminate\Support\Collection;

class MediaContactResource extends Resource
{
    protected static ?string $model = MediaContact::class;

    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $label = 'Contacto del medio de comunicación';
    protected static ?string $pluralLabel = 'Contactos del medio de comunicación';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Básica')
                ->schema([
                    Forms\Components\Select::make('media_outlet_id')
                        ->label('Medio de Comunicación')
                        ->relationship('mediaOutlet', 'name')
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('type')
                        ->label('Tipo de Contacto')
                        ->options([
                            'editorial' => 'Editorial',
                            'commercial' => 'Comercial',
                            'general' => 'General',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('contact_name')
                        ->label('Nombre del Contacto')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('job_title')
                        ->label('Cargo/Posición')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('department')
                        ->label('Departamento')
                        ->maxLength(255),
                ])->columns(2),
                
            Forms\Components\Section::make('Información de Contacto')
                ->schema([
                    Forms\Components\TextInput::make('phone')
                        ->label('Teléfono Principal')
                        ->tel()
                        ->maxLength(20),
                    Forms\Components\TextInput::make('mobile_phone')
                        ->label('Teléfono Móvil')
                        ->tel()
                        ->maxLength(20),
                    Forms\Components\TextInput::make('email')
                        ->label('Email Principal')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('secondary_email')
                        ->label('Email Secundario')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\Select::make('preferred_contact_method')
                        ->label('Método de Contacto Preferido')
                        ->options([
                            'email' => 'Email',
                            'phone' => 'Teléfono',
                            'whatsapp' => 'WhatsApp',
                            'linkedin' => 'LinkedIn',
                            'twitter' => 'Twitter',
                        ]),
                    Forms\Components\TextInput::make('language_preference')
                        ->label('Idioma Preferido')
                        ->maxLength(10)
                        ->default('es'),
                ])->columns(2),
                
            Forms\Components\Section::make('Especializaciones y Cobertura')
                ->schema([
                    Forms\Components\TagsInput::make('specializations')
                        ->label('Especializaciones Temáticas')
                        ->separator(','),
                    Forms\Components\TagsInput::make('coverage_areas')
                        ->label('Áreas de Cobertura')
                        ->separator(','),
                    Forms\Components\KeyValue::make('availability_schedule')
                        ->label('Horario de Disponibilidad')
                        ->keyLabel('Día')
                        ->valueLabel('Horario'),
                ])->columns(2),
                
            Forms\Components\Section::make('Configuración')
                ->schema([
                    Forms\Components\Toggle::make('accepts_press_releases')
                        ->label('Acepta Comunicados de Prensa')
                        ->default(true),
                    Forms\Components\Toggle::make('accepts_interviews')
                        ->label('Acepta Entrevistas')
                        ->default(false),
                    Forms\Components\Toggle::make('accepts_events_invitations')
                        ->label('Acepta Invitaciones a Eventos')
                        ->default(true),
                    Forms\Components\Toggle::make('is_freelancer')
                        ->label('Es Freelance')
                        ->default(false),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Contacto Activo')
                        ->default(true),
                    Forms\Components\Toggle::make('is_verified')
                        ->label('Contacto Verificado')
                        ->default(false),
                ])->columns(2),
                
            Forms\Components\Section::make('Métricas y Prioridad')
                ->schema([
                    Forms\Components\Select::make('priority_level')
                        ->label('Nivel de Prioridad')
                        ->options([
                            1 => 'Muy Baja',
                            2 => 'Baja',
                            3 => 'Media',
                            4 => 'Alta',
                            5 => 'Muy Alta',
                        ])
                        ->default(3)
                        ->required(),
                    Forms\Components\TextInput::make('response_rate')
                        ->label('Tasa de Respuesta')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->suffix('%'),
                    Forms\Components\TextInput::make('contacts_count')
                        ->label('Número de Contactos')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),
                    Forms\Components\TextInput::make('successful_contacts')
                        ->label('Contactos Exitosos')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),
                ])->columns(2),
                
            Forms\Components\Section::make('Información Adicional')
                ->schema([
                    Forms\Components\Textarea::make('bio')
                        ->label('Biografía Breve')
                        ->rows(3)
                        ->maxLength(500),
                    Forms\Components\KeyValue::make('social_media_profiles')
                        ->label('Perfiles de Redes Sociales')
                        ->keyLabel('Plataforma')
                        ->valueLabel('Usuario'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas Internas')
                        ->rows(3)
                        ->maxLength(1000),
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
                Tables\Columns\TextColumn::make('mediaOutlet.name')
                    ->label('Medio de Comunicación')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'editorial' => 'info',
                        'commercial' => 'success',
                        'general' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Nombre del Contacto')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('job_title')
                    ->label('Cargo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('department')
                    ->label('Departamento')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation'),
                Tables\Columns\TextColumn::make('priority_level')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1, 2 => 'gray',
                        3 => 'warning',
                        4, 5 => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('response_rate')
                    ->label('Tasa Respuesta')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('contacts_count')
                    ->label('Contactos')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('last_contacted_at')
                    ->label('Último Contacto')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Contacto')
                    ->options([
                        'editorial' => 'Editorial',
                        'commercial' => 'Comercial',
                        'general' => 'General',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado Activo'),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),
                Tables\Filters\SelectFilter::make('priority_level')
                    ->label('Nivel de Prioridad')
                    ->options([
                        1 => 'Muy Baja',
                        2 => 'Baja',
                        3 => 'Media',
                        4 => 'Alta',
                        5 => 'Muy Alta',
                    ]),
                Tables\Filters\Filter::make('high_priority')
                    ->label('Alta Prioridad')
                    ->query(fn (Builder $query): Builder => $query->where('priority_level', '>=', 4)),
                Tables\Filters\Filter::make('recent_contacts')
                    ->label('Contactos Recientes')
                    ->query(fn (Builder $query): Builder => $query->where('last_contacted_at', '>=', now()->subDays(30))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (MediaContact $record): string => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn (MediaContact $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (MediaContact $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (MediaContact $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                        \Filament\Notifications\Notification::make()
                            ->title($record->is_active ? 'Contacto activado' : 'Contacto desactivado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('toggle_verified')
                    ->label(fn (MediaContact $record): string => $record->is_verified ? 'Desverificar' : 'Verificar')
                    ->icon(fn (MediaContact $record): string => $record->is_verified ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                    ->color(fn (MediaContact $record): string => $record->is_verified ? 'warning' : 'success')
                    ->action(function (MediaContact $record): void {
                        $record->update(['is_verified' => !$record->is_verified]);
                        \Filament\Notifications\Notification::make()
                            ->title($record->is_verified ? 'Contacto verificado' : 'Contacto desverificado')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records): void {
                            $records->each(fn (MediaContact $record) => $record->update(['is_active' => true]));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' contactos activados')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar Seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->action(function (Collection $records): void {
                            $records->each(fn (MediaContact $record) => $record->update(['is_active' => false]));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' contactos desactivados')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['mediaOutlet']));
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
