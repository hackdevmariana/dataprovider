<?php

use App\Models\User;
use App\Models\Achievement;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('AchievementController', function () {
    
    describe('GET /api/v1/achievements', function () {
        test('returns paginated list of achievements', function () {
            Achievement::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/achievements');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'icon',
                            'points',
                            'category',
                            'is_active',
                            'unlock_criteria'
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
        
        test('filters achievements by category', function () {
            Achievement::factory()->create(['category' => 'sustainability']);
            Achievement::factory()->create(['category' => 'social']);
            
            $response = $this->getJson('/api/v1/achievements?category=sustainability');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.category', 'sustainability');
        });
        
        test('filters achievements by is_active', function () {
            Achievement::factory()->create(['is_active' => true]);
            Achievement::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/achievements?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('filters achievements by points range', function () {
            Achievement::factory()->create(['points' => 10]);
            Achievement::factory()->create(['points' => 50]);
            
            $response = $this->getJson('/api/v1/achievements?points_min=20&points_max=100');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.points', 50);
        });
        
        test('searches achievements by name or description', function () {
            Achievement::factory()->create(['name' => 'Primer Paso Verde']);
            Achievement::factory()->create(['name' => 'Héroe Social']);
            
            $response = $this->getJson('/api/v1/achievements?search=verde');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Primer Paso Verde');
        });
        
        test('respects per_page parameter', function () {
            Achievement::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/achievements?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/achievements', function () {
        test('creates new achievement with valid data', function () {
            $achievementData = [
                'name' => 'Primer Paso Verde',
                'description' => 'Completa tu primera acción sostenible',
                'icon' => 'fa-leaf',
                'points' => 10,
                'category' => 'sustainability',
                'is_active' => true,
                'unlock_criteria' => ['actions_completed' => 1]
            ];
            
            $response = $this->postJson('/api/v1/achievements', $achievementData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Primer Paso Verde')
                ->assertJsonPath('data.category', 'sustainability')
                ->assertJsonPath('data.points', 10);
                
            $this->assertDatabaseHas('achievements', [
                'name' => 'Primer Paso Verde',
                'category' => 'sustainability',
                'points' => 10
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/achievements', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'description', 'category']);
        });
        
        test('returns 422 with invalid category', function () {
            $achievementData = [
                'name' => 'Test Achievement',
                'description' => 'Test description',
                'category' => 'invalid_category'
            ];
            
            $response = $this->postJson('/api/v1/achievements', $achievementData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 422 with negative points', function () {
            $achievementData = [
                'name' => 'Test Achievement',
                'description' => 'Test description',
                'category' => 'sustainability',
                'points' => -10
            ];
            
            $response = $this->postJson('/api/v1/achievements', $achievementData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['points']);
        });
        
        test('returns 422 with invalid unlock_criteria format', function () {
            $achievementData = [
                'name' => 'Test Achievement',
                'description' => 'Test description',
                'category' => 'sustainability',
                'unlock_criteria' => 'invalid-json'
            ];
            
            $response = $this->postJson('/api/v1/achievements', $achievementData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['unlock_criteria']);
        });
        
        test('returns 422 with duplicate name', function () {
            Achievement::factory()->create(['name' => 'Test Achievement']);
            
            $achievementData = [
                'name' => 'Test Achievement',
                'description' => 'Another description',
                'category' => 'sustainability'
            ];
            
            $response = $this->postJson('/api/v1/achievements', $achievementData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
    });
    
    describe('GET /api/v1/achievements/{id}', function () {
        test('returns achievement details', function () {
            $achievement = Achievement::factory()->create();
            
            $response = $this->getJson("/api/v1/achievements/{$achievement->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $achievement->id)
                ->assertJsonPath('data.name', $achievement->name)
                ->assertJsonPath('data.category', $achievement->category);
        });
        
        test('returns 404 for non-existent achievement', function () {
            $response = $this->getJson('/api/v1/achievements/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/achievements/{id}', function () {
        test('updates achievement with valid data', function () {
            $achievement = Achievement::factory()->create();
            $updateData = [
                'name' => 'Primer Paso Verde Actualizado',
                'points' => 15,
                'unlock_criteria' => ['actions_completed' => 2]
            ];
            
            $response = $this->putJson("/api/v1/achievements/{$achievement->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Primer Paso Verde Actualizado')
                ->assertJsonPath('data.points', 15);
                
            $this->assertDatabaseHas('achievements', [
                'id' => $achievement->id,
                'name' => 'Primer Paso Verde Actualizado',
                'points' => 15
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $achievement = Achievement::factory()->create();
            
            $response = $this->putJson("/api/v1/achievements/{$achievement->id}", [
                'category' => 'invalid_category'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 404 for non-existent achievement', function () {
            $response = $this->putJson('/api/v1/achievements/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/achievements/{id}', function () {
        test('deletes achievement successfully', function () {
            $achievement = Achievement::factory()->create();
            
            $response = $this->deleteJson("/api/v1/achievements/{$achievement->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
        });
        
        test('returns 404 for non-existent achievement', function () {
            $response = $this->deleteJson('/api/v1/achievements/999');
            
            $response->assertStatus(404);
        });
    });
});
