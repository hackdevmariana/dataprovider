<?php

use App\Models\User;
use App\Models\Alias;
use App\Models\Person;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('AliasController', function () {
    
    describe('GET /api/v1/aliases', function () {
        test('returns paginated list of aliases', function () {
            Alias::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/aliases');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'alias',
                            'aliasable_type',
                            'aliasable_id',
                            'is_active',
                            'description'
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
        
        test('filters aliases by aliasable_type', function () {
            Alias::factory()->create(['aliasable_type' => 'Person']);
            Alias::factory()->create(['aliasable_type' => 'Organization']);
            
            $response = $this->getJson('/api/v1/aliases?aliasable_type=Person');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.aliasable_type', 'Person');
        });
        
        test('filters aliases by aliasable_id', function () {
            $person = Person::factory()->create();
            Alias::factory()->create(['aliasable_id' => $person->id]);
            Alias::factory()->create(['aliasable_id' => Person::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/aliases?aliasable_id={$person->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.aliasable_id', $person->id);
        });
        
        test('filters aliases by is_active', function () {
            Alias::factory()->create(['is_active' => true]);
            Alias::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/aliases?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches aliases by name or alias', function () {
            Alias::factory()->create(['name' => 'Juan Pérez', 'alias' => 'JP']);
            Alias::factory()->create(['name' => 'María García', 'alias' => 'MG']);
            
            $response = $this->getJson('/api/v1/aliases?search=juan');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Juan Pérez');
        });
        
        test('respects per_page parameter', function () {
            Alias::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/aliases?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/aliases', function () {
        test('creates new alias with valid data', function () {
            $person = Person::factory()->create();
            $aliasData = [
                'name' => 'Juan Pérez',
                'alias' => 'JP',
                'aliasable_type' => 'Person',
                'aliasable_id' => $person->id,
                'description' => 'Alias común para Juan Pérez',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/aliases', $aliasData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Juan Pérez')
                ->assertJsonPath('data.alias', 'JP')
                ->assertJsonPath('data.aliasable_type', 'Person');
                
            $this->assertDatabaseHas('aliases', [
                'name' => 'Juan Pérez',
                'alias' => 'JP',
                'aliasable_type' => 'Person'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/aliases', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'alias', 'aliasable_type', 'aliasable_id']);
        });
        
        test('returns 422 with invalid aliasable_id', function () {
            $aliasData = [
                'name' => 'Test Alias',
                'alias' => 'TA',
                'aliasable_type' => 'Person',
                'aliasable_id' => 999
            ];
            
            $response = $this->postJson('/api/v1/aliases', $aliasData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['aliasable_id']);
        });
        
        test('returns 422 with duplicate alias for same aliasable', function () {
            $person = Person::factory()->create();
            Alias::factory()->create([
                'aliasable_type' => 'Person',
                'aliasable_id' => $person->id,
                'alias' => 'JP'
            ]);
            
            $aliasData = [
                'name' => 'Another Name',
                'alias' => 'JP',
                'aliasable_type' => 'Person',
                'aliasable_id' => $person->id
            ];
            
            $response = $this->postJson('/api/v1/aliases', $aliasData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['alias']);
        });
    });
    
    describe('GET /api/v1/aliases/{id}', function () {
        test('returns alias details', function () {
            $alias = Alias::factory()->create();
            
            $response = $this->getJson("/api/v1/aliases/{$alias->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $alias->id)
                ->assertJsonPath('data.name', $alias->name)
                ->assertJsonPath('data.alias', $alias->alias);
        });
        
        test('returns 404 for non-existent alias', function () {
            $response = $this->getJson('/api/v1/aliases/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/aliases/{id}', function () {
        test('updates alias with valid data', function () {
            $alias = Alias::factory()->create();
            $updateData = [
                'name' => 'Juan Carlos Pérez',
                'alias' => 'JCP'
            ];
            
            $response = $this->putJson("/api/v1/aliases/{$alias->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Juan Carlos Pérez')
                ->assertJsonPath('data.alias', 'JCP');
                
            $this->assertDatabaseHas('aliases', [
                'id' => $alias->id,
                'name' => 'Juan Carlos Pérez',
                'alias' => 'JCP'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $alias = Alias::factory()->create();
            
            $response = $this->putJson("/api/v1/aliases/{$alias->id}", [
                'aliasable_id' => 999
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['aliasable_id']);
        });
        
        test('returns 404 for non-existent alias', function () {
            $response = $this->putJson('/api/v1/aliases/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/aliases/{id}', function () {
        test('deletes alias successfully', function () {
            $alias = Alias::factory()->create();
            
            $response = $this->deleteJson("/api/v1/aliases/{$alias->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('aliases', ['id' => $alias->id]);
        });
        
        test('returns 404 for non-existent alias', function () {
            $response = $this->deleteJson('/api/v1/aliases/999');
            
            $response->assertStatus(404);
        });
    });
});
