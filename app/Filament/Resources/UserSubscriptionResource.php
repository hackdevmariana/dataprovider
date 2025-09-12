<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserSubscriptionResource\Pages;
use App\Models\UserSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class UserSubscriptionResource extends Resource
{
    protected static ?string $model = UserSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Projects & Monetization';

    protected static ?string $modelLabel = 'Suscripción de Usuario';

    protected static ?string $pluralModelLabel = 'Suscripciones de Usuarios';

    protected static ?int $navigationSort = 2;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('subscription_plan_id')
                            ->label('Plan de Suscripción')
                            ->relationship('subscriptionPlan', 'name')
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activa',
                                'cancelled' => 'Cancelada',
                                'expired' => 'Expirada',
                                'trial' => 'Período de prueba',
                                'suspended' => 'Suspendida',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Cantidad Pagada')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->required(),
                        
                        Forms\Components\Select::make('billing_cycle')
                            ->label('Ciclo de Facturación')
                            ->options([
                                'monthly' => 'Mensual',
                                'yearly' => 'Anual',
                                'one_time' => 'Pago único',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Fecha de Inicio')
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Fecha de Fin'),
                        
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Fin del Período de Prueba'),
                        
                        Forms\Components\DateTimePicker::make('next_billing_at')
                            ->label('Próxima Facturación'),
                        
                        Forms\Components\DateTimePicker::make('cancelled_at')
                            ->label('Fecha de Cancelación'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Método de Pago')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('external_subscription_id')
                            ->label('ID Externo de Suscripción')
                            ->maxLength(255),
                        
                        Forms\Components\Toggle::make('auto_renew')
                            ->label('Renovación Automática')
                            ->default(true),
                        
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('Razón de Cancelación')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('subscriptionPlan.name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'primary' => 'trial',
                        'danger' => 'cancelled',
                        'secondary' => 'expired',
                        'warning' => 'suspended',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activa',
                        'cancelled' => 'Cancelada',
                        'expired' => 'Expirada',
                        'trial' => 'Prueba',
                        'suspended' => 'Suspendida',
                    }),
                
                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Cantidad Pagada')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Ciclo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'Mensual',
                        'yearly' => 'Anual',
                        'one_time' => 'Único',
                    }),
                
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('next_billing_at')
                    ->label('Próxima Facturación')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('auto_renew')
                    ->label('Auto-renovación')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'cancelled' => 'Cancelada',
                        'expired' => 'Expirada',
                        'trial' => 'Período de prueba',
                        'suspended' => 'Suspendida',
                    ]),
                
                Tables\Filters\SelectFilter::make('subscription_plan_id')
                    ->label('Plan')
                    ->relationship('subscriptionPlan', 'name'),
                
                Tables\Filters\SelectFilter::make('billing_cycle')
                    ->label('Ciclo de Facturación')
                    ->options([
                        'monthly' => 'Mensual',
                        'yearly' => 'Anual',
                        'one_time' => 'Pago único',
                    ]),
                
                Tables\Filters\TernaryFilter::make('auto_renew')
                    ->label('Renovación Automática'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (UserSubscription $record) => $record->cancel('Cancelada desde admin'))
                    ->visible(fn (UserSubscription $record) => $record->status === 'active'),
                
                Tables\Actions\Action::make('reactivate')
                    ->label('Reactivar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (UserSubscription $record) => $record->reactivate())
                    ->visible(fn (UserSubscription $record) => $record->status === 'cancelled'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Suscripción')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario'),
                        
                        Infolists\Components\TextEntry::make('subscriptionPlan.name')
                            ->label('Plan'),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'trial' => 'primary',
                                'cancelled' => 'danger',
                                'expired' => 'secondary',
                                'suspended' => 'warning',
                            }),
                        
                        Infolists\Components\TextEntry::make('amount_paid')
                            ->label('Cantidad Pagada')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('billing_cycle')
                            ->label('Ciclo de Facturación')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'monthly' => 'Mensual',
                                'yearly' => 'Anual',
                                'one_time' => 'Pago único',
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('Fechas Importantes')
                    ->schema([
                        Infolists\Components\TextEntry::make('starts_at')
                            ->label('Fecha de Inicio')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('ends_at')
                            ->label('Fecha de Fin')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('next_billing_at')
                            ->label('Próxima Facturación')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('days_remaining')
                            ->label('Días Restantes')
                            ->formatStateUsing(fn ($record) => $record->daysRemaining() . ' días'),
                    ])->columns(2),

                Infolists\Components\Section::make('Configuración de Pago')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Método de Pago'),
                        
                        Infolists\Components\TextEntry::make('external_subscription_id')
                            ->label('ID Externo'),
                        
                        Infolists\Components\IconEntry::make('auto_renew')
                            ->label('Renovación Automática')
                            ->boolean(),
                    ])->columns(3),
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
            'index' => Pages\ListUserSubscriptions::route('/'),
            'create' => Pages\CreateUserSubscription::route('/create'),
            'edit' => Pages\EditUserSubscription::route('/{record}/edit'),
        ];
    }
}