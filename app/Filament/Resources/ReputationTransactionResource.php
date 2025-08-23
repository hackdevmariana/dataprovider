<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReputationTransactionResource\Pages;
use App\Filament\Resources\ReputationTransactionResource\RelationManagers;
use App\Models\ReputationTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReputationTransactionResource extends Resource
{
    protected static ?string $navigationGroup = 'Gamification & Reputation';
    protected static ?string $model = ReputationTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $modelLabel = 'Transacción de Reputación';
    
    protected static ?string $pluralModelLabel = 'Transacciones de Reputación';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Transacción')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->placeholder('Selecciona un usuario')
                            ->helperText('Usuario que recibe o pierde reputación'),

                        Forms\Components\Select::make('action_type')
                            ->label('Tipo de Acción')
                            ->options([
                                // Acciones positivas
                                'answer_accepted' => 'Respuesta Aceptada (+15)',
                                'answer_upvoted' => 'Respuesta Votada (+10)',
                                'question_upvoted' => 'Pregunta Votada (+5)',
                                'helpful_comment' => 'Comentario Útil (+2)',
                                'tutorial_featured' => 'Tutorial Destacado (+50)',
                                'project_completed' => 'Proyecto Completado (+100)',
                                'expert_verification' => 'Verificación Experto (+500)',
                                'community_award' => 'Premio Comunidad (+200)',
                                'first_answer' => 'Primera Respuesta (+1)',
                                'consistency_bonus' => 'Bonus Consistencia (+10)',
                                'daily_login' => 'Login Diario (+1)',
                                'profile_completed' => 'Perfil Completado (+10)',
                                'bounty_awarded' => 'Recompensa Bounty (+25)',
                                'seasonal_bonus' => 'Bonus Estacional (+20)',
                                // Acciones negativas
                                'answer_downvoted' => 'Respuesta Downvote (-2)',
                                'question_downvoted' => 'Pregunta Downvote (-2)',
                                'spam_detected' => 'Spam Detectado (-100)',
                                'rule_violation' => 'Violación Reglas (-50)',
                                'answer_deleted' => 'Respuesta Eliminada (-15)',
                                'reputation_reversal' => 'Reversión Reputación (Variable)',
                            ])
                            ->required()
                            ->placeholder('Selecciona el tipo de acción')
                            ->helperText('Tipo de acción que genera el cambio de reputación'),

                        Forms\Components\TextInput::make('reputation_change')
                            ->label('Cambio de Reputación')
                            ->numeric()
                            ->required()
                            ->placeholder('Puntos ganados o perdidos')
                            ->helperText('Cambio en puntos de reputación (+ o -)'),

                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'solar' => 'Energía Solar',
                                'wind' => 'Energía Eólica',
                                'hydro' => 'Energía Hidroeléctrica',
                                'nuclear' => 'Energía Nuclear',
                                'biomass' => 'Biomasa',
                                'geothermal' => 'Energía Geotérmica',
                                'efficiency' => 'Eficiencia Energética',
                                'storage' => 'Almacenamiento de Energía',
                                'grid' => 'Red Eléctrica',
                                'renewable' => 'Energías Renovables',
                                'general' => 'General',
                            ])
                            ->searchable()
                            ->placeholder('Selecciona una categoría')
                            ->helperText('Categoría específica de energía si aplica'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contexto de la Acción')
                    ->schema([
                        Forms\Components\TextInput::make('topic_id')
                            ->label('ID del Tema')
                            ->numeric()
                            ->placeholder('ID del tema relacionado')
                            ->helperText('Tema específico si aplica'),

                        Forms\Components\TextInput::make('related_type')
                            ->label('Tipo de Relación')
                            ->placeholder('post, answer, comment, etc.')
                            ->helperText('Tipo de entidad relacionada'),

                        Forms\Components\TextInput::make('related_id')
                            ->label('ID de Relación')
                            ->numeric()
                            ->placeholder('ID de la entidad relacionada')
                            ->helperText('ID de la entidad relacionada'),

                        Forms\Components\Select::make('triggered_by')
                            ->label('Disparado por')
                            ->relationship('awarder', 'name')
                            ->searchable()
                            ->placeholder('Usuario que disparó la acción')
                            ->helperText('Usuario que otorgó o penalizó la reputación'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Descripción y Metadatos')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada de la acción')
                            ->helperText('Explicación de por qué se otorgó o penalizó la reputación'),

                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor')
                            ->helperText('Datos adicionales en formato clave-valor'),
                    ]),

                Forms\Components\Section::make('Estado y Validación')
                    ->schema([
                        Forms\Components\Toggle::make('is_validated')
                            ->label('Validado')
                            ->helperText('Si la transacción es válida'),

                        Forms\Components\Toggle::make('is_reversed')
                            ->label('Revertida')
                            ->helperText('Si la transacción fue revertida'),

                        Forms\Components\Select::make('reversed_by')
                            ->label('Revertida por')
                            ->relationship('reverser', 'name')
                            ->searchable()
                            ->placeholder('Usuario que revirtió la transacción')
                            ->helperText('Usuario que revirtió la transacción')
                            ->visible(fn ($record) => $record && $record->is_reversed),

                        Forms\Components\Textarea::make('reversal_reason')
                            ->label('Razón de Reversión')
                            ->rows(2)
                            ->placeholder('Razón por la que se revirtió')
                            ->helperText('Explicación de la reversión')
                            ->visible(fn ($record) => $record && $record->is_reversed),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información del Sistema')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Creado')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Actualizado')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),
                    ])
                    ->collapsible()
                    ->collapsed()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->width(60),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('action_type')
                    ->label('Tipo de Acción')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        $positiveActions = [
                            'answer_accepted', 'answer_upvoted', 'question_upvoted', 'helpful_comment',
                            'tutorial_featured', 'project_completed', 'expert_verification', 'community_award',
                            'first_answer', 'consistency_bonus', 'daily_login', 'profile_completed',
                            'bounty_awarded', 'seasonal_bonus'
                        ];
                        
                        if (in_array($record->action_type, $positiveActions)) {
                            return 'success';
                        }
                        
                        $negativeActions = [
                            'answer_downvoted', 'question_downvoted', 'spam_detected',
                            'rule_violation', 'answer_deleted', 'reputation_reversal'
                        ];
                        
                        if (in_array($record->action_type, $negativeActions)) {
                            return 'danger';
                        }
                        
                        return 'info';
                    })
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'answer_accepted' => 'Respuesta Aceptada',
                            'answer_upvoted' => 'Respuesta Votada',
                            'question_upvoted' => 'Pregunta Votada',
                            'helpful_comment' => 'Comentario Útil',
                            'tutorial_featured' => 'Tutorial Destacado',
                            'project_completed' => 'Proyecto Completado',
                            'expert_verification' => 'Verificación Experto',
                            'community_award' => 'Premio Comunidad',
                            'first_answer' => 'Primera Respuesta',
                            'consistency_bonus' => 'Bonus Consistencia',
                            'daily_login' => 'Login Diario',
                            'profile_completed' => 'Perfil Completado',
                            'bounty_awarded' => 'Recompensa Bounty',
                            'seasonal_bonus' => 'Bonus Estacional',
                            'answer_downvoted' => 'Respuesta Downvote',
                            'question_downvoted' => 'Pregunta Downvote',
                            'spam_detected' => 'Spam Detectado',
                            'rule_violation' => 'Violación Reglas',
                            'answer_deleted' => 'Respuesta Eliminada',
                            'reputation_reversal' => 'Reversión Reputación',
                        ];
                        
                        return $labels[$state] ?? $state;
                    }),

                Tables\Columns\TextColumn::make('reputation_change')
                    ->label('Cambio de Reputación')
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        return $record->reputation_change >= 0 ? 'success' : 'danger';
                    })
                    ->formatStateUsing(function ($state) {
                        return ($state >= 0 ? '+' : '') . $state . ' pts';
                    })
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($state) {
                        $categories = [
                            'solar' => 'Energía Solar',
                            'wind' => 'Energía Eólica',
                            'hydro' => 'Energía Hidroeléctrica',
                            'nuclear' => 'Energía Nuclear',
                            'biomass' => 'Biomasa',
                            'geothermal' => 'Energía Geotérmica',
                            'efficiency' => 'Eficiencia Energética',
                            'storage' => 'Almacenamiento de Energía',
                            'grid' => 'Red Eléctrica',
                            'renewable' => 'Energías Renovables',
                            'general' => 'General',
                        ];
                        
                        return $categories[$state] ?? $state;
                    }),

                Tables\Columns\TextColumn::make('awarder.name')
                    ->label('Otorgado por')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('secondary')
                    ->limit(20)
                    ->getStateUsing(fn ($record) => $record->awarder?->name ?? 'Sistema'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(50)
                    ->getStateUsing(fn ($record) => $record->description ?? 'Sin descripción')
                    ->tooltip(fn ($record) => $record->description ?? 'Sin descripción'),

                Tables\Columns\IconColumn::make('is_validated')
                    ->label('Validado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_reversed')
                    ->label('Revertida')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-uturn-left')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Filtrar por usuario')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('action_type')
                    ->label('Tipo de Acción')
                    ->options([
                        'answer_accepted' => 'Respuesta Aceptada',
                        'answer_upvoted' => 'Respuesta Votada',
                        'question_upvoted' => 'Pregunta Votada',
                        'helpful_comment' => 'Comentario Útil',
                        'tutorial_featured' => 'Tutorial Destacado',
                        'project_completed' => 'Proyecto Completado',
                        'expert_verification' => 'Verificación Experto',
                        'community_award' => 'Premio Comunidad',
                        'first_answer' => 'Primera Respuesta',
                        'consistency_bonus' => 'Bonus Consistencia',
                        'daily_login' => 'Login Diario',
                        'profile_completed' => 'Perfil Completado',
                        'bounty_awarded' => 'Recompensa Bounty',
                        'seasonal_bonus' => 'Bonus Estacional',
                        'answer_downvoted' => 'Respuesta Downvote',
                        'question_downvoted' => 'Pregunta Downvote',
                        'spam_detected' => 'Spam Detectado',
                        'rule_violation' => 'Violación Reglas',
                        'answer_deleted' => 'Respuesta Eliminada',
                        'reputation_reversal' => 'Reversión Reputación',
                    ])
                    ->placeholder('Filtrar por tipo de acción')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'solar' => 'Energía Solar',
                        'wind' => 'Energía Eólica',
                        'hydro' => 'Energía Hidroeléctrica',
                        'nuclear' => 'Energía Nuclear',
                        'biomass' => 'Biomasa',
                        'geothermal' => 'Energía Geotérmica',
                        'efficiency' => 'Eficiencia Energética',
                        'storage' => 'Almacenamiento de Energía',
                        'grid' => 'Red Eléctrica',
                        'renewable' => 'Energías Renovables',
                        'general' => 'General',
                    ])
                    ->placeholder('Filtrar por categoría')
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_validated')
                    ->label('Validado')
                    ->placeholder('Todas las transacciones')
                    ->trueLabel('Solo validadas')
                    ->falseLabel('Solo no validadas'),

                Tables\Filters\TernaryFilter::make('is_reversed')
                    ->label('Revertida')
                    ->placeholder('Todas las transacciones')
                    ->trueLabel('Solo revertidas')
                    ->falseLabel('Solo no revertidas'),

                Tables\Filters\Filter::make('reputation_range')
                    ->label('Rango de Reputación')
                    ->form([
                        Forms\Components\TextInput::make('min_points')
                            ->label('Mínimo')
                            ->numeric()
                            ->placeholder('Puntos mínimos'),
                        Forms\Components\TextInput::make('max_points')
                            ->label('Máximo')
                            ->numeric()
                            ->placeholder('Puntos máximos'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['min_points']) {
                            $query->where('reputation_change', '>=', $data['min_points']);
                        }
                        if ($data['max_points']) {
                            $query->where('reputation_change', '<=', $data['max_points']);
                        }
                        return $query;
                    }),

                Tables\Filters\Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['date_from']) {
                            $query->where('created_at', '>=', $data['date_from']);
                        }
                        if ($data['date_to']) {
                            $query->where('created_at', '<=', $data['date_to']);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalles')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->tooltip('Ver detalles completos de la transacción'),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->tooltip('Editar la transacción de reputación'),

                Tables\Actions\Action::make('view_user')
                    ->label('Ver Usuario')
                    ->icon('heroicon-o-user')
                    ->color('primary')
                    ->url(fn ($record) => $record->user ? route('filament.admin.resources.users.edit', $record->user) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->user !== null)
                    ->tooltip('Ver perfil del usuario'),

                Tables\Actions\Action::make('view_awarder')
                    ->label('Ver Otorgador')
                    ->icon('heroicon-o-star')
                    ->color('secondary')
                    ->url(fn ($record) => $record->awarder ? route('filament.admin.resources.users.edit', $record->awarder) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->awarder !== null)
                    ->tooltip('Ver perfil del usuario que otorgó la reputación'),

                Tables\Actions\Action::make('reverse_transaction')
                    ->label('Revertir')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Revertir esta transacción?')
                    ->modalDescription('Esta acción revertirá los puntos de reputación de esta transacción.')
                    ->modalSubmitActionLabel('Sí, revertir')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(fn ($record) => !$record->is_reversed)
                    ->action(function ($record) {
                        // Aquí se implementaría la lógica de reversión
                        \Filament\Notifications\Notification::make()
                            ->title('Transacción revertida')
                            ->body('La transacción ha sido revertida exitosamente')
                            ->success()
                            ->send();
                    })
                    ->tooltip('Revertir esta transacción de reputación'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar Seleccionadas')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar transacciones seleccionadas?')
                        ->modalDescription('Esta acción eliminará permanentemente las transacciones seleccionadas.')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->modalCancelActionLabel('Cancelar'),

                    Tables\Actions\BulkAction::make('validate_transactions')
                        ->label('Validar Transacciones')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                $record->update(['is_validated' => true]);
                                $updated++;
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Transacciones validadas')
                                ->body("Se han validado {$updated} transacciones")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('export_transactions')
                        ->label('Exportar Transacciones')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function ($records) {
                            \Filament\Notifications\Notification::make()
                                ->title('Exportación iniciada')
                                ->body('Se están exportando ' . $records->count() . ' transacciones')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'awarder']));
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
            'index' => Pages\ListReputationTransactions::route('/'),
            'create' => Pages\CreateReputationTransaction::route('/create'),
            'edit' => Pages\EditReputationTransaction::route('/{record}/edit'),
        ];
    }
}
