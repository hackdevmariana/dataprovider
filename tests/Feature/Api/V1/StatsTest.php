<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class StatsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario autenticado
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_list_stats_with_pagination()
    {
        Stat::factory()->count(25)->create();
        
        $response = $this->getJson('/api/v1/stats?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'type',
                            'value',
                            'unit',
                            'period',
                            'date'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);

        $this->assertCount(10, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_stats_by_type()
    {
        Stat::factory()->count(3)->create(['type' => 'energy']);
        Stat::factory()->count(2)->create(['type' => 'carbon']);
        
        $response = $this->getJson('/api/v1/stats?type=energy');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_stats_by_period()
    {
        Stat::factory()->count(3)->create(['period' => 'monthly']);
        Stat::factory()->count(2)->create(['period' => 'daily']);
        
        $response = $this->getJson('/api/v1/stats?period=monthly');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_stats_by_date_range()
    {
        Stat::factory()->create(['date' => '2024-01-01']);
        Stat::factory()->create(['date' => '2024-01-15']);
        Stat::factory()->create(['date' => '2024-02-01']);
        
        $response = $this->getJson('/api/v1/stats?date_from=2024-01-01&date_to=2024-01-31');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    /** @test */
    public function it_can_search_stats_by_name_or_description()
    {
        Stat::factory()->create(['name' => 'Consumo Energético', 'description' => 'Consumo mensual de energía']);
        Stat::factory()->create(['name' => 'Emisiones CO2', 'description' => 'Emisiones de dióxido de carbono']);
        
        $response = $this->getJson('/api/v1/stats?search=energia');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Consumo Energético', $response->json('data.0.name'));
    }

    /** @test */
    public function it_can_create_a_new_stat()
    {
        $statData = [
            'name' => 'Nueva Estadística',
            'type' => 'energy',
            'value' => 1250.5,
            'unit' => 'kWh',
            'period' => 'monthly',
            'date' => '2024-01-01',
            'description' => 'Descripción de la nueva estadística'
        ];

        $response = $this->postJson('/api/v1/stats', $statData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'type',
                        'value',
                        'unit',
                        'period',
                        'date',
                        'description',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('stats', [
            'name' => 'Nueva Estadística',
            'type' => 'energy',
            'value' => 1250.5
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_stat()
    {
        $response = $this->postJson('/api/v1/stats', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'type', 'value', 'unit', 'period', 'date']);
    }

    /** @test */
    public function it_validates_type_enum_when_creating_stat()
    {
        $response = $this->postJson('/api/v1/stats', [
            'name' => 'Test Stat',
            'type' => 'invalid_type',
            'value' => 100,
            'unit' => 'kWh',
            'period' => 'monthly',
            'date' => '2024-01-01'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function it_validates_period_enum_when_creating_stat()
    {
        $response = $this->postJson('/api/v1/stats', [
            'name' => 'Test Stat',
            'type' => 'energy',
            'value' => 100,
            'unit' => 'kWh',
            'period' => 'invalid_period',
            'date' => '2024-01-01'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['period']);
    }

    /** @test */
    public function it_validates_date_format_when_creating_stat()
    {
        $response = $this->postJson('/api/v1/stats', [
            'name' => 'Test Stat',
            'type' => 'energy',
            'value' => 100,
            'unit' => 'kWh',
            'period' => 'monthly',
            'date' => 'invalid-date'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['date']);
    }

    /** @test */
    public function it_can_show_stat_details()
    {
        $stat = Stat::factory()->create();

        $response = $this->getJson("/api/v1/stats/{$stat->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $stat->id,
                        'name' => $stat->name,
                        'type' => $stat->type
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_stat()
    {
        $response = $this->getJson('/api/v1/stats/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_stat()
    {
        $stat = Stat::factory()->create();

        $updateData = [
            'name' => 'Estadística Actualizada',
            'value' => 1500.0,
            'description' => 'Descripción actualizada'
        ];

        $response = $this->putJson("/api/v1/stats/{$stat->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'Estadística Actualizada',
                        'value' => 1500.0,
                        'description' => 'Descripción actualizada'
                    ]
                ]);

        $this->assertDatabaseHas('stats', [
            'id' => $stat->id,
            'name' => 'Estadística Actualizada'
        ]);
    }

    /** @test */
    public function it_can_delete_stat()
    {
        $stat = Stat::factory()->create();

        $response = $this->deleteJson("/api/v1/stats/{$stat->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('stats', ['id' => $stat->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_stat()
    {
        $response = $this->deleteJson('/api/v1/stats/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        Stat::factory()->count(150)->create();
        
        $response = $this->getJson('/api/v1/stats?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_stats_ordered_by_date_desc()
    {
        Stat::factory()->create(['date' => '2024-01-01']);
        Stat::factory()->create(['date' => '2024-01-15']);
        Stat::factory()->create(['date' => '2024-02-01']);
        
        $response = $this->getJson('/api/v1/stats');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('2024-02-01', $data[0]['date']);
        $this->assertEquals('2024-01-15', $data[1]['date']);
        $this->assertEquals('2024-01-01', $data[2]['date']);
    }

    /** @test */
    public function it_can_filter_stats_by_multiple_criteria()
    {
        Stat::factory()->create([
            'name' => 'Consumo Energético',
            'type' => 'energy',
            'period' => 'monthly',
            'date' => '2024-01-01'
        ]);
        Stat::factory()->create([
            'name' => 'Consumo Energético',
            'type' => 'energy',
            'period' => 'daily',
            'date' => '2024-01-01'
        ]);
        
        $response = $this->getJson('/api/v1/stats?type=energy&period=monthly&search=energia');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('monthly', $response->json('data.0.period'));
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $stat = Stat::factory()->create();
        
        // Desautenticar
        auth()->logout();

        // Test index
        $this->getJson('/api/v1/stats')->assertStatus(401);
        
        // Test store
        $this->postJson('/api/v1/stats', [])->assertStatus(401);
        
        // Test show
        $this->getJson("/api/v1/stats/{$stat->id}")->assertStatus(401);
        
        // Test update
        $this->putJson("/api/v1/stats/{$stat->id}", [])->assertStatus(401);
        
        // Test delete
        $this->deleteJson("/api/v1/stats/{$stat->id}")->assertStatus(401);
    }

    /** @test */
    public function it_can_handle_stat_with_metadata()
    {
        $statData = [
            'name' => 'Estadística con Metadatos',
            'type' => 'energy',
            'value' => 1000.0,
            'unit' => 'kWh',
            'period' => 'monthly',
            'date' => '2024-01-01',
            'metadata' => [
                'source' => 'smart_meter',
                'location' => 'building_a',
                'accuracy' => '95%'
            ]
        ];

        $response = $this->postJson('/api/v1/stats', $statData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('stats', [
            'name' => 'Estadística con Metadatos',
            'type' => 'energy'
        ]);
    }
}
