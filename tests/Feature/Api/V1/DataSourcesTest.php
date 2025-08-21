<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\DataSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class DataSourcesTest extends TestCase
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
    public function it_can_list_data_sources_with_pagination()
    {
        DataSource::factory()->count(25)->create();
        
        $response = $this->getJson('/api/v1/data-sources?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'type',
                            'url',
                            'status',
                            'last_sync_at'
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
    public function it_can_filter_data_sources_by_type()
    {
        DataSource::factory()->count(3)->create(['type' => 'api']);
        DataSource::factory()->count(2)->create(['type' => 'file']);
        
        $response = $this->getJson('/api/v1/data-sources?type=api');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_data_sources_by_status()
    {
        DataSource::factory()->count(3)->create(['status' => 'active']);
        DataSource::factory()->count(2)->create(['status' => 'inactive']);
        
        $response = $this->getJson('/api/v1/data-sources?status=active');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_search_data_sources_by_name_or_description()
    {
        DataSource::factory()->create(['name' => 'Weather API', 'description' => 'API del clima']);
        DataSource::factory()->create(['name' => 'Stock API', 'description' => 'API de acciones']);
        
        $response = $this->getJson('/api/v1/data-sources?search=weather');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Weather API', $response->json('data.0.name'));
    }

    /** @test */
    public function it_can_create_a_new_data_source()
    {
        $dataSourceData = [
            'name' => 'Nueva Fuente de Datos',
            'slug' => 'nueva-fuente',
            'type' => 'api',
            'url' => 'https://api.example.com',
            'description' => 'Descripción de la nueva fuente',
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/data-sources', $dataSourceData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'type',
                        'url',
                        'description',
                        'status',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('data_sources', [
            'name' => 'Nueva Fuente de Datos',
            'slug' => 'nueva-fuente'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_data_source()
    {
        $response = $this->postJson('/api/v1/data-sources', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'type']);
    }

    /** @test */
    public function it_validates_slug_uniqueness_when_creating_data_source()
    {
        DataSource::factory()->create(['slug' => 'test-source']);
        
        $response = $this->postJson('/api/v1/data-sources', [
            'name' => 'Test Source',
            'slug' => 'test-source',
            'type' => 'api'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_validates_url_format_when_creating_data_source()
    {
        $response = $this->postJson('/api/v1/data-sources', [
            'name' => 'Test Source',
            'slug' => 'test-source',
            'type' => 'api',
            'url' => 'invalid-url'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['url']);
    }

    /** @test */
    public function it_validates_type_enum_when_creating_data_source()
    {
        $response = $this->postJson('/api/v1/data-sources', [
            'name' => 'Test Source',
            'slug' => 'test-source',
            'type' => 'invalid_type'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function it_can_show_data_source_details()
    {
        $dataSource = DataSource::factory()->create();

        $response = $this->getJson("/api/v1/data-sources/{$dataSource->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $dataSource->id,
                        'name' => $dataSource->name,
                        'slug' => $dataSource->slug
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_data_source()
    {
        $response = $this->getJson('/api/v1/data-sources/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_data_source()
    {
        $dataSource = DataSource::factory()->create();

        $updateData = [
            'name' => 'Fuente Actualizada',
            'url' => 'https://new-api.example.com',
            'status' => 'inactive'
        ];

        $response = $this->putJson("/api/v1/data-sources/{$dataSource->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'Fuente Actualizada',
                        'url' => 'https://new-api.example.com',
                        'status' => 'inactive'
                    ]
                ]);

        $this->assertDatabaseHas('data_sources', [
            'id' => $dataSource->id,
            'name' => 'Fuente Actualizada'
        ]);
    }

    /** @test */
    public function it_validates_slug_uniqueness_when_updating_data_source()
    {
        $source1 = DataSource::factory()->create(['slug' => 'source-1']);
        $source2 = DataSource::factory()->create(['slug' => 'source-2']);

        $response = $this->putJson("/api/v1/data-sources/{$source1->id}", [
            'slug' => 'source-2'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_can_delete_data_source()
    {
        $dataSource = DataSource::factory()->create();

        $response = $this->deleteJson("/api/v1/data-sources/{$dataSource->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('data_sources', ['id' => $dataSource->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_data_source()
    {
        $response = $this->deleteJson('/api/v1/data-sources/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        DataSource::factory()->count(150)->create();
        
        $response = $this->getJson('/api/v1/data-sources?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_data_sources_ordered_by_name()
    {
        DataSource::factory()->create(['name' => 'Zebra API']);
        DataSource::factory()->create(['name' => 'Alpha API']);
        DataSource::factory()->create(['name' => 'Beta API']);
        
        $response = $this->getJson('/api/v1/data-sources');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Alpha API', $data[0]['name']);
        $this->assertEquals('Beta API', $data[1]['name']);
        $this->assertEquals('Zebra API', $data[2]['name']);
    }

    /** @test */
    public function it_can_filter_data_sources_by_multiple_criteria()
    {
        DataSource::factory()->create([
            'name' => 'Weather API',
            'type' => 'api',
            'status' => 'active'
        ]);
        DataSource::factory()->create([
            'name' => 'Weather File',
            'type' => 'file',
            'status' => 'active'
        ]);
        
        $response = $this->getJson('/api/v1/data-sources?type=api&status=active&search=weather');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Weather API', $response->json('data.0.name'));
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $dataSource = DataSource::factory()->create();
        
        // Desautenticar
        auth()->logout();

        // Test index
        $this->getJson('/api/v1/data-sources')->assertStatus(401);
        
        // Test store
        $this->postJson('/api/v1/data-sources', [])->assertStatus(401);
        
        // Test show
        $this->getJson("/api/v1/data-sources/{$dataSource->id}")->assertStatus(401);
        
        // Test update
        $this->putJson("/api/v1/data-sources/{$dataSource->id}", [])->assertStatus(401);
        
        // Test delete
        $this->deleteJson("/api/v1/data-sources/{$dataSource->id}")->assertStatus(401);
    }

    /** @test */
    public function it_can_handle_data_source_with_metadata()
    {
        $dataSourceData = [
            'name' => 'API con Metadatos',
            'slug' => 'api-metadata',
            'type' => 'api',
            'url' => 'https://api.example.com',
            'metadata' => [
                'api_key_required' => true,
                'rate_limit' => '1000/hour',
                'version' => 'v2.1'
            ]
        ];

        $response = $this->postJson('/api/v1/data-sources', $dataSourceData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('data_sources', [
            'name' => 'API con Metadatos',
            'slug' => 'api-metadata'
        ]);
    }
}
