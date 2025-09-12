<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserGeneratedContentResource\Pages;
use App\Filament\Resources\UserGeneratedContentResource\RelationManagers;
use App\Models\UserGeneratedContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class UserGeneratedContentResource extends Resource
{
    protected static ?string $model = UserGeneratedContent::class;

    protected static ?string $navigationIcon = 'fas-comments';
    
    protected static ?string $navigationGroup = 'Content & Media';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $modelLabel = 'Contenido de Usuario';
    
    protected static ?string $pluralModelLabel = 'Contenido de Usuarios';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('content_type')
                    ->label('Tipo')
                    ->options([
                        'comment' => 'Comentario',
                        'suggestion' => 'Sugerencia',
                        'photo' => 'Foto',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->label('Contenido')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ])
                    ->required(),
                Forms\Components\Select::make('visibility')
                    ->label('Visibilidad')
                    ->options([
                        'public' => 'Público',
                        'private' => 'Privado',
                        'unlisted' => 'No listado',
                    ])
                    ->default('public'),
                Forms\Components\TextInput::make('rating')
                    ->label('Calificación')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->step(0.1),
                Forms\Components\Toggle::make('is_verified')
                    ->label('Verificado'),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Destacado'),
                Forms\Components\Toggle::make('is_spam')
                    ->label('Es Spam'),
                Forms\Components\Toggle::make('needs_moderation')
                    ->label('Necesita Moderación'),
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
                Tables\Columns\TextColumn::make('content_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'comment' => 'info',
                        'suggestion' => 'warning',
                        'photo' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'comment' => 'Comentario',
                        'suggestion' => 'Sugerencia',
                        'photo' => 'Foto',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('content')
                    ->label('Contenido')
                    ->limit(80)
                    ->wrap()
                    ->tooltip(function (UserGeneratedContent $record): string {
                        return $record->content;
                    })
                    ->formatStateUsing(function (UserGeneratedContent $record): string {
                        $content = $record->content;
                        $words = explode(' ', $content);
                        $firstWords = array_slice($words, 0, 15);
                        return implode(' ', $firstWords) . (count($words) > 15 ? '...' : '');
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('related_info')
                    ->label('Relacionado con')
                    ->formatStateUsing(function (UserGeneratedContent $record): string {
                        if (!$record->related_type || !$record->related_id) {
                            return 'Sin relación';
                        }
                        
                        $type = class_basename($record->related_type);
                        return $type . ' #' . $record->related_id;
                    })
                    ->tooltip(function (UserGeneratedContent $record): string {
                        if (!$record->related_type || !$record->related_id) {
                            return 'Sin relación';
                        }
                        
                        return 'Tipo: ' . $record->related_type . "\nID: " . $record->related_id;
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Calificación')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('sentiment_label')
                    ->label('Sentimiento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'positivo' => 'success',
                        'neutral' => 'info',
                        'negativo' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'positivo' => 'Positivo',
                        'neutral' => 'Neutral',
                        'negativo' => 'Negativo',
                        default => ucfirst($state ?? 'Sin datos'),
                    }),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_anonymous')
                    ->label('Anónimo')
                    ->boolean()
                    ->trueIcon('fas-user-secret')
                    ->falseIcon('fas-user')
                    ->trueColor('warning')
                    ->falseColor('success'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Respuestas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('content_type')
                    ->label('Tipo de Contenido')
                    ->options([
                        'comment' => 'Comentario',
                        'suggestion' => 'Sugerencia',
                        'photo' => 'Foto',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ]),
                Tables\Filters\SelectFilter::make('visibility')
                    ->label('Visibilidad')
                    ->options([
                        'public' => 'Público',
                        'private' => 'Privado',
                        'unlisted' => 'No listado',
                    ]),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
                Tables\Filters\TernaryFilter::make('is_spam')
                    ->label('Es Spam'),
                Tables\Filters\TernaryFilter::make('needs_moderation')
                    ->label('Necesita Moderación'),
                Tables\Filters\SelectFilter::make('sentiment_label')
                    ->label('Sentimiento')
                    ->options([
                        'positivo' => 'Positivo',
                        'neutral' => 'Neutral',
                        'negativo' => 'Negativo',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('fas-check-circle')
                    ->color('success')
                    ->visible(fn (UserGeneratedContent $record): bool => $record->status === 'pending')
                    ->action(function (UserGeneratedContent $record): void {
                        $record->update(['status' => 'approved']);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('fas-times-circle')
                    ->color('danger')
                    ->visible(fn (UserGeneratedContent $record): bool => $record->status === 'pending')
                    ->action(function (UserGeneratedContent $record): void {
                        $record->update(['status' => 'rejected']);
                    }),
                Tables\Actions\Action::make('feature')
                    ->label('Destacar')
                    ->icon('fas-star')
                    ->color('warning')
                    ->visible(fn (UserGeneratedContent $record): bool => !$record->is_featured)
                    ->action(function (UserGeneratedContent $record): void {
                        $record->update(['is_featured' => true]);
                    }),
                Tables\Actions\Action::make('unfeature')
                    ->label('Quitar Destacado')
                    ->icon('fas-star')
                    ->color('gray')
                    ->visible(fn (UserGeneratedContent $record): bool => $record->is_featured)
                    ->action(function (UserGeneratedContent $record): void {
                        $record->update(['is_featured' => false]);
                    }),
                Tables\Actions\Action::make('mark_spam')
                    ->label('Marcar como Spam')
                    ->icon('fas-ban')
                    ->color('danger')
                    ->visible(fn (UserGeneratedContent $record): bool => !$record->is_spam)
                    ->action(function (UserGeneratedContent $record): void {
                        $record->update([
                            'is_spam' => true,
                            'status' => 'rejected',
                            'needs_moderation' => false,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Aprobar Seleccionados')
                        ->icon('fas-check-circle')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $records->each(fn (UserGeneratedContent $record) => $record->update(['status' => 'approved']));
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Rechazar Seleccionados')
                        ->icon('fas-times-circle')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $records->each(fn (UserGeneratedContent $record) => $record->update(['status' => 'rejected']));
                        }),
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Destacar Seleccionados')
                        ->icon('fas-star')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $records->each(fn (UserGeneratedContent $record) => $record->update(['is_featured' => true]));
                        }),
                    Tables\Actions\BulkAction::make('mark_spam')
                        ->label('Marcar como Spam')
                        ->icon('fas-ban')
                        ->color('danger')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $records->each(fn (UserGeneratedContent $record) => $record->update([
                                'is_spam' => true,
                                'status' => 'rejected',
                                'needs_moderation' => false,
                            ]));
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user']));
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
            'index' => Pages\ListUserGeneratedContents::route('/'),
            'create' => Pages\CreateUserGeneratedContent::route('/create'),
            'edit' => Pages\EditUserGeneratedContent::route('/{record}/edit'),
        ];
    }

    /**
     * Obtener estadísticas del contenido generado por usuarios.
     */
    public static function getStats(): array
    {
        $total = UserGeneratedContent::count();
        $pending = UserGeneratedContent::where('status', 'pending')->count();
        $approved = UserGeneratedContent::where('status', 'approved')->count();
        $rejected = UserGeneratedContent::where('status', 'rejected')->count();
        $spam = UserGeneratedContent::where('is_spam', true)->count();
        $featured = UserGeneratedContent::where('is_featured', true)->count();
        $anonymous = UserGeneratedContent::where('is_anonymous', true)->count();
        $verified = UserGeneratedContent::where('is_verified', true)->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'spam' => $spam,
            'featured' => $featured,
            'anonymous' => $anonymous,
            'verified' => $verified,
        ];
    }
}
