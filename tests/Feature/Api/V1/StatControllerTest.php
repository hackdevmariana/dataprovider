<?php

use App\Models\User;
use App\Models\Stat;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('StatController', function () {
    
    describe('GET /api/v1/stats', function () {
        test('returns paginated list of statistics', function () {
            Stat::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/stats');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'type',
                            'value',
                            'unit',
                            'period',
                            'date'
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
        
        test('filters statistics by type', function () {
            Stat::factory()->create(['type' => 'energy']);
            Stat::factory()->create(['type' => 'carbon']);
            
            $response = $this->getJson('/api/v1/stats?type=energy');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.type', 'energy');
        });
        
        test('filters statistics by period', function () {
            Stat::factory()->create(['period' => 'daily']);
            Stat::factory()->create(['period' => 'monthly']);
            
            $response = $this->getJson('/api/v1/stats?period=daily');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.period', 'daily');
        });
        
        test('filters statistics by date range', function () {
            Stat::factory()->create(['date' => '2024-01-01']);
            Stat::factory()->create(['date' => '2024-02-01']);
            
            $response = $this->getJson('/api/v1/stats?date_from=2024-01-01&date_to=2024-01-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.date', '2024-01-01');
        });
        
        test('respects per_page parameter', function () {
            Stat::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/stats?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/stats', function () {
        test('creates new statistic with valid data', function () {
            $statData = [
                'name' => 'Consumo Energético',
                'type' => 'energy',
                'value' => 1250.5,
                'unit' => 'kWh',
                'period' => 'monthly',
                'date' => '2024-01-01'
            ];
            
            $response = $this->postJson('/api/v1/stats', $statData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Consumo Energético')
                ->assertJsonPath('data.type', 'energy')
                ->assertJsonPath('data.value', 1250.5);
                
            $this->assertDatabaseHas('stats', [
                'name' => 'Consumo Energético',
                'type' => 'energy',
                'value' => 1250.5
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/stats', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'type', 'value', 'period']);
        });
        
        test('returns 422 with invalid type', function () {
            $statData = [
                'name' => 'Test Stat',
                'type' => 'invalid_type',
                'value' => 100,
                'period' => 'daily'
            ];
            
            $response = $this->postJson('/api/v1/stats', $statData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 422 with invalid period', function () {
            $statData = [
                'name' => 'Test Stat',
                'type' => 'energy',
                'value' => 100,
                'period' => 'invalid_period'
            ];
            
            $response = $this->postJson('/api/v1/stats', $statData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['period']);
        });
        
        test('returns 422 with invalid date format', function () {
            $statData = [
                'name' => 'Test Stat',
                'type' => 'energy',
                'value' => 100,
                'period' => 'daily',
                'date' => 'invalid-date'
            ];
            
            $response = $this->postJson('/api/v1/stats', $statData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['date']);
        });
    });
    
    describe('GET /api/v1/stats/{id}', function () {
        test('returns statistic details', function () {
            $stat = Stat::factory()->create();
            
            $response = $this->getJson("/api/v1/stats/{$stat->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $stat->id)
                ->assertJsonPath('data.name', $stat->name)
                ->assertJsonPath('data.type', $stat->type);
        });
        
        test('returns 404 for non-existent statistic', function () {
            $response = $this->getJson('/api/v1/stats/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/stats/{id}', function () {
        test('updates statistic with valid data', function () {
            $stat = Stat::factory()->create();
            $updateData = [
                'name' => 'Consumo Energético Actualizado',
                'value' => 1500.0
            ];
            
            $response = $this->putJson("/api/v1/stats/{$stat->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Consumo Energético Actualizado')
                ->assertJsonPath('data.value', 1500.0);
                
            $this->assertDatabaseHas('stats', [
                'id' => $stat->id,
                'name' => 'Consumo Energético Actualizado',
                'value' => 1500.0
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $stat = Stat::factory()->create();
            
            $response = $this->putJson("/api/v1/stats/{$stat->id}", [
                'type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 404 for non-existent statistic', function () {
            $response = $this->putJson('/api/v1/stats/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/stats/{id}', function () {
        test('deletes statistic successfully', function () {
            $stat = Stat::factory()->create();
            
            $response = $this->deleteJson("/api/v1/stats/{$stat->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('stats', ['id' => $stat->id]);
        });
        
        test('returns 404 for non-existent statistic', function () {
            $response = $this->deleteJson('/api/v1/stats/999');
            
            $response->assertStatus(404);
        });
    });
});
