<?php

namespace Database\Factories;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiKey>
 */
class ApiKeyFactory extends Factory
{
    protected $model = ApiKey::class;

    public function definition(): array
    {
        $scopes = ['read-only', 'write', 'full-access'];
        $scope = $this->faker->randomElement($scopes);
        
        // Generar límite de tasa basado en el scope
        $rateLimit = $this->generateRateLimit($scope);
        
        // Generar fecha de expiración (algunas no expiran)
        $expiresAt = $this->faker->boolean(70) ? $this->faker->dateTimeBetween('now', '+1 year') : null;
        
        // Generar token único
        $token = $this->generateUniqueToken();

        return [
            'user_id' => User::factory(),
            'token' => $token,
            'scope' => $scope,
            'rate_limit' => $rateLimit,
            'expires_at' => $expiresAt,
            'is_revoked' => $this->faker->boolean(15), // 15% revocadas
        ];
    }

    private function generateRateLimit(string $scope): int
    {
        return match ($scope) {
            'read-only' => $this->faker->numberBetween(100, 2000),
            'write' => $this->faker->numberBetween(500, 5000),
            'full-access' => $this->faker->numberBetween(1000, 10000),
            default => $this->faker->numberBetween(100, 1000),
        };
    }

    private function generateUniqueToken(): string
    {
        $prefixes = ['ak_', 'api_', 'key_', 'token_', ''];
        $prefix = $this->faker->randomElement($prefixes);
        
        // Generar token único
        do {
            $token = $prefix . $this->faker->regexify('[A-Za-z0-9]{32,64}');
        } while (ApiKey::where('token', $token)->exists());
        
        return $token;
    }

    public function readOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'read-only',
            'rate_limit' => $this->faker->numberBetween(100, 2000),
        ]);
    }

    public function write(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'write',
            'rate_limit' => $this->faker->numberBetween(500, 5000),
        ]);
    }

    public function fullAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'full-access',
            'rate_limit' => $this->faker->numberBetween(1000, 10000),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_revoked' => false,
            'expires_at' => $this->faker->boolean(80) ? $this->faker->dateTimeBetween('now', '+1 year') : null,
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_revoked' => true,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_revoked' => false,
            'expires_at' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_revoked' => false,
            'expires_at' => $this->faker->dateTimeBetween('now', '+7 days'),
        ]);
    }

    public function neverExpires(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => null,
        ]);
    }

    public function highRateLimit(): static
    {
        return $this->state(fn (array $attributes) => [
            'rate_limit' => $this->faker->numberBetween(5000, 10000),
        ]);
    }

    public function lowRateLimit(): static
    {
        return $this->state(fn (array $attributes) => [
            'rate_limit' => $this->faker->numberBetween(100, 500),
        ]);
    }

    public function mediumRateLimit(): static
    {
        return $this->state(fn (array $attributes) => [
            'rate_limit' => $this->faker->numberBetween(1000, 3000),
        ]);
    }

    public function withCustomToken(string $token): static
    {
        return $this->state(fn (array $attributes) => [
            'token' => $token,
        ]);
    }

    public function withExpiration(int $days): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addDays($days),
        ]);
    }

    public function withRateLimit(int $rateLimit): static
    {
        return $this->state(fn (array $attributes) => [
            'rate_limit' => $rateLimit,
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function development(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'full-access',
            'rate_limit' => 10000,
            'expires_at' => null,
            'is_revoked' => false,
            'token' => 'dev_' . $this->faker->regexify('[A-Za-z0-9]{32}'),
        ]);
    }

    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => $this->faker->randomElement(['read-only', 'write']),
            'rate_limit' => $this->faker->numberBetween(1000, 5000),
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'is_revoked' => false,
            'token' => 'prod_' . $this->faker->regexify('[A-Za-z0-9]{40}'),
        ]);
    }

    public function testing(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'read-only',
            'rate_limit' => 100,
            'expires_at' => now()->addDays(30),
            'is_revoked' => false,
            'token' => 'test_' . $this->faker->regexify('[A-Za-z0-9]{24}'),
        ]);
    }

    public function apiIntegration(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'write',
            'rate_limit' => $this->faker->numberBetween(2000, 8000),
            'expires_at' => $this->faker->boolean(60) ? $this->faker->dateTimeBetween('now', '+6 months') : null,
            'is_revoked' => false,
            'token' => 'int_' . $this->faker->regexify('[A-Za-z0-9]{36}'),
        ]);
    }

    public function mobileApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'read-only',
            'rate_limit' => $this->faker->numberBetween(500, 2000),
            'expires_at' => $this->faker->dateTimeBetween('now', '+2 years'),
            'is_revoked' => false,
            'token' => 'mob_' . $this->faker->regexify('[A-Za-z0-9]{28}'),
        ]);
    }

    public function webApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => $this->faker->randomElement(['read-only', 'write']),
            'rate_limit' => $this->faker->numberBetween(1000, 5000),
            'expires_at' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('now', '+1 year') : null,
            'is_revoked' => false,
            'token' => 'web_' . $this->faker->regexify('[A-Za-z0-9]{32}'),
        ]);
    }

    public function thirdParty(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'read-only',
            'rate_limit' => $this->faker->numberBetween(100, 1000),
            'expires_at' => $this->faker->dateTimeBetween('now', '+3 months'),
            'is_revoked' => $this->faker->boolean(20),
            'token' => '3rd_' . $this->faker->regexify('[A-Za-z0-9]{30}'),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'full-access',
            'rate_limit' => 10000,
            'expires_at' => null,
            'is_revoked' => false,
            'token' => 'admin_' . $this->faker->regexify('[A-Za-z0-9]{44}'),
        ]);
    }

    public function service(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'write',
            'rate_limit' => $this->faker->numberBetween(5000, 10000),
            'expires_at' => null,
            'is_revoked' => false,
            'token' => 'svc_' . $this->faker->regexify('[A-Za-z0-9]{40}'),
        ]);
    }
}
