<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios existentes para asignar las claves API
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Creando usuario de prueba...');
            $user = User::factory()->create([
                'name' => 'API Test User',
                'email' => 'api@example.com',
            ]);
            $users = collect([$user]);
        }

        // Crear claves API de ejemplo
        $apiKeys = [
            [
                'user_id' => $users->first()->id,
                'token' => 'dp_' . Str::random(32),
                'scope' => 'full-access',
                'rate_limit' => 10000,
                'expires_at' => now()->addYear(),
                'is_revoked' => false,
            ],
            [
                'user_id' => $users->first()->id,
                'token' => 'dp_' . Str::random(32),
                'scope' => 'write',
                'rate_limit' => 5000,
                'expires_at' => now()->addMonths(6),
                'is_revoked' => false,
            ],
            [
                'user_id' => $users->first()->id,
                'token' => 'dp_' . Str::random(32),
                'scope' => 'read-only',
                'rate_limit' => 1000,
                'expires_at' => now()->addMonths(3),
                'is_revoked' => false,
            ],
            [
                'user_id' => $users->first()->id,
                'token' => 'dp_' . Str::random(32),
                'scope' => 'read-only',
                'rate_limit' => 500,
                'expires_at' => now()->addMonth(),
                'is_revoked' => false,
            ],
            [
                'user_id' => $users->first()->id,
                'token' => 'dp_' . Str::random(32),
                'scope' => 'write',
                'rate_limit' => 2000,
                'expires_at' => null, // Sin expiración
                'is_revoked' => false,
            ],
        ];

        // Crear claves API adicionales para otros usuarios si existen
        if ($users->count() > 1) {
            foreach ($users->skip(1)->take(3) as $user) {
                $apiKeys[] = [
                    'user_id' => $user->id,
                    'token' => 'dp_' . Str::random(32),
                    'scope' => 'read-only',
                    'rate_limit' => rand(500, 2000),
                    'expires_at' => now()->addMonths(rand(1, 12)),
                    'is_revoked' => false,
                ];
            }
        }

        // Crear algunas claves API revocadas para testing
        $apiKeys[] = [
            'user_id' => $users->first()->id,
            'token' => 'dp_' . Str::random(32),
            'scope' => 'read-only',
            'rate_limit' => 1000,
            'expires_at' => now()->subDay(), // Expirada
            'is_revoked' => true,
        ];

        // Insertar las claves API
        foreach ($apiKeys as $apiKeyData) {
            ApiKey::create($apiKeyData);
        }

        $this->command->info('Se han creado ' . count($apiKeys) . ' claves API de ejemplo.');
        
        // Mostrar información de las claves creadas
        $this->command->table(
            ['Usuario', 'Scope', 'Rate Limit', 'Expira', 'Estado', 'Token'],
            ApiKey::with('user')->get()->map(function ($apiKey) {
                return [
                    $apiKey->user->name ?? 'N/A',
                    $apiKey->scope,
                    $apiKey->rate_limit,
                    $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d') : 'Sin expiración',
                    $apiKey->is_revoked ? 'Revocada' : 'Activa',
                    substr($apiKey->token, 0, 20) . '...',
                ];
            })->toArray()
        );
    }
}
