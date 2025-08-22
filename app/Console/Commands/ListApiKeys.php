<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ListApiKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:list-keys 
                            {--user= : Filtrar por ID o email del usuario}
                            {--scope= : Filtrar por scope (read-only, write, full-access)}
                            {--status= : Filtrar por estado (active, revoked)}
                            {--expired : Mostrar solo claves expiradas}
                            {--expiring-soon : Mostrar claves que expiran en los próximos 30 días}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listar todas las claves API con filtros opcionales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = ApiKey::with('user');

        // Aplicar filtros
        $this->applyFilters($query);

        $apiKeys = $query->orderBy('created_at', 'desc')->get();

        if ($apiKeys->isEmpty()) {
            $this->info('No se encontraron claves API con los filtros especificados.');
            return 0;
        }

        $this->info('🔑 Claves API encontradas: ' . $apiKeys->count());
        $this->newLine();

        $this->table(
            ['ID', 'Usuario', 'Scope', 'Rate Limit', 'Expira', 'Estado', 'Creada', 'Token'],
            $apiKeys->map(function ($apiKey) {
                $status = $apiKey->is_revoked ? '🔴 Revocada' : '🟢 Activa';
                $expires = $this->formatExpiration($apiKey->expires_at);
                
                return [
                    $apiKey->id,
                    $apiKey->user->name ?? 'N/A',
                    $this->formatScope($apiKey->scope),
                    $apiKey->rate_limit . '/h',
                    $expires,
                    $status,
                    $apiKey->created_at->format('Y-m-d'),
                    substr($apiKey->token, 0, 20) . '...',
                ];
            })->toArray()
        );

        // Mostrar estadísticas
        $this->showStatistics($apiKeys);

        return 0;
    }

    /**
     * Aplicar filtros a la consulta
     */
    private function applyFilters($query)
    {
        // Filtro por usuario
        if ($userId = $this->option('user')) {
            $query->whereHas('user', function ($q) use ($userId) {
                $q->where('id', $userId)
                  ->orWhere('email', $userId);
            });
        }

        // Filtro por scope
        if ($scope = $this->option('scope')) {
            $query->where('scope', $scope);
        }

        // Filtro por estado
        if ($status = $this->option('status')) {
            if ($status === 'active') {
                $query->where('is_revoked', false);
            } elseif ($status === 'revoked') {
                $query->where('is_revoked', true);
            }
        }

        // Filtro por expiradas
        if ($this->option('expired')) {
            $query->where('expires_at', '<', now());
        }

        // Filtro por próximas a expirar
        if ($this->option('expiring-soon')) {
            $query->where('expires_at', '>', now())
                  ->where('expires_at', '<', now()->addDays(30));
        }
    }

    /**
     * Formatear la fecha de expiración
     */
    private function formatExpiration($expiresAt)
    {
        if (!$expiresAt) {
            return '🟡 Nunca';
        }

        if ($expiresAt->isPast()) {
            return '🔴 Expirada';
        }

        if ($expiresAt->diffInDays(now()) <= 30) {
            return '🟠 ' . $expiresAt->format('Y-m-d');
        }

        return '🟢 ' . $expiresAt->format('Y-m-d');
    }

    /**
     * Formatear el scope con emojis
     */
    private function formatScope($scope)
    {
        return match($scope) {
            'full-access' => '🔓 ' . $scope,
            'write' => '✏️ ' . $scope,
            'read-only' => '👁️ ' . $scope,
            default => $scope,
        };
    }

    /**
     * Mostrar estadísticas de las claves API
     */
    private function showStatistics($apiKeys)
    {
        $this->newLine();
        $this->info('📊 Estadísticas:');
        
        $total = $apiKeys->count();
        $active = $apiKeys->where('is_revoked', false)->count();
        $revoked = $apiKeys->where('is_revoked', true)->count();
        $expired = $apiKeys->where('expires_at', '<', now())->count();
        $expiringSoon = $apiKeys->where('expires_at', '>', now())
                               ->where('expires_at', '<', now()->addDays(30))
                               ->count();

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total de claves', $total],
                ['🟢 Activas', $active],
                ['🔴 Revocadas', $revoked],
                ['🔴 Expiradas', $expired],
                ['🟠 Expiran pronto', $expiringSoon],
            ]
        );
    }
}
