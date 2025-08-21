<?php

use App\Models\User;
use App\Models\UserChallenge;
use App\Models\Challenge;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('UserChallengeController', function () {
    
    describe('GET /api/v1/user-challenges', function () {
        test('returns paginated list of user challenges', function () {
            UserChallenge::factory()->count(3)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/user-challenges');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'challenge_id',
                            'status',
                            'started_at',
                            'progress',
                            'challenge'
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
        
        test('filters user challenges by challenge_id', function () {
            $challenge = Challenge::factory()->create();
            UserChallenge::factory()->create([
                'user_id' => $this->user->id,
                'challenge_id' => $challenge->id
            ]);
            UserChallenge::factory()->create([
                'user_id' => $this->user->id,
                'challenge_id' => Challenge::factory()->create()->id
            ]);
            
            $response = $this->getJson("/api/v1/user-challenges?challenge_id={$challenge->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.challenge_id', $challenge->id);
        });
        
        test('filters user challenges by status', function () {
            UserChallenge::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'active'
            ]);
            UserChallenge::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'completed'
            ]);
            
            $response = $this->getJson('/api/v1/user-challenges?status=active');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'active');
        });
        
        test('filters user challenges by date range', function () {
            UserChallenge::factory()->create([
                'user_id' => $this->user->id,
                'started_at' => '2024-01-01 10:00:00'
            ]);
            UserChallenge::factory()->create([
                'user_id' => $this->user->id,
                'started_at' => '2024-02-01 10:00:00'
            ]);
            
            $response = $this->getJson('/api/v1/user-challenges?date_from=2024-01-01&date_to=2024-01-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('only returns challenges for authenticated user', function () {
            $otherUser = User::factory()->create();
            UserChallenge::factory()->create(['user_id' => $otherUser->id]);
            UserChallenge::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/user-challenges');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
        
        test('respects per_page parameter', function () {
            UserChallenge::factory()->count(15)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/user-challenges?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/user-challenges', function () {
        test('creates new user challenge with valid data', function () {
            $challenge = Challenge::factory()->create();
            $userChallengeData = [
                'challenge_id' => $challenge->id,
                'status' => 'active',
                'progress' => 0
            ];
            
            $response = $this->postJson('/api/v1/user-challenges', $userChallengeData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.challenge_id', $challenge->id)
                ->assertJsonPath('data.status', 'active')
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('user_challenges', [
                'challenge_id' => $challenge->id,
                'user_id' => $this->user->id,
                'status' => 'active'
            ]);
        });
        
        test('sets started_at when creating challenge', function () {
            $challenge = Challenge::factory()->create();
            $userChallengeData = [
                'challenge_id' => $challenge->id,
                'status' => 'active'
            ];
            
            $response = $this->postJson('/api/v1/user-challenges', $userChallengeData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.started_at', function ($date) {
                    return !is_null($date);
                });
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/user-challenges', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['challenge_id', 'status']);
        });
        
        test('returns 422 with invalid status', function () {
            $challenge = Challenge::factory()->create();
            $userChallengeData = [
                'challenge_id' => $challenge->id,
                'status' => 'invalid_status'
            ];
            
            $response = $this->postJson('/api/v1/user-challenges', $userChallengeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 422 with invalid challenge_id', function () {
            $userChallengeData = [
                'challenge_id' => 999,
                'status' => 'active'
            ];
            
            $response = $this->postJson('/api/v1/user-challenges', $userChallengeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['challenge_id']);
        });
    });
    
    describe('GET /api/v1/user-challenges/{id}', function () {
        test('returns user challenge details', function () {
            $userChallenge = UserChallenge::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/user-challenges/{$userChallenge->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $userChallenge->id)
                ->assertJsonPath('data.user_id', $this->user->id);
        });
        
        test('returns 404 for non-existent user challenge', function () {
            $response = $this->getJson('/api/v1/user-challenges/999');
            
            $response->assertStatus(404);
        });
        
        test('returns 404 for challenge belonging to other user', function () {
            $otherUser = User::factory()->create();
            $userChallenge = UserChallenge::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->getJson("/api/v1/user-challenges/{$userChallenge->id}");
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/user-challenges/{id}', function () {
        test('updates user challenge with valid data', function () {
            $userChallenge = UserChallenge::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'status' => 'completed',
                'progress' => 100
            ];
            
            $response = $this->putJson("/api/v1/user-challenges/{$userChallenge->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.status', 'completed')
                ->assertJsonPath('data.progress', 100);
                
            $this->assertDatabaseHas('user_challenges', [
                'id' => $userChallenge->id,
                'status' => 'completed',
                'progress' => 100
            ]);
        });
        
        test('sets completed_at when status changes to completed', function () {
            $userChallenge = UserChallenge::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'active'
            ]);
            
            $response = $this->putJson("/api/v1/user-challenges/{$userChallenge->id}", [
                'status' => 'completed'
            ]);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.completed_at', function ($date) {
                    return !is_null($date);
                });
        });
        
        test('returns 422 with invalid data', function () {
            $userChallenge = UserChallenge::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/user-challenges/{$userChallenge->id}", [
                'status' => 'invalid_status'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 404 for challenge belonging to other user', function () {
            $otherUser = User::factory()->create();
            $userChallenge = UserChallenge::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->putJson("/api/v1/user-challenges/{$userChallenge->id}", [
                'status' => 'completed'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/user-challenges/{id}', function () {
        test('deletes user challenge successfully', function () {
            $userChallenge = UserChallenge::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/user-challenges/{$userChallenge->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('user_challenges', ['id' => $userChallenge->id]);
        });
        
        test('returns 404 for challenge belonging to other user', function () {
            $otherUser = User::factory()->create();
            $userChallenge = UserChallenge::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->deleteJson("/api/v1/user-challenges/{$userChallenge->id}");
            
            $response->assertStatus(404);
        });
    });
});
