<?php

use App\Models\User;
use App\Models\RelationshipType;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('RelationshipTypeController', function () {
    
    describe('GET /api/v1/relationship-types', function () {
        test('returns paginated list of relationship types', function () {
            RelationshipType::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/relationship-types');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'is_active',
                            'sort_order'
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
        
        test('filters relationship types by is_active', function () {
            RelationshipType::factory()->create(['is_active' => true]);
            RelationshipType::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/relationship-types?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches relationship types by name or description', function () {
            RelationshipType::factory()->create(['name' => 'Padre']);
            RelationshipType::factory()->create(['name' => 'Hermano']);
            
            $response = $this->getJson('/api/v1/relationship-types?search=padre');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Padre');
        });
        
        test('respects per_page parameter', function () {
            RelationshipType::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/relationship-types?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/relationship-types', function () {
        test('creates new relationship type with valid data', function () {
            $relationshipTypeData = [
                'name' => 'Padre',
                'description' => 'Relación de padre a hijo',
                'is_active' => true,
                'sort_order' => 1
            ];
            
            $response = $this->postJson('/api/v1/relationship-types', $relationshipTypeData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Padre')
                ->assertJsonPath('data.description', 'Relación de padre a hijo')
                ->assertJsonPath('data.is_active', true);
                
            $this->assertDatabaseHas('relationship_types', [
                'name' => 'Padre',
                'description' => 'Relación de padre a hijo',
                'is_active' => true
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/relationship-types', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
        
        test('returns 422 with duplicate name', function () {
            RelationshipType::factory()->create(['name' => 'Padre']);
            
            $relationshipTypeData = [
                'name' => 'Padre',
                'description' => 'Another description'
            ];
            
            $response = $this->postJson('/api/v1/relationship-types', $relationshipTypeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
        
        test('returns 422 with negative sort order', function () {
            $relationshipTypeData = [
                'name' => 'Test Type',
                'sort_order' => -1
            ];
            
            $response = $this->postJson('/api/v1/relationship-types', $relationshipTypeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['sort_order']);
        });
    });
    
    describe('GET /api/v1/relationship-types/{id}', function () {
        test('returns relationship type details', function () {
            $relationshipType = RelationshipType::factory()->create();
            
            $response = $this->getJson("/api/v1/relationship-types/{$relationshipType->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $relationshipType->id)
                ->assertJsonPath('data.name', $relationshipType->name)
                ->assertJsonPath('data.description', $relationshipType->description);
        });
        
        test('returns 404 for non-existent relationship type', function () {
            $response = $this->getJson('/api/v1/relationship-types/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/relationship-types/{id}', function () {
        test('updates relationship type with valid data', function () {
            $relationshipType = RelationshipType::factory()->create();
            $updateData = [
                'name' => 'Padre Biológico',
                'description' => 'Relación de padre biológico a hijo'
            ];
            
            $response = $this->putJson("/api/v1/relationship-types/{$relationshipType->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Padre Biológico')
                ->assertJsonPath('data.description', 'Relación de padre biológico a hijo');
                
            $this->assertDatabaseHas('relationship_types', [
                'id' => $relationshipType->id,
                'name' => 'Padre Biológico',
                'description' => 'Relación de padre biológico a hijo'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $relationshipType = RelationshipType::factory()->create();
            
            $response = $this->putJson("/api/v1/relationship-types/{$relationshipType->id}", [
                'sort_order' => -1
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['sort_order']);
        });
        
        test('returns 404 for non-existent relationship type', function () {
            $response = $this->putJson('/api/v1/relationship-types/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/relationship-types/{id}', function () {
        test('deletes relationship type successfully', function () {
            $relationshipType = RelationshipType::factory()->create();
            
            $response = $this->deleteJson("/api/v1/relationship-types/{$relationshipType->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('relationship_types', ['id' => $relationshipType->id]);
        });
        
        test('returns 404 for non-existent relationship type', function () {
            $response = $this->deleteJson('/api/v1/relationship-types/999');
            
            $response->assertStatus(404);
        });
    });
});
