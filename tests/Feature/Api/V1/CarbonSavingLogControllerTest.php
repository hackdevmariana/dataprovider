<?php

use App\Models\User;
use App\Models\CarbonSavingLog;
use App\Models\Challenge;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('CarbonSavingLogController', function () {
    
    describe('GET /api/v1/carbon-saving-logs', function () {
        test('returns paginated list of carbon saving logs', function () {
            CarbonSavingLog::factory()->count(3)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/carbon-saving-logs');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'challenge_id',
                            'activity_type',
                            'carbon_saved',
                            'activity_date',
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
        
        test('filters logs by challenge_id', function () {
            $challenge = Challenge::factory()->create();
            CarbonSavingLog::factory()->create([
                'user_id' => $this->user->id,
                'challenge_id' => $challenge->id
            ]);
            CarbonSavingLog::factory()->create([
                'user_id' => $this->user->id,
                'challenge_id' => Challenge::factory()->create()->id
            ]);
            
            $response = $this->getJson("/api/v1/carbon-saving-logs?challenge_id={$challenge->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.challenge_id', $challenge->id);
        });
        
        test('filters logs by activity_type', function () {
            CarbonSavingLog::factory()->create([
                'user_id' => $this->user->id,
                'activity_type' => 'transport'
            ]);
            CarbonSavingLog::factory()->create([
                'user_id' => $this->user->id,
                'activity_type' => 'energy'
            ]);
            
            $response = $this->getJson('/api/v1/carbon-saving-logs?activity_type=transport');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.activity_type', 'transport');
        });
        
        test('filters logs by date range', function () {
            CarbonSavingLog::factory()->create([
                'user_id' => $this->user->id,
                'activity_date' => '2024-01-01'
            ]);
            CarbonSavingLog::factory()->create([
                'user_id' => $this->user->id,
                'activity_date' => '2024-02-01'
            ]);
            
            $response = $this->getJson('/api/v1/carbon-saving-logs?date_from=2024-01-01&date_to=2024-01-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('only returns logs for authenticated user', function () {
            $otherUser = User::factory()->create();
            CarbonSavingLog::factory()->create(['user_id' => $otherUser->id]);
            CarbonSavingLog::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/carbon-saving-logs');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
        
        test('respects per_page parameter', function () {
            CarbonSavingLog::factory()->count(15)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/carbon-saving-logs?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/carbon-saving-logs', function () {
        test('creates new carbon saving log with valid data', function () {
            $challenge = Challenge::factory()->create();
            $logData = [
                'challenge_id' => $challenge->id,
                'activity_type' => 'transport',
                'carbon_saved' => 2.5,
                'activity_date' => '2024-01-01',
                'description' => 'Uso de transporte público en lugar de coche'
            ];
            
            $response = $this->postJson('/api/v1/carbon-saving-logs', $logData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.challenge_id', $challenge->id)
                ->assertJsonPath('data.activity_type', 'transport')
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('carbon_saving_logs', [
                'challenge_id' => $challenge->id,
                'user_id' => $this->user->id,
                'activity_type' => 'transport'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/carbon-saving-logs', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['challenge_id', 'activity_type', 'carbon_saved']);
        });
        
        test('returns 422 with invalid activity_type', function () {
            $challenge = Challenge::factory()->create();
            $logData = [
                'challenge_id' => $challenge->id,
                'activity_type' => 'invalid_type',
                'carbon_saved' => 2.5
            ];
            
            $response = $this->postJson('/api/v1/carbon-saving-logs', $logData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['activity_type']);
        });
        
        test('returns 422 with invalid challenge_id', function () {
            $logData = [
                'challenge_id' => 999,
                'activity_type' => 'transport',
                'carbon_saved' => 2.5
            ];
            
            $response = $this->postJson('/api/v1/carbon-saving-logs', $logData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['challenge_id']);
        });
        
        test('returns 422 with negative carbon_saved', function () {
            $challenge = Challenge::factory()->create();
            $logData = [
                'challenge_id' => $challenge->id,
                'activity_type' => 'transport',
                'carbon_saved' => -1.0
            ];
            
            $response = $this->postJson('/api/v1/carbon-saving-logs', $logData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['carbon_saved']);
        });
    });
    
    describe('GET /api/v1/carbon-saving-logs/{id}', function () {
        test('returns carbon saving log details', function () {
            $log = CarbonSavingLog::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/carbon-saving-logs/{$log->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $log->id)
                ->assertJsonPath('data.user_id', $this->user->id);
        });
        
        test('returns 404 for non-existent log', function () {
            $response = $this->getJson('/api/v1/carbon-saving-logs/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/carbon-saving-logs/{id}', function () {
        test('updates carbon saving log with valid data', function () {
            $log = CarbonSavingLog::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'carbon_saved' => 3.0,
                'description' => 'Uso de transporte público en lugar de coche personal'
            ];
            
            $response = $this->putJson("/api/v1/carbon-saving-logs/{$log->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.carbon_saved', 3.0)
                ->assertJsonPath('data.description', 'Uso de transporte público en lugar de coche personal');
                
            $this->assertDatabaseHas('carbon_saving_logs', [
                'id' => $log->id,
                'carbon_saved' => 3.0,
                'description' => 'Uso de transporte público en lugar de coche personal'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $log = CarbonSavingLog::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/carbon-saving-logs/{$log->id}", [
                'carbon_saved' => -1.0
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['carbon_saved']);
        });
        
        test('returns 404 for non-existent log', function () {
            $response = $this->putJson('/api/v1/carbon-saving-logs/999', [
                'carbon_saved' => 3.0
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/carbon-saving-logs/{id}', function () {
        test('deletes carbon saving log successfully', function () {
            $log = CarbonSavingLog::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/carbon-saving-logs/{$log->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('carbon_saving_logs', ['id' => $log->id]);
        });
        
        test('returns 404 for non-existent log', function () {
            $response = $this->deleteJson('/api/v1/carbon-saving-logs/999');
            
            $response->assertStatus(404);
        });
    });
});
