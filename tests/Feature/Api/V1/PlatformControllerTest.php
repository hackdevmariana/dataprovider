<?php

use App\Models\User;
use App\Models\Platform;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('PlatformController', function () {
    
    describe('GET /api/v1/platforms', function () {
        test('returns paginated list of platforms', function () {
            Platform::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/platforms');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'type',
                            'version',
                            'is_active'
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
        
        test('filters platforms by type', function () {
            Platform::factory()->create(['type' => 'web']);
            Platform::factory()->create(['type' => 'mobile']);
            
            $response = $this->getJson('/api/v1/platforms?type=web');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.type', 'web');
        });
        
        test('filters platforms by is_active', function () {
            Platform::factory()->create(['is_active' => true]);
            Platform::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/platforms?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches platforms by name or description', function () {
            Platform::factory()->create(['name' => 'iOS Platform']);
            Platform::factory()->create(['name' => 'Android Platform']);
            
            $response = $this->getJson('/api/v1/platforms?search=ios');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'iOS Platform');
        });
        
        test('respects per_page parameter', function () {
            Platform::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/platforms?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/platforms', function () {
        test('creates new platform with valid data', function () {
            $platformData = [
                'name' => 'iOS Platform',
                'slug' => 'ios-platform',
                'type' => 'mobile',
                'version' => '15.0',
                'description' => 'Platform for iOS devices',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/platforms', $platformData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'iOS Platform')
                ->assertJsonPath('data.type', 'mobile')
                ->assertJsonPath('data.version', '15.0');
                
            $this->assertDatabaseHas('platforms', [
                'name' => 'iOS Platform',
                'slug' => 'ios-platform',
                'type' => 'mobile'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/platforms', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'type']);
        });
        
        test('returns 422 with invalid type', function () {
            $platformData = [
                'name' => 'Test Platform',
                'slug' => 'test-platform',
                'type' => 'invalid_type'
            ];
            
            $response = $this->postJson('/api/v1/platforms', $platformData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 422 with duplicate slug', function () {
            Platform::factory()->create(['slug' => 'test-platform']);
            
            $platformData = [
                'name' => 'Another Platform',
                'slug' => 'test-platform',
                'type' => 'web'
            ];
            
            $response = $this->postJson('/api/v1/platforms', $platformData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
        });
        
        test('returns 422 with invalid version format', function () {
            $platformData = [
                'name' => 'Test Platform',
                'slug' => 'test-platform',
                'type' => 'web',
                'version' => 'invalid-version'
            ];
            
            $response = $this->postJson('/api/v1/platforms', $platformData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['version']);
        });
    });
    
    describe('GET /api/v1/platforms/{id}', function () {
        test('returns platform details', function () {
            $platform = Platform::factory()->create();
            
            $response = $this->getJson("/api/v1/platforms/{$platform->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $platform->id)
                ->assertJsonPath('data.name', $platform->name)
                ->assertJsonPath('data.type', $platform->type);
        });
        
        test('returns 404 for non-existent platform', function () {
            $response = $this->getJson('/api/v1/platforms/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/platforms/{id}', function () {
        test('updates platform with valid data', function () {
            $platform = Platform::factory()->create();
            $updateData = [
                'name' => 'iOS Platform Updated',
                'version' => '16.0'
            ];
            
            $response = $this->putJson("/api/v1/platforms/{$platform->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'iOS Platform Updated')
                ->assertJsonPath('data.version', '16.0');
                
            $this->assertDatabaseHas('platforms', [
                'id' => $platform->id,
                'name' => 'iOS Platform Updated',
                'version' => '16.0'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $platform = Platform::factory()->create();
            
            $response = $this->putJson("/api/v1/platforms/{$platform->id}", [
                'type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 404 for non-existent platform', function () {
            $response = $this->putJson('/api/v1/platforms/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/platforms/{id}', function () {
        test('deletes platform successfully', function () {
            $platform = Platform::factory()->create();
            
            $response = $this->deleteJson("/api/v1/platforms/{$platform->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('platforms', ['id' => $platform->id]);
        });
        
        test('returns 404 for non-existent platform', function () {
            $response = $this->deleteJson('/api/v1/platforms/999');
            
            $response->assertStatus(404);
        });
    });
});
