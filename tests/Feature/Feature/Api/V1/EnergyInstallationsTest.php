<?php

namespace Tests\Feature\Feature\Api\V1;

use App\Models\EnergyInstallation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class EnergyInstallationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_energy_installations()
    {
        EnergyInstallation::factory(5)->create();

        $response = $this->getJson('/api/v1/energy-installations');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'type',
                            'type_name',
                            'capacity_kw',
                            'location',
                            'status',
                            'commissioned_at',
                            'estimated_monthly_production_kwh',
                            'owner',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'links',
                    'meta'
                ]);
    }

    public function test_can_get_energy_installation_by_id()
    {
        $installation = EnergyInstallation::factory()->create();

        $response = $this->getJson("/api/v1/energy-installations/{$installation->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'type',
                        'type_name',
                        'capacity_kw',
                        'location',
                        'status',
                        'commissioned_at',
                        'estimated_monthly_production_kwh',
                        'owner',
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'id' => $installation->id,
                        'name' => $installation->name,
                        'type' => $installation->type,
                    ]
                ]);
    }

    public function test_can_create_energy_installation_when_authenticated()
    {
        $user = User::factory()->create();

        $installationData = [
            'name' => 'Nueva Instalación Solar',
            'type' => 'solar',
            'capacity_kw' => 5.5,
            'location' => 'Madrid, España',
            'commissioned_at' => '2025-01-01',
        ];

        $response = $this->actingAs($user, 'sanctum')
                        ->postJson('/api/v1/energy-installations', $installationData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'type',
                        'capacity_kw',
                        'location',
                        'owner',
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'name' => 'Nueva Instalación Solar',
                        'type' => 'solar',
                        'capacity_kw' => 5.5,
                        'owner' => [
                            'id' => $user->id,
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('energy_installations', [
            'name' => 'Nueva Instalación Solar',
            'type' => 'solar',
            'capacity_kw' => 5.5,
            'owner_id' => $user->id,
        ]);
    }

    public function test_can_create_energy_installation_without_authentication()
    {
        $installationData = [
            'name' => 'Instalación Pública',
            'type' => 'wind',
            'capacity_kw' => 25.0,
            'location' => 'Barcelona, España',
        ];

        $response = $this->postJson('/api/v1/energy-installations', $installationData);

        $response->assertStatus(201)
                ->assertJson([
                    'data' => [
                        'name' => 'Instalación Pública',
                        'type' => 'wind',
                        'capacity_kw' => 25.0,
                        'owner' => null,
                    ]
                ]);

        $this->assertDatabaseHas('energy_installations', [
            'name' => 'Instalación Pública',
            'type' => 'wind',
            'capacity_kw' => 25.0,
            'owner_id' => null,
        ]);
    }

    public function test_validates_required_fields_when_creating_installation()
    {
        $response = $this->postJson('/api/v1/energy-installations', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'type', 'capacity_kw', 'location']);
    }

    public function test_validates_type_enum_when_creating_installation()
    {
        $installationData = [
            'name' => 'Test Installation',
            'type' => 'invalid_type',
            'capacity_kw' => 5.0,
            'location' => 'Test Location',
        ];

        $response = $this->postJson('/api/v1/energy-installations', $installationData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
    }

    public function test_validates_capacity_range_when_creating_installation()
    {
        // Test capacidad negativa
        $response = $this->postJson('/api/v1/energy-installations', [
            'name' => 'Test Installation',
            'type' => 'solar',
            'capacity_kw' => -1.0,
            'location' => 'Test Location',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['capacity_kw']);

        // Test capacidad demasiado pequeña
        $response = $this->postJson('/api/v1/energy-installations', [
            'name' => 'Test Installation',
            'type' => 'solar',
            'capacity_kw' => 0.05,
            'location' => 'Test Location',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['capacity_kw']);
    }

    public function test_can_update_energy_installation_when_authenticated()
    {
        $user = User::factory()->create();
        $installation = EnergyInstallation::factory()->create([
            'owner_id' => $user->id,
        ]);

        $updateData = [
            'name' => 'Instalación Actualizada',
            'capacity_kw' => 10.0,
        ];

        $response = $this->actingAs($user, 'sanctum')
                        ->putJson("/api/v1/energy-installations/{$installation->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $installation->id,
                        'name' => 'Instalación Actualizada',
                        'capacity_kw' => 10.0,
                    ]
                ]);

        $this->assertDatabaseHas('energy_installations', [
            'id' => $installation->id,
            'name' => 'Instalación Actualizada',
            'capacity_kw' => 10.0,
        ]);
    }

    public function test_can_delete_energy_installation_when_authenticated()
    {
        $user = User::factory()->create();
        $installation = EnergyInstallation::factory()->create([
            'owner_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
                        ->deleteJson("/api/v1/energy-installations/{$installation->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('energy_installations', [
            'id' => $installation->id,
        ]);
    }

    public function test_can_filter_installations_by_type()
    {
        EnergyInstallation::factory()->create(['type' => 'solar']);
        EnergyInstallation::factory()->create(['type' => 'solar']);
        EnergyInstallation::factory()->create(['type' => 'wind']);
        EnergyInstallation::factory()->create(['type' => 'hydro']);

        $response = $this->getJson('/api/v1/energy-installations/filter/by-type/solar');

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Verificar que solo aparecen instalaciones solares
        foreach ($data as $installation) {
            $this->assertEquals('solar', $installation['type']);
        }
        
        $this->assertCount(2, $data);
    }

    public function test_can_filter_installations_by_capacity()
    {
        EnergyInstallation::factory()->create(['capacity_kw' => 5.0]);
        EnergyInstallation::factory()->create(['capacity_kw' => 15.0]);
        EnergyInstallation::factory()->create(['capacity_kw' => 25.0]);
        EnergyInstallation::factory()->create(['capacity_kw' => 35.0]);

        // Filtrar por rango 10-30 kW
        $response = $this->getJson('/api/v1/energy-installations/filter/by-capacity?min_kw=10&max_kw=30');

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Verificar que están en el rango correcto
        foreach ($data as $installation) {
            $this->assertGreaterThanOrEqual(10.0, $installation['capacity_kw']);
            $this->assertLessThanOrEqual(30.0, $installation['capacity_kw']);
        }
        
        $this->assertCount(2, $data); // 15.0 y 25.0
    }

    public function test_can_get_commissioned_installations()
    {
        // Crear instalaciones comisionadas
        EnergyInstallation::factory(2)->commissioned()->create();
        
        // Crear instalaciones en desarrollo
        EnergyInstallation::factory(2)->inDevelopment()->create();

        $response = $this->getJson('/api/v1/energy-installations/commissioned');

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Verificar que todas están comisionadas
        foreach ($data as $installation) {
            $this->assertEquals('operativa', $installation['status']);
        }
        
        $this->assertCount(2, $data);
    }

    public function test_can_get_installations_in_development()
    {
        // Crear instalaciones comisionadas
        EnergyInstallation::factory(2)->commissioned()->create();
        
        // Crear instalaciones en desarrollo
        EnergyInstallation::factory(2)->inDevelopment()->create();

        $response = $this->getJson('/api/v1/energy-installations/in-development');

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Verificar que todas están en desarrollo
        foreach ($data as $installation) {
            $this->assertContains($installation['status'], ['planificación', 'construcción']);
        }
        
        $this->assertCount(2, $data);
    }

    public function test_can_search_installations()
    {
        EnergyInstallation::factory()->create([
            'name' => 'Instalación Solar Madrid',
            'location' => 'Madrid, España',
        ]);

        EnergyInstallation::factory()->create([
            'name' => 'Planta Eólica Barcelona',
            'location' => 'Barcelona, España',
        ]);

        // Buscar por nombre
        $response = $this->getJson('/api/v1/energy-installations/search?q=solar');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Solar', $data[0]['name']);

        // Buscar por ubicación
        $response = $this->getJson('/api/v1/energy-installations/search?q=madrid');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Madrid', $data[0]['location']);
    }

    public function test_search_requires_query_parameter()
    {
        $response = $this->getJson('/api/v1/energy-installations/search');

        $response->assertStatus(400)
                ->assertJson([
                    'error' => 'Query parameter q is required'
                ]);
    }

    public function test_can_get_installation_statistics()
    {
        // Crear instalaciones con datos específicos
        EnergyInstallation::factory(3)->solar()->commissioned()->create();
        EnergyInstallation::factory(2)->wind()->commissioned()->create();
        EnergyInstallation::factory(1)->inDevelopment()->create();

        $response = $this->getJson('/api/v1/energy-installations/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'total_installations',
                        'commissioned_installations',
                        'in_development_installations',
                        'total_capacity_kw',
                        'commissioned_capacity_kw',
                        'by_type',
                        'average_capacity_kw',
                        'estimated_monthly_production_kwh',
                    ]
                ]);

        $stats = $response->json('data');
        
        $this->assertEquals(6, $stats['total_installations']);
        $this->assertEquals(5, $stats['commissioned_installations']);
        $this->assertEquals(1, $stats['in_development_installations']);
        $this->assertIsArray($stats['by_type']);
    }

    public function test_installation_includes_calculated_attributes()
    {
        $installation = EnergyInstallation::factory()->create([
            'type' => 'solar',
            'capacity_kw' => 10.0,
            'commissioned_at' => Carbon::now()->subMonths(1),
        ]);

        $response = $this->getJson("/api/v1/energy-installations/{$installation->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'type' => 'solar',
                        'type_name' => 'Fotovoltaica',
                        'status' => 'operativa',
                        'estimated_monthly_production_kwh' => 1152.0, // 10kW * 0.16 * 24 * 30
                    ]
                ]);
    }

    public function test_returns_404_for_nonexistent_installation()
    {
        $response = $this->getJson('/api/v1/energy-installations/99999');

        $response->assertStatus(404);
    }

    public function test_unauthorized_users_cannot_modify_installations()
    {
        $installation = EnergyInstallation::factory()->create();

        // Test create sin autenticación
        $response = $this->putJson("/api/v1/energy-installations/{$installation->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(401);

        // Test delete sin autenticación
        $response = $this->deleteJson("/api/v1/energy-installations/{$installation->id}");

        $response->assertStatus(401);
    }
}