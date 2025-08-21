<?php

use App\Models\User;
use App\Models\SyncLog;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('SyncLogController', function () {
    
    describe('GET /api/v1/sync-logs', function () {
        test('returns paginated list of sync logs', function () {
            SyncLog::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/sync-logs');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'data_source_id',
                            'status',
                            'started_at',
                            'completed_at',
                            'records_processed',
                            'records_synced'
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
        
        test('filters sync logs by status', function () {
            SyncLog::factory()->create(['status' => 'success']);
            SyncLog::factory()->create(['status' => 'failed']);
            
            $response = $this->getJson('/api/v1/sync-logs?status=success');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'success');
        });
        
        test('filters sync logs by data source', function () {
            SyncLog::factory()->create(['data_source_id' => 1]);
            SyncLog::factory()->create(['data_source_id' => 2]);
            
            $response = $this->getJson('/api/v1/sync-logs?data_source_id=1');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.data_source_id', 1);
        });
        
        test('filters sync logs by date range', function () {
            SyncLog::factory()->create(['started_at' => '2024-01-01 10:00:00']);
            SyncLog::factory()->create(['started_at' => '2024-02-01 10:00:00']);
            
            $response = $this->getJson('/api/v1/sync-logs?date_from=2024-01-01&date_to=2024-01-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('respects per_page parameter', function () {
            SyncLog::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/sync-logs?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/sync-logs', function () {
        test('creates new sync log with valid data', function () {
            $syncLogData = [
                'data_source_id' => 1,
                'status' => 'running',
                'started_at' => now(),
                'description' => 'Sincronización de datos meteorológicos'
            ];
            
            $response = $this->postJson('/api/v1/sync-logs', $syncLogData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.data_source_id', 1)
                ->assertJsonPath('data.status', 'running');
                
            $this->assertDatabaseHas('sync_logs', [
                'data_source_id' => 1,
                'status' => 'running'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/sync-logs', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['data_source_id', 'status']);
        });
        
        test('returns 422 with invalid status', function () {
            $syncLogData = [
                'data_source_id' => 1,
                'status' => 'invalid_status'
            ];
            
            $response = $this->postJson('/api/v1/sync-logs', $syncLogData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 422 with invalid data source id', function () {
            $syncLogData = [
                'data_source_id' => 999,
                'status' => 'running'
            ];
            
            $response = $this->postJson('/api/v1/sync-logs', $syncLogData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['data_source_id']);
        });
    });
    
    describe('GET /api/v1/sync-logs/{id}', function () {
        test('returns sync log details', function () {
            $syncLog = SyncLog::factory()->create();
            
            $response = $this->getJson("/api/v1/sync-logs/{$syncLog->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $syncLog->id)
                ->assertJsonPath('data.data_source_id', $syncLog->data_source_id)
                ->assertJsonPath('data.status', $syncLog->status);
        });
        
        test('returns 404 for non-existent sync log', function () {
            $response = $this->getJson('/api/v1/sync-logs/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/sync-logs/{id}', function () {
        test('updates sync log with valid data', function () {
            $syncLog = SyncLog::factory()->create();
            $updateData = [
                'status' => 'completed',
                'completed_at' => now(),
                'records_processed' => 1000,
                'records_synced' => 950
            ];
            
            $response = $this->putJson("/api/v1/sync-logs/{$syncLog->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.status', 'completed')
                ->assertJsonPath('data.records_processed', 1000);
                
            $this->assertDatabaseHas('sync_logs', [
                'id' => $syncLog->id,
                'status' => 'completed',
                'records_processed' => 1000
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $syncLog = SyncLog::factory()->create();
            
            $response = $this->putJson("/api/v1/sync-logs/{$syncLog->id}", [
                'status' => 'invalid_status'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 404 for non-existent sync log', function () {
            $response = $this->putJson('/api/v1/sync-logs/999', [
                'status' => 'completed'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/sync-logs/{id}', function () {
        test('deletes sync log successfully', function () {
            $syncLog = SyncLog::factory()->create();
            
            $response = $this->deleteJson("/api/v1/sync-logs/{$syncLog->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('sync_logs', ['id' => $syncLog->id]);
        });
        
        test('returns 404 for non-existent sync log', function () {
            $response = $this->deleteJson('/api/v1/sync-logs/999');
            
            $response->assertStatus(404);
        });
    });
});
