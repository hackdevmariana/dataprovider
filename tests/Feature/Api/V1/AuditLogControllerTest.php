<?php

use App\Models\User;
use App\Models\AuditLog;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('AuditLogController', function () {
    
    describe('GET /api/v1/audit-logs', function () {
        test('returns paginated list of audit logs', function () {
            AuditLog::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/audit-logs');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'action',
                            'model_type',
                            'model_id',
                            'old_values',
                            'new_values',
                            'ip_address',
                            'user_agent',
                            'created_at'
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
        
        test('filters audit logs by user_id', function () {
            $otherUser = User::factory()->create();
            AuditLog::factory()->create(['user_id' => $otherUser->id]);
            AuditLog::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/audit-logs?user_id={$this->user->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
        
        test('filters audit logs by action', function () {
            AuditLog::factory()->create(['action' => 'create']);
            AuditLog::factory()->create(['action' => 'update']);
            
            $response = $this->getJson('/api/v1/audit-logs?action=create');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.action', 'create');
        });
        
        test('filters audit logs by model_type', function () {
            AuditLog::factory()->create(['model_type' => 'User']);
            AuditLog::factory()->create(['model_type' => 'Organization']);
            
            $response = $this->getJson('/api/v1/audit-logs?model_type=User');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.model_type', 'User');
        });
        
        test('filters audit logs by date range', function () {
            AuditLog::factory()->create(['created_at' => '2024-01-01 10:00:00']);
            AuditLog::factory()->create(['created_at' => '2024-03-01 10:00:00']);
            
            $response = $this->getJson('/api/v1/audit-logs?date_from=2024-01-01&date_to=2024-02-29');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('filters audit logs by ip_address', function () {
            AuditLog::factory()->create(['ip_address' => '192.168.1.1']);
            AuditLog::factory()->create(['ip_address' => '192.168.1.2']);
            
            $response = $this->getJson('/api/v1/audit-logs?ip_address=192.168.1.1');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.ip_address', '192.168.1.1');
        });
        
        test('respects per_page parameter', function () {
            AuditLog::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/audit-logs?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/audit-logs', function () {
        test('creates new audit log with valid data', function () {
            $auditLogData = [
                'action' => 'create',
                'model_type' => 'User',
                'model_id' => 1,
                'old_values' => null,
                'new_values' => ['name' => 'John Doe', 'email' => 'john@example.com'],
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ];
            
            $response = $this->postJson('/api/v1/audit-logs', $auditLogData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.action', 'create')
                ->assertJsonPath('data.model_type', 'User')
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('audit_logs', [
                'action' => 'create',
                'model_type' => 'User',
                'user_id' => $this->user->id
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/audit-logs', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['action', 'model_type']);
        });
        
        test('returns 422 with invalid action', function () {
            $auditLogData = [
                'action' => 'invalid_action',
                'model_type' => 'User'
            ];
            
            $response = $this->postJson('/api/v1/audit-logs', $auditLogData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['action']);
        });
        
        test('returns 422 with invalid model_type', function () {
            $auditLogData = [
                'action' => 'create',
                'model_type' => ''
            ];
            
            $response = $this->postJson('/api/v1/audit-logs', $auditLogData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['model_type']);
        });
        
        test('returns 422 with invalid old_values format', function () {
            $auditLogData = [
                'action' => 'update',
                'model_type' => 'User',
                'old_values' => 'invalid-json'
            ];
            
            $response = $this->postJson('/api/v1/audit-logs', $auditLogData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['old_values']);
        });
        
        test('returns 422 with invalid new_values format', function () {
            $auditLogData = [
                'action' => 'create',
                'model_type' => 'User',
                'new_values' => 'invalid-json'
            ];
            
            $response = $this->postJson('/api/v1/audit-logs', $auditLogData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['new_values']);
        });
    });
    
    describe('GET /api/v1/audit-logs/{id}', function () {
        test('returns audit log details', function () {
            $auditLog = AuditLog::factory()->create();
            
            $response = $this->getJson("/api/v1/audit-logs/{$auditLog->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $auditLog->id)
                ->assertJsonPath('data.action', $auditLog->action)
                ->assertJsonPath('data.model_type', $auditLog->model_type);
        });
        
        test('returns 404 for non-existent audit log', function () {
            $response = $this->getJson('/api/v1/audit-logs/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/audit-logs/{id}', function () {
        test('updates audit log with valid data', function () {
            $auditLog = AuditLog::factory()->create();
            $updateData = [
                'new_values' => ['name' => 'John Doe Updated', 'email' => 'john.updated@example.com'],
                'ip_address' => '192.168.1.100'
            ];
            
            $response = $this->putJson("/api/v1/audit-logs/{$auditLog->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.ip_address', '192.168.1.100');
                
            $this->assertDatabaseHas('audit_logs', [
                'id' => $auditLog->id,
                'ip_address' => '192.168.1.100'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $auditLog = AuditLog::factory()->create();
            
            $response = $this->putJson("/api/v1/audit-logs/{$auditLog->id}", [
                'new_values' => 'invalid-json'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['new_values']);
        });
        
        test('returns 404 for non-existent audit log', function () {
            $response = $this->putJson('/api/v1/audit-logs/999', [
                'ip_address' => '192.168.1.100'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/audit-logs/{id}', function () {
        test('deletes audit log successfully', function () {
            $auditLog = AuditLog::factory()->create();
            
            $response = $this->deleteJson("/api/v1/audit-logs/{$auditLog->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('audit_logs', ['id' => $auditLog->id]);
        });
        
        test('returns 404 for non-existent audit log', function () {
            $response = $this->deleteJson('/api/v1/audit-logs/999');
            
            $response->assertStatus(404);
        });
    });
});

