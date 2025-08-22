<?php

use App\Models\User;
use App\Models\Cooperative;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('CooperativeController', function () {
    
    describe('GET /api/v1/cooperatives', function () {
        test('returns paginated list of cooperatives', function () {
            Cooperative::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/cooperatives');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'type',
                            'status',
                            'founded_date',
                            'member_count'
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
        
        test('filters cooperatives by type', function () {
            Cooperative::factory()->create(['type' => 'energy']);
            Cooperative::factory()->create(['type' => 'agriculture']);
            
            $response = $this->getJson('/api/v1/cooperatives?type=energy');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.type', 'energy');
        });
        
        test('filters cooperatives by status', function () {
            Cooperative::factory()->create(['status' => 'active']);
            Cooperative::factory()->create(['status' => 'inactive']);
            
            $response = $this->getJson('/api/v1/cooperatives?status=active');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'active');
        });
        
        test('filters cooperatives by member count range', function () {
            Cooperative::factory()->create(['member_count' => 50]);
            Cooperative::factory()->create(['member_count' => 200]);
            
            $response = $this->getJson('/api/v1/cooperatives?member_count_min=100&member_count_max=300');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.member_count', 200);
        });
        
        test('searches cooperatives by name or description', function () {
            Cooperative::factory()->create(['name' => 'Coop Verde']);
            Cooperative::factory()->create(['name' => 'Coop Solar']);
            
            $response = $this->getJson('/api/v1/cooperatives?search=verde');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Coop Verde');
        });
        
        test('respects per_page parameter', function () {
            Cooperative::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/cooperatives?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/cooperatives', function () {
        test('creates new cooperative with valid data', function () {
            $cooperativeData = [
                'name' => 'Coop Verde',
                'slug' => 'coop-verde',
                'description' => 'Cooperativa de energía renovable',
                'type' => 'energy',
                'status' => 'active',
                'founded_date' => '2020-01-01',
                'mission' => 'Promover la sostenibilidad energética',
                'vision' => 'Ser líder en energía renovable'
            ];
            
            $response = $this->postJson('/api/v1/cooperatives', $cooperativeData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Coop Verde')
                ->assertJsonPath('data.type', 'energy')
                ->assertJsonPath('data.status', 'active');
                
            $this->assertDatabaseHas('cooperatives', [
                'name' => 'Coop Verde',
                'slug' => 'coop-verde',
                'type' => 'energy'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/cooperatives', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'type']);
        });
        
        test('returns 422 with invalid type', function () {
            $cooperativeData = [
                'name' => 'Test Coop',
                'slug' => 'test-coop',
                'type' => 'invalid_type'
            ];
            
            $response = $this->postJson('/api/v1/cooperatives', $cooperativeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 422 with invalid status', function () {
            $cooperativeData = [
                'name' => 'Test Coop',
                'slug' => 'test-coop',
                'type' => 'energy',
                'status' => 'invalid_status'
            ];
            
            $response = $this->postJson('/api/v1/cooperatives', $cooperativeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 422 with duplicate slug', function () {
            Cooperative::factory()->create(['slug' => 'test-coop']);
            
            $cooperativeData = [
                'name' => 'Another Coop',
                'slug' => 'test-coop',
                'type' => 'energy'
            ];
            
            $response = $this->postJson('/api/v1/cooperatives', $cooperativeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
        });
        
        test('returns 422 with invalid founded_date format', function () {
            $cooperativeData = [
                'name' => 'Test Coop',
                'slug' => 'test-coop',
                'type' => 'energy',
                'founded_date' => 'invalid-date'
            ];
            
            $response = $this->postJson('/api/v1/cooperatives', $cooperativeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['founded_date']);
        });
    });
    
    describe('GET /api/v1/cooperatives/{id}', function () {
        test('returns cooperative details', function () {
            $cooperative = Cooperative::factory()->create();
            
            $response = $this->getJson("/api/v1/cooperatives/{$cooperative->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $cooperative->id)
                ->assertJsonPath('data.name', $cooperative->name)
                ->assertJsonPath('data.type', $cooperative->type);
        });
        
        test('returns 404 for non-existent cooperative', function () {
            $response = $this->getJson('/api/v1/cooperatives/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/cooperatives/{id}', function () {
        test('updates cooperative with valid data', function () {
            $cooperative = Cooperative::factory()->create();
            $updateData = [
                'name' => 'Coop Verde Actualizada',
                'description' => 'Cooperativa de energía renovable actualizada',
                'member_count' => 150
            ];
            
            $response = $this->putJson("/api/v1/cooperatives/{$cooperative->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Coop Verde Actualizada')
                ->assertJsonPath('data.description', 'Cooperativa de energía renovable actualizada');
                
            $this->assertDatabaseHas('cooperatives', [
                'id' => $cooperative->id,
                'name' => 'Coop Verde Actualizada',
                'description' => 'Cooperativa de energía renovable actualizada'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $cooperative = Cooperative::factory()->create();
            
            $response = $this->putJson("/api/v1/cooperatives/{$cooperative->id}", [
                'type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 404 for non-existent cooperative', function () {
            $response = $this->putJson('/api/v1/cooperatives/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/cooperatives/{id}', function () {
        test('deletes cooperative successfully', function () {
            $cooperative = Cooperative::factory()->create();
            
            $response = $this->deleteJson("/api/v1/cooperatives/{$cooperative->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('cooperatives', ['id' => $cooperative->id]);
        });
        
        test('returns 404 for non-existent cooperative', function () {
            $response = $this->deleteJson('/api/v1/cooperatives/999');
            
            $response->assertStatus(404);
        });
    });
});
