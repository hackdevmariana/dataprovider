<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-key 
                            {--user= : ID o email del usuario}
                            {--scope=read-only : Scope de la clave (read-only, write, full-access)}
                            {--rate-limit=1000 : Límite de rate por hora}
                            {--expires= : Fecha de expiración (YYYY-MM-DD) o "never" para sin expiración}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar una nueva clave API para un usuario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener o identificar el usuario
        $user = $this->getUser();
        if (!$user) {
            return 1;
        }

        // Validar el scope
        $scope = $this->option('scope');
        if (!in_array($scope, ['read-only', 'write', 'full-access'])) {
            $this->error('Scope inválido. Debe ser: read-only, write, o full-access');
            return 1;
        }

        // Validar el rate limit
        $rateLimit = (int) $this->option('rate-limit');
        if ($rateLimit < 1) {
            $this->error('El rate limit debe ser mayor a 0');
            return 1;
        }

        // Procesar la fecha de expiración
        $expiresAt = $this->processExpirationDate();

        // Generar la clave API
        $apiKey = ApiKey::create([
            'user_id' => $user->id,
            'token' => 'dp_' . Str::random(32),
            'scope' => $scope,
            'rate_limit' => $rateLimit,
            'expires_at' => $expiresAt,
            'is_revoked' => false,
        ]);

        $this->info('✅ Clave API generada exitosamente!');
        $this->newLine();
        
        $this->table(
            ['Campo', 'Valor'],
            [
                ['Usuario', $user->name . ' (' . $user->email . ')'],
                ['Token', $apiKey->token],
                ['Scope', $scope],
                ['Rate Limit', $rateLimit . ' requests/hora'],
                ['Expira', $expiresAt ? $expiresAt->format('Y-m-d H:i:s') : 'Nunca'],
                ['Estado', 'Activa'],
            ]
        );

        $this->newLine();
        $this->warn('⚠️  Guarda esta clave en un lugar seguro. No se mostrará de nuevo.');

        return 0;
    }

    /**
     * Obtener el usuario por ID o email
     */
    private function getUser()
    {
        $userIdentifier = $this->option('user');
        
        if (!$userIdentifier) {
            // Mostrar lista de usuarios disponibles
            $users = User::select('id', 'name', 'email')->get();
            
            if ($users->isEmpty()) {
                $this->error('No hay usuarios en el sistema');
                return null;
            }

            $this->info('Usuarios disponibles:');
            $this->table(
                ['ID', 'Nombre', 'Email'],
                $users->toArray()
            );

            $userIdentifier = $this->ask('Ingresa el ID o email del usuario:');
        }

        // Buscar por ID o email
        $user = User::where('id', $userIdentifier)
                   ->orWhere('email', $userIdentifier)
                   ->first();

        if (!$user) {
            $this->error('Usuario no encontrado');
            return null;
        }

        return $user;
    }

    /**
     * Procesar la fecha de expiración
     */
    private function processExpirationDate()
    {
        $expires = $this->option('expires');
        
        if (!$expires || $expires === 'never') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($expires);
        } catch (\Exception $e) {
            $this->error('Formato de fecha inválido. Use YYYY-MM-DD');
            return null;
        }
    }
}
