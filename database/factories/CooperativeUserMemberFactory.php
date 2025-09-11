<?php

namespace Database\Factories;

use App\Models\CooperativeUserMember;
use App\Models\Cooperative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CooperativeUserMember>
 */
class CooperativeUserMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = [
            'miembro',
            'socio',
            'gestor',
            'representante legal',
            'administrador',
            'tesorero',
            'secretario',
            'presidente',
            'vicepresidente',
            'consejero',
            'delegado',
            'coordinador',
            'responsable técnico',
            'responsable comercial',
            'responsable de comunicación'
        ];

        return [
            'cooperative_id' => Cooperative::factory(),
            'user_id' => User::factory(),
            'role' => $this->faker->randomElement($roles),
            'joined_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'is_active' => $this->faker->boolean(85), // 85% activos
        ];
    }

    /**
     * Indicate that the member is a regular member.
     */
    public function member(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $this->faker->randomElement(['miembro', 'socio']),
        ]);
    }

    /**
     * Indicate that the member is a manager.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $this->faker->randomElement(['gestor', 'administrador', 'coordinador']),
        ]);
    }

    /**
     * Indicate that the member is a board member.
     */
    public function boardMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $this->faker->randomElement([
                'presidente',
                'vicepresidente',
                'secretario',
                'tesorero',
                'consejero'
            ]),
        ]);
    }

    /**
     * Indicate that the member is a legal representative.
     */
    public function legalRepresentative(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'representante legal',
        ]);
    }

    /**
     * Indicate that the member is a technical responsible.
     */
    public function technicalResponsible(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $this->faker->randomElement([
                'responsable técnico',
                'responsable comercial',
                'responsable de comunicación'
            ]),
        ]);
    }

    /**
     * Indicate that the member is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the member is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the member joined recently.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'joined_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the member is a founding member.
     */
    public function founding(): static
    {
        return $this->state(fn (array $attributes) => [
            'joined_at' => $this->faker->dateTimeBetween('-10 years', '-5 years'),
            'role' => $this->faker->randomElement(['presidente', 'vicepresidente', 'secretario', 'tesorero']),
        ]);
    }
}
