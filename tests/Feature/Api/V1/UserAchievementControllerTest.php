<?php

use App\Models\User;
use App\Models\UserAchievement;
use App\Models\Achievement;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('UserAchievementController', function () {
    
    describe('GET /api/v1/user-achievements', function () {
        test('returns paginated list of user achievements', function () {
            UserAchievement::factory()->count(3)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/user-achievements');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'achievement_id',
                            'status',
                            'unlocked_at',
                            'progress',
                            'achievement'
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
        
        test('filters user achievements by achievement_id', function () {
            $achievement = Achievement::factory()->create();
            UserAchievement::factory()->create([
                'user_id' => $this->user->id,
                'achievement_id' => $achievement->id
            ]);
            UserAchievement::factory()->create([
                'user_id' => $this->user->id,
                'achievement_id' => Achievement::factory()->create()->id
            ]);
            
            $response = $this->getJson("/api/v1/user-achievements?achievement_id={$achievement->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.achievement_id', $achievement->id);
        });
        
        test('filters user achievements by status', function () {
            UserAchievement::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'unlocked'
            ]);
            UserAchievement::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'locked'
            ]);
            
            $response = $this->getJson('/api/v1/user-achievements?status=unlocked');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'unlocked');
        });
        
        test('filters user achievements by date range', function () {
            UserAchievement::factory()->create([
                'user_id' => $this->user->id,
                'unlocked_at' => '2024-01-01 10:00:00'
            ]);
            UserAchievement::factory()->create([
                'user_id' => $this->user->id,
                'unlocked_at' => '2024-02-01 10:00:00'
            ]);
            
            $response = $this->getJson('/api/v1/user-achievements?date_from=2024-01-01&date_to=2024-01-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('only returns achievements for authenticated user', function () {
            $otherUser = User::factory()->create();
            UserAchievement::factory()->create(['user_id' => $otherUser->id]);
            UserAchievement::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/user-achievements');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
        
        test('respects per_page parameter', function () {
            UserAchievement::factory()->count(15)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/user-achievements?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/user-achievements', function () {
        test('creates new user achievement with valid data', function () {
            $achievement = Achievement::factory()->create();
            $userAchievementData = [
                'achievement_id' => $achievement->id,
                'status' => 'unlocked',
                'progress' => 100
            ];
            
            $response = $this->postJson('/api/v1/user-achievements', $userAchievementData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.achievement_id', $achievement->id)
                ->assertJsonPath('data.status', 'unlocked')
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('user_achievements', [
                'achievement_id' => $achievement->id,
                'user_id' => $this->user->id,
                'status' => 'unlocked'
            ]);
        });
        
        test('sets unlocked_at when status is unlocked', function () {
            $achievement = Achievement::factory()->create();
            $userAchievementData = [
                'achievement_id' => $achievement->id,
                'status' => 'unlocked'
            ];
            
            $response = $this->postJson('/api/v1/user-achievements', $userAchievementData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.unlocked_at', function ($date) {
                    return !is_null($date);
                });
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/user-achievements', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['achievement_id', 'status']);
        });
        
        test('returns 422 with invalid status', function () {
            $achievement = Achievement::factory()->create();
            $userAchievementData = [
                'achievement_id' => $achievement->id,
                'status' => 'invalid_status'
            ];
            
            $response = $this->postJson('/api/v1/user-achievements', $userAchievementData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 422 with invalid achievement_id', function () {
            $userAchievementData = [
                'achievement_id' => 999,
                'status' => 'unlocked'
            ];
            
            $response = $this->postJson('/api/v1/user-achievements', $userAchievementData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['achievement_id']);
        });
    });
    
    describe('GET /api/v1/user-achievements/{id}', function () {
        test('returns user achievement details', function () {
            $userAchievement = UserAchievement::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/user-achievements/{$userAchievement->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $userAchievement->id)
                ->assertJsonPath('data.user_id', $this->user->id);
        });
        
        test('returns 404 for non-existent user achievement', function () {
            $response = $this->getJson('/api/v1/user-achievements/999');
            
            $response->assertStatus(404);
        });
        
        test('returns 404 for achievement belonging to other user', function () {
            $otherUser = User::factory()->create();
            $userAchievement = UserAchievement::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->getJson("/api/v1/user-achievements/{$userAchievement->id}");
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/user-achievements/{id}', function () {
        test('updates user achievement with valid data', function () {
            $userAchievement = UserAchievement::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'status' => 'completed',
                'progress' => 100
            ];
            
            $response = $this->putJson("/api/v1/user-achievements/{$userAchievement->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.status', 'completed')
                ->assertJsonPath('data.progress', 100);
                
            $this->assertDatabaseHas('user_achievements', [
                'id' => $userAchievement->id,
                'status' => 'completed',
                'progress' => 100
            ]);
        });
        
        test('sets unlocked_at when status changes to unlocked', function () {
            $userAchievement = UserAchievement::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'locked'
            ]);
            
            $response = $this->putJson("/api/v1/user-achievements/{$userAchievement->id}", [
                'status' => 'unlocked'
            ]);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.unlocked_at', function ($date) {
                    return !is_null($date);
                });
        });
        
        test('returns 422 with invalid data', function () {
            $userAchievement = UserAchievement::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/user-achievements/{$userAchievement->id}", [
                'status' => 'invalid_status'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 404 for achievement belonging to other user', function () {
            $otherUser = User::factory()->create();
            $userAchievement = UserAchievement::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->putJson("/api/v1/user-achievements/{$userAchievement->id}", [
                'status' => 'completed'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/user-achievements/{id}', function () {
        test('deletes user achievement successfully', function () {
            $userAchievement = UserAchievement::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/user-achievements/{$userAchievement->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('user_achievements', ['id' => $userAchievement->id]);
        });
        
        test('returns 404 for achievement belonging to other user', function () {
            $otherUser = User::factory()->create();
            $userAchievement = UserAchievement::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->deleteJson("/api/v1/user-achievements/{$userAchievement->id}");
            
            $response->assertStatus(404);
        });
    });
});
