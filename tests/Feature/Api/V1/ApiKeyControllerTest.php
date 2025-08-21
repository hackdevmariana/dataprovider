<?php

use App\Models\User;
use App\Models\ApiKey;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('ApiKeyController', function () {
    
    describe('GET /api/v1/api-keys', function () {
        test('returns paginated list of API keys', function () {
            ApiKey::factory()->count(3)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/api-keys');
            
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
        });
        
        test('filters API keys by is_active', function () {
            ApiKey::factory()->create([
                'user_id' => $this->user->id,
                'is_active' => true
            ]);
            ApiKey::factory()->create([
                'user_id' => $this->user->id,
                'is_active' => false
            ]);
            
            $response = $this->getJson('/api/v1/api-keys?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches API keys by name', function () {
            ApiKey::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Production API Key'
            ]);
            ApiKey::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Development API Key'
            ]);
            
            $response = $this->getJson('/api/v1/api-keys?search=production');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Production API Key');
        });
        
        test('only returns API keys for authenticated user', function () {
            $otherUser = User::factory()->create();
            ApiKey::factory()->create(['user_id' => $otherUser->id]);
            ApiKey::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/api-keys');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
        
        test('respects per_page parameter', function () {
            ApiKey::factory()->count(15)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/api-keys?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/api-keys', function () {
        test('creates new API key with valid data', function () {
            $apiKeyData = [
                'name' => 'Production API Key',
                'description' => 'API key for production environment',
                'is_active' => true,
                'expires_at' => now()->addYear()
            ];
            
            $response = $this->postJson('/api/v1/api-keys', $apiKeyData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Production API Key')
                ->assertJsonPath('data.is_active', true)
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('api_keys', [
                'name' => 'Production API Key',
                'user_id' => $this->user->id,
                'is_active' => true
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/api-keys', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
        
        test('returns 422 with invalid expiration date', function () {
            $apiKeyData = [
                'name' => 'Test API Key',
                'expires_at' => 'invalid-date'
            ];
            
            $response = $this->postJson('/api/v1/api-keys', $apiKeyData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['expires_at']);
        });
        
        test('returns 422 with past expiration date', function () {
            $apiKeyData = [
                'name' => 'Test API Key',
                'expires_at' => now()->subDay()
            ];
            
            $response = $this->postJson('/api/v1/api-keys', $apiKeyData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['expires_at']);
        });
    });
    
    describe('GET /api/v1/api-keys/{id}', function () {
        test('returns API key details', function () {
            $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/api-keys/{$apiKey->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $apiKey->id)
                ->assertJsonPath('data.name', $apiKey->name)
                ->assertJsonPath('data.user_id', $this->user->id);
        });
        
        test('returns 404 for non-existent API key', function () {
            $response = $this->getJson('/api/v1/api-keys/999');
            
            $response->assertStatus(404);
        });
        
        test('returns 404 for API key belonging to other user', function () {
            $otherUser = User::factory()->create();
            $apiKey = ApiKey::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->getJson("/api/v1/api-keys/{$apiKey->id}");
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/api-keys/{id}', function () {
        test('updates API key with valid data', function () {
            $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'name' => 'Updated Production API Key',
                'is_active' => false
            ];
            
            $response = $this->putJson("/api/v1/api-keys/{$apiKey->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Updated Production API Key')
                ->assertJsonPath('data.is_active', false);
                
            $this->assertDatabaseHas('api_keys', [
                'id' => $apiKey->id,
                'name' => 'Updated Production API Key',
                'is_active' => false
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/api-keys/{$apiKey->id}", [
                'expires_at' => 'invalid-date'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['expires_at']);
        });
        
        test('returns 404 for API key belonging to other user', function () {
            $otherUser = User::factory()->create();
            $apiKey = ApiKey::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->putJson("/api/v1/api-keys/{$apiKey->id}", [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/api-keys/{id}', function () {
        test('deletes API key successfully', function () {
            $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/api-keys/{$apiKey->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('api_keys', ['id' => $apiKey->id]);
        });
        
        test('returns 404 for API key belonging to other user', function () {
            $otherUser = User::factory()->create();
            $apiKey = ApiKey::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->deleteJson("/api/v1/api-keys/{$apiKey->id}");
            
            $response->assertStatus(404);
        });
    });
});
