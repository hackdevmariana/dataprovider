<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectInvestmentResource\Pages;
use App\Models\ProjectInvestment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectInvestmentResource extends Resource
{
    protected static ?string $model = ProjectInvestment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-euro';

    protected static ?string $navigationGroup = 'Projects & Monetization';

    protected static ?string $modelLabel = 'Inversión';

    protected static ?string $pluralModelLabel = 'Inversiones';

    protected static ?int $navigationSort = 2;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Inversión')
                    ->schema([
                        Forms\Components\Select::make('project_proposal_id')
                            ->label('Proyecto')
                            ->relationship('projectProposal', 'title')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('investor_id')
                            ->label('Inversor')
                            ->relationship('investor', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Cantidad')
                            ->numeric()
                            ->prefix('€')
                            ->required(),
                        
                        Forms\Components\Select::make('investment_type')
                            ->label('Tipo de Inversión')
                            ->options([
                                'monetary' => 'Monetaria',
                                'equipment' => 'Equipamiento',
                                'service' => 'Servicio',
                                'labor' => 'Mano de Obra',
                                'mixed' => 'Mixta',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Condiciones')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'confirmed' => 'Confirmada',
                                'completed' => 'Completada',
                                'cancelled' => 'Cancelada',
                                'refunded' => 'Reembolsada',
                            ])
                            ->default('pending')
                            ->required(),
                        
                        Forms\Components\TextInput::make('expected_return_rate')
                            ->label('Tasa de Retorno Esperada (%)')
                            ->numeric()
                            ->suffix('%')
                            ->step(0.1),
                        
                        Forms\Components\TextInput::make('expected_return_period_months')
                            ->label('Período de Retorno (meses)')
                            ->numeric()
                            ->min(1),
                    ])->columns(3),

                Forms\Components\Section::make('Términos y Condiciones')
                    ->schema([
                        Forms\Components\Textarea::make('terms')
                            ->label('Términos')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('conditions')
                            ->label('Condiciones')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('invested_at')
                            ->label('Fecha de Inversión')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('confirmed_at')
                            ->label('Fecha de Confirmación'),
                        
                        Forms\Components\DateTimePicker::make('expected_return_date')
                            ->label('Fecha Esperada de Retorno'),
                    ])->columns(3),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->label('Público')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_anonymous')
                            ->label('Anónimo')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('accepts_partial_funding')
                            ->label('Acepta Financiación Parcial')
                            ->default(true),
                    ])->columns(3),
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
                
                Tables\Columns\TextColumn::make('investor.name')
                    ->label('Inversor')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Cantidad')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('investment_type')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'monetary',
                        'info' => 'equipment',
                        'warning' => 'service',
                        'primary' => 'labor',
                        'gray' => 'mixed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monetary' => 'Monetaria',
                        'equipment' => 'Equipamiento',
                        'service' => 'Servicio',
                        'labor' => 'Mano de Obra',
                        'mixed' => 'Mixta',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'refunded' => 'Reembolsada',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\TextColumn::make('expected_return_rate')
                    ->label('Retorno (%)')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('is_anonymous')
                    ->label('Anónimo')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('invested_at')
                    ->label('Fecha Inversión')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('confirmed_at')
                    ->label('Confirmada')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('No confirmada')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('investment_type')
                    ->options([
                        'monetary' => 'Monetaria',
                        'equipment' => 'Equipamiento',
                        'service' => 'Servicio',
                        'labor' => 'Mano de Obra',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Público'),
                
                Tables\Filters\Filter::make('large_investments')
                    ->label('Inversiones Grandes (>€10,000)')
                    ->query(fn ($query) => $query->where('amount', '>', 10000)),
                
                Tables\Filters\Filter::make('recent')
                    ->label('Recientes (30 días)')
                    ->query(fn ($query) => $query->where('invested_at', '>=', now()->subMonth())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (ProjectInvestment $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'confirmed_at' => now(),
                        ]);
                    })
                    ->visible(fn (ProjectInvestment $record) => $record->status === 'pending'),
                
                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (ProjectInvestment $record) => $record->update(['status' => 'cancelled']))
                    ->visible(fn (ProjectInvestment $record) => in_array($record->status, ['pending', 'confirmed'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('confirm_investments')
                        ->label('Confirmar Inversiones')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'confirmed',
                                        'confirmed_at' => now(),
                                    ]);
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('invested_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectInvestments::route('/'),
            'create' => Pages\CreateProjectInvestment::route('/create'),
            'edit' => Pages\EditProjectInvestment::route('/{record}/edit'),
        ];
    }
}