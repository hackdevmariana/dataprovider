<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class RevokeApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:revoke-key 
                            {token : Token de la clave API a revocar}
                            {--reason= : Razón de la revocación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revocar una clave API existente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = $this->argument('token');
        $reason = $this->option('reason') ?: 'Revocada por administrador';

        // Buscar la clave API
        $apiKey = ApiKey::where('token', $token)->first();

        if (!$apiKey) {
            $this->error('❌ Clave API no encontrada');
            return 1;
        }

        if ($apiKey->is_revoked) {
            $this->warn('⚠️  Esta clave API ya está revocada');
            return 0;
        }

        // Revocar la clave
        $apiKey->update([
            'is_revoked' => true,
        ]);

        $this->info('✅ Clave API revocada exitosamente!');
        $this->newLine();
        
        $this->table(
            ['Campo', 'Valor'],
            [
                ['Token', $apiKey->token],
                ['Usuario', $apiKey->user->name ?? 'N/A'],
                ['Scope', $apiKey->scope],
                ['Rate Limit', $apiKey->rate_limit . ' requests/hora'],
                ['Expira', $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d H:i:s') : 'Nunca'],
                ['Estado', 'Revocada'],
                ['Razón', $reason],
                ['Revocada el', now()->format('Y-m-d H:i:s')],
            ]
        );

        return 0;
    }
}
