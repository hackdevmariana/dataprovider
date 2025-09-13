<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsoredContentResource\Pages;
use App\Filament\Resources\SponsoredContentResource\RelationManagers;
use App\Models\SponsoredContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class SponsoredContentResource extends Resource
{
    protected static ?string $navigationGroup = 'Content & Media';
    protected static ?string $model = SponsoredContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Patrocinador')
                    ->schema([
                        Forms\Components\Select::make('sponsor_id')
                            ->label('Patrocinador')
                            ->relationship('sponsor', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('sponsorable_type')
                            ->label('Tipo de Contenido Patrocinado')
                            ->options([
                                'App\Models\TopicPost' => 'Post de Tema',
                                'App\Models\Event' => 'Evento',
                                'App\Models\Cooperative' => 'Cooperativa',
                                'App\Models\NewsArticle' => 'Artículo de Noticias',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('sponsorable_id')
                            ->label('Contenido Específico')
                            ->options(function (callable $get) {
                                $type = $get('sponsorable_type');
                                if (!$type) return [];
                                
                                return match ($type) {
                                    'App\Models\TopicPost' => \App\Models\TopicPost::pluck('title', 'id')->toArray(),
                                    'App\Models\Event' => \App\Models\Event::pluck('name', 'id')->toArray(),
                                    'App\Models\Cooperative' => \App\Models\Cooperative::pluck('name', 'id')->toArray(),
                                    'App\Models\NewsArticle' => \App\Models\NewsArticle::pluck('title', 'id')->toArray(),
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->required(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Configuración de la Campaña')
                    ->schema([
                        Forms\Components\TextInput::make('campaign_name')
                            ->label('Nombre de la Campaña')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('campaign_description')
                            ->label('Descripción de la Campaña')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\Select::make('content_type')
                            ->label('Tipo de Contenido')
                            ->options([
                                'promoted_post' => 'Post Promocionado',
                                'banner_ad' => 'Banner Publicitario',
                                'sponsored_topic' => 'Tema Patrocinado',
                                'product_placement' => 'Placement de Producto',
                                'native_content' => 'Contenido Nativo',
                                'event_promotion' => 'Promoción de Evento',
                                'job_posting' => 'Oferta de Trabajo',
                                'service_highlight' => 'Destacar Servicio',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'pending_review' => 'Pendiente de Revisión',
                                'approved' => 'Aprobado',
                                'active' => 'Activo',
                                'paused' => 'Pausado',
                                'completed' => 'Completado',
                                'rejected' => 'Rechazado',
                                'expired' => 'Expirado',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Targeting y Audiencia')
                    ->schema([
                        Forms\Components\TagsInput::make('target_audience')
                            ->label('Audiencia Objetivo')
                            ->separator(',')
                            ->suggestions([
                                'profesionales_energia',
                                'propietarios_vivienda',
                                'empresas_sostenibles',
                                'inversores_verdes',
                                'estudiantes_medioambiente',
                                'activistas_clima',
                                'consultores_energia',
                                'arquitectos_sostenibles'
                            ]),
                        Forms\Components\TagsInput::make('target_topics')
                            ->label('Temas Objetivo')
                            ->separator(',')
                            ->suggestions([
                                'energia_solar',
                                'autoconsumo',
                                'eficiencia_energetica',
                                'sostenibilidad',
                                'cambio_climatico',
                                'energias_renovables',
                                'edificios_verdes',
                                'movilidad_electrica'
                            ]),
                        Forms\Components\TagsInput::make('target_locations')
                            ->label('Ubicaciones Objetivo')
                            ->separator(',')
                            ->suggestions([
                                'madrid',
                                'barcelona',
                                'valencia',
                                'sevilla',
                                'bilbao',
                                'andalucia',
                                'cataluna',
                                'pais_vasco'
                            ]),
                        Forms\Components\TagsInput::make('target_demographics')
                            ->label('Demografía Objetivo')
                            ->separator(',')
                            ->suggestions([
                                'edad_25_35',
                                'edad_35_45',
                                'edad_45_55',
                                'ingresos_altos',
                                'ingresos_medios',
                                'educacion_superior',
                                'propietarios_vivienda',
                                'profesionales_tecnologia'
                            ]),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Configuración de Visualización')
                    ->schema([
                        Forms\Components\TextInput::make('ad_label')
                            ->label('Etiqueta del Anuncio')
                            ->default('Patrocinado')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('call_to_action')
                            ->label('Call to Action')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('destination_url')
                            ->label('URL de Destino')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\KeyValue::make('creative_assets')
                            ->label('Recursos Creativos')
                            ->keyLabel('Tipo')
                            ->valueLabel('URL')
                            ->addActionLabel('Agregar Recurso')
                            ->keyLabel('Tipo de Recurso')
                            ->valueLabel('URL del Recurso')
                            ->default([
                                'banner_principal' => '',
                                'imagen_secundaria' => '',
                                'video_promocional' => '',
                                'logo_patrocinador' => ''
                            ]),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Presupuesto y Bidding')
                    ->schema([
                        Forms\Components\Select::make('pricing_model')
                            ->label('Modelo de Precios')
                            ->options([
                                'cpm' => 'CPM (Coste por Mil Impresiones)',
                                'cpc' => 'CPC (Coste por Click)',
                                'cpa' => 'CPA (Coste por Adquisición)',
                                'fixed' => 'Precio Fijo',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('bid_amount')
                            ->label('Cantidad de Bid')
                            ->numeric()
                            ->required()
                            ->prefix('€')
                            ->step(0.0001),
                        Forms\Components\TextInput::make('daily_budget')
                            ->label('Presupuesto Diario')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        Forms\Components\TextInput::make('total_budget')
                            ->label('Presupuesto Total')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Programación')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Fecha de Inicio')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Fecha de Fin'),
                        Forms\Components\KeyValue::make('schedule_config')
                            ->label('Configuración de Horarios')
                            ->keyLabel('Día/Hora')
                            ->valueLabel('Configuración')
                            ->addActionLabel('Agregar Horario')
                            ->keyLabel('Día/Hora')
                            ->valueLabel('Configuración')
                            ->default([
                                'lunes_09_12' => 'activo',
                                'martes_09_12' => 'activo',
                                'miercoles_09_12' => 'activo',
                                'jueves_09_12' => 'activo',
                                'viernes_09_12' => 'activo',
                                'fin_de_semana' => 'pausado'
                            ]),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Configuración de Transparencia')
                    ->schema([
                        Forms\Components\Toggle::make('show_sponsor_info')
                            ->label('Mostrar Información del Patrocinador')
                            ->default(true),
                        Forms\Components\Toggle::make('allow_user_feedback')
                            ->label('Permitir Feedback de Usuarios')
                            ->default(true),
                        Forms\Components\KeyValue::make('disclosure_text')
                            ->label('Texto de Divulgación')
                            ->keyLabel('Idioma')
                            ->valueLabel('Texto')
                            ->addActionLabel('Agregar Idioma')
                            ->keyLabel('Idioma')
                            ->valueLabel('Texto de Divulgación')
                            ->default([
                                'es' => 'Contenido patrocinado',
                                'en' => 'Sponsored content',
                                'ca' => 'Contingut patrocinat',
                                'eu' => 'Babestutako edukiak'
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sponsor.name')
                    ->label('Patrocinador')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('campaign_name')
                    ->label('Nombre de Campaña')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('content_type')
                    ->label('Tipo de Contenido')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'promoted_post' => 'info',
                        'banner_ad' => 'success',
                        'sponsored_topic' => 'warning',
                        'product_placement' => 'primary',
                        'native_content' => 'secondary',
                        'event_promotion' => 'danger',
                        'job_posting' => 'dark',
                        'service_highlight' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_review' => 'warning',
                        'approved' => 'success',
                        'active' => 'info',
                        'paused' => 'secondary',
                        'completed' => 'primary',
                        'rejected' => 'danger',
                        'expired' => 'dark',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('pricing_model')
                    ->label('Modelo de Precios')
                    ->badge()
                    ->color('secondary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid_amount')
                    ->label('Bid')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_budget')
                    ->label('Presupuesto Diario')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_budget')
                    ->label('Presupuesto Total')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('spent_amount')
                    ->label('Gastado')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('impressions')
                    ->label('Impresiones')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('clicks')
                    ->label('Clicks')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('ctr')
                    ->label('CTR')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('conversions')
                    ->label('Conversiones')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('conversion_rate')
                    ->label('Tasa Conversión')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('engagement_rate')
                    ->label('Engagement')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha de Inicio')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha de Fin')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_audience')
                    ->label('Audiencia Objetivo')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('target_topics')
                    ->label('Temas Objetivo')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('target_locations')
                    ->label('Ubicaciones Objetivo')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ad_label')
                    ->label('Etiqueta')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('call_to_action')
                    ->label('CTA')
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('destination_url')
                    ->label('URL Destino')
                    ->url(fn ($record) => $record->destination_url)
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('show_sponsor_info')
                    ->label('Mostrar Info')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('allow_user_feedback')
                    ->label('Permitir Feedback')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sponsorable_type')
                    ->label('Tipo de Contenido')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\TopicPost' => 'Post de Tema',
                        'App\Models\Event' => 'Evento',
                        'App\Models\Cooperative' => 'Cooperativa',
                        'App\Models\NewsArticle' => 'Artículo de Noticias',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reviewedBy.name')
                    ->label('Revisado por')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Fecha de Revisión')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('content_type')
                    ->label('Tipo de Contenido')
                    ->options([
                        'promoted_post' => 'Post Promocionado',
                        'banner_ad' => 'Banner Publicitario',
                        'sponsored_topic' => 'Tema Patrocinado',
                        'product_placement' => 'Placement de Producto',
                        'native_content' => 'Contenido Nativo',
                        'event_promotion' => 'Promoción de Evento',
                        'job_posting' => 'Oferta de Trabajo',
                        'service_highlight' => 'Destacar Servicio',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'pending_review' => 'Pendiente de Revisión',
                        'approved' => 'Aprobado',
                        'active' => 'Activo',
                        'paused' => 'Pausado',
                        'completed' => 'Completado',
                        'rejected' => 'Rechazado',
                        'expired' => 'Expirado',
                    ]),
                Tables\Filters\SelectFilter::make('pricing_model')
                    ->label('Modelo de Precios')
                    ->options([
                        'cpm' => 'CPM',
                        'cpc' => 'CPC',
                        'cpa' => 'CPA',
                        'fixed' => 'Precio Fijo',
                    ]),
                Tables\Filters\SelectFilter::make('sponsorable_type')
                    ->label('Tipo de Contenido Patrocinado')
                    ->options([
                        'App\Models\TopicPost' => 'Post de Tema',
                        'App\Models\Event' => 'Evento',
                        'App\Models\Cooperative' => 'Cooperativa',
                        'App\Models\NewsArticle' => 'Artículo de Noticias',
                    ]),
                Tables\Filters\Filter::make('active_campaigns')
                    ->label('Campañas Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                Tables\Filters\Filter::make('pending_review')
                    ->label('Pendientes de Revisión')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending_review')),
                Tables\Filters\Filter::make('high_budget')
                    ->label('Alto Presupuesto')
                    ->query(fn (Builder $query): Builder => $query->where('total_budget', '>=', 1000)),
                Tables\Filters\Filter::make('active_campaigns')
                    ->label('Campañas Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
                Tables\Filters\Filter::make('pending_review')
                    ->label('Pendientes de Revisión')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending_review')),
                Tables\Filters\Filter::make('high_performance')
                    ->label('Alto Rendimiento')
                    ->query(fn (Builder $query): Builder => $query->where('ctr', '>=', 2.0)),
                Tables\Filters\Filter::make('low_budget')
                    ->label('Bajo Presupuesto')
                    ->query(fn (Builder $query): Builder => $query->where('total_budget', '<=', 100)),
                Tables\Filters\Filter::make('recent_campaigns')
                    ->label('Campañas Recientes')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30))),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Fin'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    })
                    ->label('Rango de Fechas'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (SponsoredContent $record): bool => $record->status === 'pending_review')
                    ->action(function (SponsoredContent $record): void {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => \Illuminate\Support\Facades\Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Campaña aprobada')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (SponsoredContent $record): bool => $record->status === 'pending_review')
                    ->action(function (SponsoredContent $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'reviewed_by' => \Illuminate\Support\Facades\Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Campaña rechazada')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (SponsoredContent $record): string => $record->status === 'active' ? 'Pausar' : 'Activar')
                    ->icon(fn (SponsoredContent $record): string => $record->status === 'active' ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (SponsoredContent $record): string => $record->status === 'active' ? 'warning' : 'success')
                    ->visible(fn (SponsoredContent $record): bool => in_array($record->status, ['approved', 'paused']))
                    ->action(function (SponsoredContent $record): void {
                        $newStatus = $record->status === 'active' ? 'paused' : 'active';
                        $record->update(['status' => $newStatus]);
                        \Filament\Notifications\Notification::make()
                            ->title("Campaña {$newStatus}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicar')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->action(function (SponsoredContent $record): void {
                        $newRecord = $record->replicate();
                        $newRecord->campaign_name = $record->campaign_name . ' (Copia)';
                        $newRecord->status = 'draft';
                        $newRecord->impressions = 0;
                        $newRecord->clicks = 0;
                        $newRecord->conversions = 0;
                        $newRecord->spent_amount = 0;
                        $newRecord->save();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Campaña duplicada')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Aprobar Seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records): void {
                            $records->each(fn (SponsoredContent $record) => $record->update([
                                'status' => 'approved',
                                'reviewed_by' => \Illuminate\Support\Facades\Auth::id(),
                                'reviewed_at' => now(),
                            ]));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' campañas aprobadas')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar Seleccionadas')
                        ->icon('heroicon-o-play')
                        ->action(function (Collection $records): void {
                            $records->each(fn (SponsoredContent $record) => $record->update(['status' => 'active']));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' campañas activadas')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('pause')
                        ->label('Pausar Seleccionadas')
                        ->icon('heroicon-o-pause')
                        ->action(function (Collection $records): void {
                            $records->each(fn (SponsoredContent $record) => $record->update(['status' => 'paused']));
                            \Filament\Notifications\Notification::make()
                                ->title($records->count() . ' campañas pausadas')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['sponsor', 'reviewedBy', 'sponsorable']));
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
            'index' => Pages\ListSponsoredContents::route('/'),
            'create' => Pages\CreateSponsoredContent::route('/create'),
            'edit' => Pages\EditSponsoredContent::route('/{record}/edit'),
        ];
    }

    /**
     * Obtener estadísticas rápidas para el dashboard
     */
    public static function getStats(): array
    {
        $total = SponsoredContent::count();
        $active = SponsoredContent::where('status', 'active')->count();
        $pending = SponsoredContent::where('status', 'pending_review')->count();
        $totalSpent = SponsoredContent::sum('spent_amount');
        $totalImpressions = SponsoredContent::sum('impressions');
        $totalClicks = SponsoredContent::sum('clicks');
        
        return [
            'total_campaigns' => $total,
            'active_campaigns' => $active,
            'pending_review' => $pending,
            'total_spent' => $totalSpent,
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'overall_ctr' => $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0,
        ];
    }
}
