<?php

use App\Models\User;
use App\Models\Challenge;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('ChallengeController', function () {
    
    describe('GET /api/v1/challenges', function () {
        test('returns paginated list of challenges', function () {
            Challenge::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/challenges');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'category',
                            'difficulty',
                            'points_reward',
                            'is_active',
                            'start_date',
                            'end_date'
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
        
        test('filters challenges by category', function () {
            Challenge::factory()->create(['category' => 'sustainability']);
            Challenge::factory()->create(['category' => 'social']);
            
            $response = $this->getJson('/api/v1/challenges?category=sustainability');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.category', 'sustainability');
        });
        
        test('filters challenges by difficulty', function () {
            Challenge::factory()->create(['difficulty' => 'easy']);
            Challenge::factory()->create(['difficulty' => 'hard']);
            
            $response = $this->getJson('/api/v1/challenges?difficulty=easy');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.difficulty', 'easy');
        });
        
        test('filters challenges by is_active', function () {
            Challenge::factory()->create(['is_active' => true]);
            Challenge::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/challenges?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('filters challenges by date range', function () {
            Challenge::factory()->create(['start_date' => '2024-01-01']);
            Challenge::factory()->create(['start_date' => '2024-02-01']);
            
            $response = $this->getJson('/api/v1/challenges?start_date_from=2024-01-01&start_date_to=2024-01-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('searches challenges by title or description', function () {
            Challenge::factory()->create(['title' => 'Desafío Verde']);
            Challenge::factory()->create(['title' => 'Desafío Social']);
            
            $response = $this->getJson('/api/v1/challenges?search=verde');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Desafío Verde');
        });
        
        test('respects per_page parameter', function () {
            Challenge::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/challenges?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/challenges', function () {
        test('creates new challenge with valid data', function () {
            $challengeData = [
                'title' => 'Desafío Verde',
                'description' => 'Completa 10 acciones sostenibles en un mes',
                'category' => 'sustainability',
                'difficulty' => 'medium',
                'points_reward' => 100,
                'is_active' => true,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'requirements' => ['actions_count' => 10]
            ];
            
            $response = $this->postJson('/api/v1/challenges', $challengeData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.title', 'Desafío Verde')
                ->assertJsonPath('data.category', 'sustainability')
                ->assertJsonPath('data.difficulty', 'medium');
                
            $this->assertDatabaseHas('challenges', [
                'title' => 'Desafío Verde',
                'category' => 'sustainability',
                'difficulty' => 'medium'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/challenges', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'description', 'category', 'difficulty']);
        });
        
        test('returns 422 with invalid category', function () {
            $challengeData = [
                'title' => 'Test Challenge',
                'description' => 'Test description',
                'category' => 'invalid_category',
                'difficulty' => 'easy'
            ];
            
            $response = $this->postJson('/api/v1/challenges', $challengeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 422 with invalid difficulty', function () {
            $challengeData = [
                'title' => 'Test Challenge',
                'description' => 'Test description',
                'category' => 'sustainability',
                'difficulty' => 'invalid_difficulty'
            ];
            
            $response = $this->postJson('/api/v1/challenges', $challengeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['difficulty']);
        });
        
        test('returns 422 with negative points_reward', function () {
            $challengeData = [
                'title' => 'Test Challenge',
                'description' => 'Test description',
                'category' => 'sustainability',
                'difficulty' => 'easy',
                'points_reward' => -50
            ];
            
            $response = $this->postJson('/api/v1/challenges', $challengeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['points_reward']);
        });
        
        test('returns 422 with end_date before start_date', function () {
            $challengeData = [
                'title' => 'Test Challenge',
                'description' => 'Test description',
                'category' => 'sustainability',
                'difficulty' => 'easy',
                'start_date' => '2024-01-01',
                'end_date' => '2023-12-31'
            ];
            
            $response = $this->postJson('/api/v1/challenges', $challengeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['end_date']);
        });
        
        test('returns 422 with invalid requirements format', function () {
            $challengeData = [
                'title' => 'Test Challenge',
                'description' => 'Test description',
                'category' => 'sustainability',
                'difficulty' => 'easy',
                'requirements' => 'invalid-json'
            ];
            
            $response = $this->postJson('/api/v1/challenges', $challengeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['requirements']);
        });
    });
    
    describe('GET /api/v1/challenges/{id}', function () {
        test('returns challenge details', function () {
            $challenge = Challenge::factory()->create();
            
            $response = $this->getJson("/api/v1/challenges/{$challenge->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $challenge->id)
                ->assertJsonPath('data.title', $challenge->title)
                ->assertJsonPath('data.category', $challenge->category);
        });
        
        test('returns 404 for non-existent challenge', function () {
            $response = $this->getJson('/api/v1/challenges/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/challenges/{id}', function () {
        test('updates challenge with valid data', function () {
            $challenge = Challenge::factory()->create();
            $updateData = [
                'title' => 'Desafío Verde Actualizado',
                'points_reward' => 150,
                'requirements' => ['actions_count' => 15]
            ];
            
            $response = $this->putJson("/api/v1/challenges/{$challenge->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.title', 'Desafío Verde Actualizado')
                ->assertJsonPath('data.points_reward', 150);
                
            $this->assertDatabaseHas('challenges', [
                'id' => $challenge->id,
                'title' => 'Desafío Verde Actualizado',
                'points_reward' => 150
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $challenge = Challenge::factory()->create();
            
            $response = $this->putJson("/api/v1/challenges/{$challenge->id}", [
                'category' => 'invalid_category'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 404 for non-existent challenge', function () {
            $response = $this->putJson('/api/v1/challenges/999', [
                'title' => 'Updated Title'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/challenges/{id}', function () {
        test('deletes challenge successfully', function () {
            $challenge = Challenge::factory()->create();
            
            $response = $this->deleteJson("/api/v1/challenges/{$challenge->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('challenges', ['id' => $challenge->id]);
        });
        
        test('returns 404 for non-existent challenge', function () {
            $response = $this->deleteJson('/api/v1/challenges/999');
            
            $response->assertStatus(404);
        });
    });
});
