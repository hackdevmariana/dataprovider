<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-euro';

    protected static ?string $navigationGroup = 'Proyectos y Monetización';

    protected static ?string $modelLabel = 'Pago';

    protected static ?string $pluralModelLabel = 'Pagos';

    protected static ?int $navigationSort = 7;

    
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
                        
                        Forms\Components\TextInput::make('payment_intent_id')
                            ->label('ID de Intención de Pago')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                'completed' => 'Completado',
                                'failed' => 'Fallido',
                                'cancelled' => 'Cancelado',
                                'refunded' => 'Reembolsado',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'subscription' => 'Suscripción',
                                'commission' => 'Comisión',
                                'verification' => 'Verificación',
                                'consultation' => 'Consultoría',
                                'refund' => 'Reembolso',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Montos')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Cantidad')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->required(),
                        
                        Forms\Components\TextInput::make('fee')
                            ->label('Tarifa del Procesador')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->default(0),
                        
                        Forms\Components\TextInput::make('net_amount')
                            ->label('Cantidad Neta')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('currency')
                            ->label('Moneda')
                            ->default('EUR')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Procesador de Pagos')
                    ->schema([
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Método de Pago')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('processor')
                            ->label('Procesador')
                            ->options([
                                'stripe' => 'Stripe',
                                'paypal' => 'PayPal',
                                'bank' => 'Transferencia Bancaria',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('Procesado en'),
                        
                        Forms\Components\DateTimePicker::make('failed_at')
                            ->label('Falló en'),
                        
                        Forms\Components\DateTimePicker::make('refunded_at')
                            ->label('Reembolsado en'),
                    ])->columns(3),

                Forms\Components\Section::make('Descripción y Notas')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('failure_reason')
                            ->label('Razón de Fallo')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_intent_id')
                    ->label('ID de Pago')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'subscription',
                        'success' => 'commission',
                        'warning' => 'verification',
                        'danger' => 'consultation',
                        'secondary' => 'refund',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'subscription' => 'Suscripción',
                        'commission' => 'Comisión',
                        'verification' => 'Verificación',
                        'consultation' => 'Consultoría',
                        'refund' => 'Reembolso',
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'secondary' => 'cancelled',
                        'purple' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'processing' => 'Procesando',
                        'completed' => 'Completado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    }),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Cantidad')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fee')
                    ->label('Tarifa')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Neto')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('processor')
                    ->label('Procesador')
                    ->colors([
                        'primary' => 'stripe',
                        'warning' => 'paypal',
                        'success' => 'bank',
                    ]),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método')
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Procesado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'subscription' => 'Suscripción',
                        'commission' => 'Comisión',
                        'verification' => 'Verificación',
                        'consultation' => 'Consultoría',
                        'refund' => 'Reembolso',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'processing' => 'Procesando',
                        'completed' => 'Completado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    ]),
                
                Tables\Filters\SelectFilter::make('processor')
                    ->label('Procesador')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'bank' => 'Transferencia Bancaria',
                    ]),
                
                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
                
                Tables\Filters\Filter::make('this_week')
                    ->label('Esta semana')
                    ->query(fn ($query) => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])),
                
                Tables\Filters\Filter::make('this_month')
                    ->label('Este mes')
                    ->query(fn ($query) => $query->whereMonth('created_at', now()->month)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_completed')
                    ->label('Marcar Completado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Payment $record) => $record->markAsCompleted())
                    ->visible(fn (Payment $record) => in_array($record->status, ['pending', 'processing'])),
                
                Tables\Actions\Action::make('mark_failed')
                    ->label('Marcar Fallido')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('failure_reason')
                            ->label('Razón del Fallo')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Payment $record, array $data) {
                        $record->markAsFailed($data['failure_reason']);
                    })
                    ->visible(fn (Payment $record) => in_array($record->status, ['pending', 'processing'])),
                
                Tables\Actions\Action::make('refund')
                    ->label('Reembolsar')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('purple')
                    ->requiresConfirmation()
                    ->action(fn (Payment $record) => $record->markAsRefunded())
                    ->visible(fn (Payment $record) => $record->status === 'completed'),
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
                Infolists\Components\Section::make('Información del Pago')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_intent_id')
                            ->label('ID de Intención de Pago'),
                        
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario'),
                        
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'subscription' => 'primary',
                                'commission' => 'success',
                                'verification' => 'warning',
                                'consultation' => 'danger',
                                'refund' => 'secondary',
                            }),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processing' => 'primary',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'cancelled' => 'secondary',
                                'refunded' => 'purple',
                            }),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),
                    ])->columns(2),

                Infolists\Components\Section::make('Información Financiera')
                    ->schema([
                        Infolists\Components\TextEntry::make('amount')
                            ->label('Cantidad')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('fee')
                            ->label('Tarifa del Procesador')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('net_amount')
                            ->label('Cantidad Neta')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('currency')
                            ->label('Moneda'),
                    ])->columns(2),

                Infolists\Components\Section::make('Procesador de Pagos')
                    ->schema([
                        Infolists\Components\TextEntry::make('processor')
                            ->label('Procesador')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'stripe' => 'primary',
                                'paypal' => 'warning',
                                'bank' => 'success',
                            }),
                        
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Método de Pago'),
                    ])->columns(2),

                Infolists\Components\Section::make('Fechas Importantes')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Creado')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('processed_at')
                            ->label('Procesado')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('failed_at')
                            ->label('Falló')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('refunded_at')
                            ->label('Reembolsado')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('Información Adicional')
                    ->schema([
                        Infolists\Components\TextEntry::make('failure_reason')
                            ->label('Razón de Fallo')
                            ->visible(fn ($record) => $record->status === 'failed'),
                        
                        Infolists\Components\TextEntry::make('payable_type')
                            ->label('Tipo de Elemento')
                            ->formatStateUsing(fn ($state) => class_basename($state)),
                        
                        Infolists\Components\TextEntry::make('payable_id')
                            ->label('ID del Elemento'),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            // Podríamos añadir widgets para estadísticas de pagos
        ];
    }
}