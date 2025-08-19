<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCommissionResource\Pages;
use App\Models\ProjectCommission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ProjectCommissionResource extends Resource
{
    protected static ?string $model = ProjectCommission::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Monetización';

    protected static ?string $modelLabel = 'Comisión de Proyecto';

    protected static ?string $pluralModelLabel = 'Comisiones de Proyectos';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('project_proposal_id')
                            ->label('Proyecto')
                            ->relationship('projectProposal', 'title')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'success_fee' => 'Comisión de éxito',
                                'listing_fee' => 'Tarifa de listado',
                                'verification_fee' => 'Tarifa de verificación',
                                'premium_fee' => 'Tarifa premium',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'paid' => 'Pagada',
                                'waived' => 'Exonerada',
                                'disputed' => 'En disputa',
                                'refunded' => 'Reembolsada',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Montos y Cálculos')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Cantidad de Comisión')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->required(),
                        
                        Forms\Components\TextInput::make('rate')
                            ->label('Tasa')
                            ->numeric()
                            ->step(0.0001)
                            ->suffix('%')
                            ->helperText('Ejemplo: 0.05 = 5%')
                            ->required(),
                        
                        Forms\Components\TextInput::make('base_amount')
                            ->label('Cantidad Base')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->required(),
                        
                        Forms\Components\TextInput::make('currency')
                            ->label('Moneda')
                            ->default('EUR')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas y Pago')
                    ->schema([
                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Fecha de Vencimiento')
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Fecha de Pago'),
                        
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Método de Pago')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ID de Transacción')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Descripción y Notas')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('projectProposal.title')
                    ->label('Proyecto')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'success_fee',
                        'primary' => 'listing_fee',
                        'warning' => 'verification_fee',
                        'danger' => 'premium_fee',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success_fee' => 'Éxito',
                        'listing_fee' => 'Listado',
                        'verification_fee' => 'Verificación',
                        'premium_fee' => 'Premium',
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'primary' => 'waived',
                        'danger' => 'disputed',
                        'secondary' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'paid' => 'Pagada',
                        'waived' => 'Exonerada',
                        'disputed' => 'Disputa',
                        'refunded' => 'Reembolsada',
                    }),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Cantidad')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('rate')
                    ->label('Tasa')
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%'),
                
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimiento')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Pagado')
                    ->date()
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
                    ->label('Tipo')
                    ->options([
                        'success_fee' => 'Comisión de éxito',
                        'listing_fee' => 'Tarifa de listado',
                        'verification_fee' => 'Tarifa de verificación',
                        'premium_fee' => 'Tarifa premium',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagada',
                        'waived' => 'Exonerada',
                        'disputed' => 'En disputa',
                        'refunded' => 'Reembolsada',
                    ]),
                
                Tables\Filters\Filter::make('overdue')
                    ->label('Vencidas')
                    ->query(fn ($query) => $query->where('status', 'pending')->where('due_date', '<', now())),
                
                Tables\Filters\Filter::make('due_soon')
                    ->label('Próximas a vencer')
                    ->query(fn ($query) => $query->where('status', 'pending')->whereBetween('due_date', [now(), now()->addDays(7)])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Marcar como Pagada')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (ProjectCommission $record) => $record->markAsPaid())
                    ->visible(fn (ProjectCommission $record) => $record->status === 'pending'),
                
                Tables\Actions\Action::make('waive')
                    ->label('Exonerar')
                    ->icon('heroicon-o-gift')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(fn (ProjectCommission $record) => $record->update(['status' => 'waived']))
                    ->visible(fn (ProjectCommission $record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('due_date', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Comisión')
                    ->schema([
                        Infolists\Components\TextEntry::make('projectProposal.title')
                            ->label('Proyecto'),
                        
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario'),
                        
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'success_fee' => 'success',
                                'listing_fee' => 'primary',
                                'verification_fee' => 'warning',
                                'premium_fee' => 'danger',
                            }),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'waived' => 'primary',
                                'disputed' => 'danger',
                                'refunded' => 'secondary',
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('Cálculos Financieros')
                    ->schema([
                        Infolists\Components\TextEntry::make('base_amount')
                            ->label('Cantidad Base')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('rate')
                            ->label('Tasa')
                            ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%'),
                        
                        Infolists\Components\TextEntry::make('amount')
                            ->label('Comisión')
                            ->money('EUR'),
                        
                        Infolists\Components\TextEntry::make('days_until_due')
                            ->label('Días hasta vencimiento')
                            ->formatStateUsing(fn ($record) => $record->getDaysUntilDue() . ' días'),
                    ])->columns(2),

                Infolists\Components\Section::make('Información de Pago')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Método de Pago'),
                        
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label('ID de Transacción'),
                        
                        Infolists\Components\TextEntry::make('paid_at')
                            ->label('Fecha de Pago')
                            ->dateTime(),
                    ])->columns(3),

                Infolists\Components\Section::make('Descripción y Notas')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),
                        
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas'),
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
            'index' => Pages\ListProjectCommissions::route('/'),
            'create' => Pages\CreateProjectCommission::route('/create'),
            'edit' => Pages\EditProjectCommission::route('/{record}/edit'),
        ];
    }
}