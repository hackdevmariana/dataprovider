<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiKeyResource\Pages;
use App\Models\ApiKey;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationLabel = 'API Keys';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'API Key';
    protected static ?string $pluralModelLabel = 'API Keys';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                ->relationship('user', 'name')
                ->searchable()
                            ->preload()
                ->required(),

                        Forms\Components\TextInput::make('token')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Token')
                            ->placeholder('Token único de la API Key')
                            ->helperText('Token único que identifica esta API Key'),

                        Forms\Components\Select::make('scope')
                            ->options([
                                'read-only' => 'Solo Lectura',
                                'write' => 'Escritura',
                                'full-access' => 'Acceso Completo',
                            ])
                            ->required()
                            ->default('read-only')
                            ->label('Alcance'),

                        Forms\Components\TextInput::make('rate_limit')
                            ->numeric()
                            ->required()
                            ->default(1000)
                            ->label('Límite de Tasa')
                            ->suffix('requests/hora')
                            ->helperText('Número máximo de requests por hora'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->displayFormat('d/m/Y H:i')
                            ->nullable()
                            ->helperText('Dejar vacío para que nunca expire'),

                        Forms\Components\Toggle::make('is_revoked')
                            ->label('Revocada')
                            ->default(false)
                            ->helperText('Indica si la API Key ha sido revocada'),
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
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('token_preview')
                    ->label('Token')
                    ->getStateUsing(fn (ApiKey $record): string => $record->token_preview)
                    ->copyable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('scope')
                    ->label('Alcance')
                    ->colors(fn (string $state): array => match ($state) {
                        'read-only' => ['info'],
                        'write' => ['warning'],
                        'full-access' => ['success'],
                        default => ['secondary'],
                    })
                    ->formatStateUsing(fn (string $state): string => ApiKey::make(['scope' => $state])->scope_label),

                Tables\Columns\TextColumn::make('rate_limit')
                    ->label('Límite')
                    ->numeric()
                    ->sortable()
                    ->suffix('/h')
                    ->color(fn (int $state): string => match (true) {
                        $state >= 5000 => 'success',
                        $state >= 1000 => 'info',
                        $state >= 100 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Sin expiración')
                    ->color(fn ($record): string => 
                        $record->expires_at && $record->expires_at->diffInDays(now()) <= 7 ? 'danger' : 
                        ($record->expires_at && $record->expires_at->diffInDays(now()) <= 30 ? 'warning' : 'success')
                    ),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->getStateUsing(fn (ApiKey $record): string => $record->status_label)
                    ->colors(fn (ApiKey $record): array => [$record->status_color]),

                Tables\Columns\IconColumn::make('is_revoked')
                    ->label('Revocada')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('scope')
                    ->options([
                        'read-only' => 'Solo Lectura',
                        'write' => 'Escritura',
                        'full-access' => 'Acceso Completo',
                    ])
                    ->label('Alcance'),

                Tables\Filters\Filter::make('is_revoked')
                    ->label('Solo Revocadas')
                    ->query(fn (Builder $query): Builder => $query->where('is_revoked', true)),

                Tables\Filters\Filter::make('active')
                    ->label('Solo Activas')
                    ->query(fn (Builder $query): Builder => $query->where('is_revoked', false)
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                        })),

                Tables\Filters\Filter::make('expired')
                    ->label('Solo Expiradas')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now())),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiran Pronto (7 días)')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<=', now()->addDays(7))
                        ->where('expires_at', '>', now())),

                Tables\Filters\Filter::make('never_expires')
                    ->label('Nunca Expiran')
                    ->query(fn (Builder $query): Builder => $query->whereNull('expires_at')),

                Tables\Filters\Filter::make('high_rate_limit')
                    ->label('Límite Alto (5K+)')
                    ->query(fn (Builder $query): Builder => $query->where('rate_limit', '>=', 5000)),

                Tables\Filters\Filter::make('low_rate_limit')
                    ->label('Límite Bajo (<1K)')
                    ->query(fn (Builder $query): Builder => $query->where('rate_limit', '<', 1000)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),

                Tables\Actions\Action::make('revoke')
                    ->label('Revocar')
                    ->icon('heroicon-o-x-circle')
                    ->action(function (ApiKey $record): void {
                        $record->revoke();
                    })
                    ->color('danger')
                    ->visible(fn (ApiKey $record): bool => !$record->is_revoked),

                Tables\Actions\Action::make('activate')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (ApiKey $record): void {
                        $record->activate();
                    })
                    ->color('success')
                    ->visible(fn (ApiKey $record): bool => $record->is_revoked),

                Tables\Actions\Action::make('extend_expiration')
                    ->label('Extender Expiración')
                    ->icon('heroicon-o-clock')
                    ->action(function (ApiKey $record): void {
                        $record->extendExpiration(30);
                    })
                    ->color('info')
                    ->visible(fn (ApiKey $record): bool => $record->expires_at && $record->expires_at->isFuture()),

                Tables\Actions\Action::make('remove_expiration')
                    ->label('Quitar Expiración')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (ApiKey $record): void {
                        $record->removeExpiration();
                    })
                    ->color('primary')
                    ->visible(fn (ApiKey $record): bool => $record->expires_at !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('revoke_all')
                        ->label('Revocar Todas')
                        ->icon('heroicon-o-x-circle')
                        ->action(function ($records): void {
                            $records->each->revoke();
                        })
                        ->color('danger'),

                    Tables\Actions\BulkAction::make('activate_all')
                        ->label('Activar Todas')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records): void {
                            $records->each->activate();
                        })
                        ->color('success'),

                    Tables\Actions\BulkAction::make('extend_expiration_all')
                        ->label('Extender Expiración (30 días)')
                        ->icon('heroicon-o-clock')
                        ->action(function ($records): void {
                            $records->each->extendExpiration(30);
                        })
                        ->color('info'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
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
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
            'view' => Pages\ViewApiKey::route('/{record}'),
            'edit' => Pages\EditApiKey::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}