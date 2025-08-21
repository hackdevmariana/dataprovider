<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class ApiKeysTest extends TestCase
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
    public function it_can_list_api_keys_with_pagination()
    {
        ApiKey::factory()->count(25)->create(['user_id' => $this->user->id]);
        
        $response = $this->getJson('/api/v1/api-keys?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'key_prefix',
                            'is_active',
                            'last_used_at',
                            'expires_at'
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
    public function it_can_filter_api_keys_by_active_status()
    {
        ApiKey::factory()->count(3)->create(['user_id' => $this->user->id, 'is_active' => true]);
        ApiKey::factory()->count(2)->create(['user_id' => $this->user->id, 'is_active' => false]);
        
        $response = $this->getJson('/api/v1/api-keys?is_active=true');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_search_api_keys_by_name()
    {
        ApiKey::factory()->create(['name' => 'Test API Key', 'user_id' => $this->user->id]);
        ApiKey::factory()->create(['name' => 'Production Key', 'user_id' => $this->user->id]);
        
        $response = $this->getJson('/api/v1/api-keys?search=test');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Test API Key', $response->json('data.0.name'));
    }

    /** @test */
    public function it_can_create_a_new_api_key()
    {
        $apiKeyData = [
            'name' => 'Nueva API Key',
            'description' => 'Descripción de la nueva API key',
            'permissions' => ['read', 'write'],
            'expires_at' => '2025-12-31'
        ];

        $response = $this->postJson('/api/v1/api-keys', $apiKeyData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'key_prefix',
                        'is_active',
                        'permissions',
                        'expires_at',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('api_keys', [
            'name' => 'Nueva API Key',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_api_key()
    {
        $response = $this->postJson('/api/v1/api-keys', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_validates_permissions_format_when_creating_api_key()
    {
        $response = $this->postJson('/api/v1/api-keys', [
            'name' => 'Test Key',
            'permissions' => 'invalid_permissions'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['permissions']);
    }

    /** @test */
    public function it_validates_expires_at_date_when_creating_api_key()
    {
        $response = $this->postJson('/api/v1/api-keys', [
            'name' => 'Test Key',
            'expires_at' => 'invalid-date'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['expires_at']);
    }

    /** @test */
    public function it_can_show_api_key_details()
    {
        $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/api-keys/{$apiKey->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $apiKey->id,
                        'name' => $apiKey->name
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_api_key()
    {
        $response = $this->getJson('/api/v1/api-keys/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_cannot_show_api_key_from_other_user()
    {
        $otherUser = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/v1/api-keys/{$apiKey->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_api_key()
    {
        $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'name' => 'API Key Actualizada',
            'description' => 'Descripción actualizada',
            'permissions' => ['read', 'write', 'delete']
        ];

        $response = $this->putJson("/api/v1/api-keys/{$apiKey->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'API Key Actualizada',
                        'description' => 'Descripción actualizada'
                    ]
                ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey->id,
            'name' => 'API Key Actualizada'
        ]);
    }

    /** @test */
    public function it_cannot_update_api_key_from_other_user()
    {
        $otherUser = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->putJson("/api/v1/api-keys/{$apiKey->id}", [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_delete_api_key()
    {
        $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/api-keys/{$apiKey->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('api_keys', ['id' => $apiKey->id]);
    }

    /** @test */
    public function it_cannot_delete_api_key_from_other_user()
    {
        $otherUser = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/v1/api-keys/{$apiKey->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_api_key()
    {
        $response = $this->deleteJson('/api/v1/api-keys/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        ApiKey::factory()->count(150)->create(['user_id' => $this->user->id]);
        
        $response = $this->getJson('/api/v1/api-keys?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_api_keys_ordered_by_created_at_desc()
    {
        $oldKey = ApiKey::factory()->create(['user_id' => $this->user->id, 'created_at' => now()->subDays(2)]);
        $newKey = ApiKey::factory()->create(['user_id' => $this->user->id, 'created_at' => now()]);
        
        $response = $this->getJson('/api/v1/api-keys');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals($newKey->id, $data[0]['id']);
        $this->assertEquals($oldKey->id, $data[1]['id']);
    }

    /** @test */
    public function it_can_filter_api_keys_by_multiple_criteria()
    {
        ApiKey::factory()->create([
            'name' => 'Test Key',
            'is_active' => true,
            'user_id' => $this->user->id
        ]);
        ApiKey::factory()->create([
            'name' => 'Test Key Inactive',
            'is_active' => false,
            'user_id' => $this->user->id
        ]);
        
        $response = $this->getJson('/api/v1/api-keys?is_active=true&search=test');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Test Key', $response->json('data.0.name'));
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);
        
        // Desautenticar
        auth()->logout();

        // Test index
        $this->getJson('/api/v1/api-keys')->assertStatus(401);
        
        // Test store
        $this->postJson('/api/v1/api-keys', [])->assertStatus(401);
        
        // Test show
        $this->getJson("/api/v1/api-keys/{$apiKey->id}")->assertStatus(401);
        
        // Test update
        $this->putJson("/api/v1/api-keys/{$apiKey->id}", [])->assertStatus(401);
        
        // Test delete
        $this->deleteJson("/api/v1/api-keys/{$apiKey->id}")->assertStatus(401);
    }

    /** @test */
    public function it_can_handle_api_key_with_metadata()
    {
        $apiKeyData = [
            'name' => 'API Key con Metadatos',
            'description' => 'Descripción de la API key',
            'permissions' => ['read', 'write'],
            'metadata' => [
                'ip_whitelist' => ['192.168.1.1', '10.0.0.1'],
                'rate_limit' => '1000/hour',
                'allowed_origins' => ['https://example.com']
            ]
        ];

        $response = $this->postJson('/api/v1/api-keys', $apiKeyData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('api_keys', [
            'name' => 'API Key con Metadatos'
        ]);
    }

    /** @test */
    public function it_generates_unique_key_prefix()
    {
        $apiKeyData = [
            'name' => 'Test Key 1'
        ];

        $response1 = $this->postJson('/api/v1/api-keys', $apiKeyData);
        $response2 = $this->postJson('/api/v1/api-keys', ['name' => 'Test Key 2']);

        $response1->assertStatus(201);
        $response2->assertStatus(201);

        $key1 = $response1->json('data.key_prefix');
        $key2 = $response2->json('data.key_prefix');

        $this->assertNotEquals($key1, $key2);
    }
}
