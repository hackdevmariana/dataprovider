<?php

use App\Models\User;
use App\Models\DataSource;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('DataSourceController', function () {
    
    describe('GET /api/v1/data-sources', function () {
        test('returns paginated list of data sources', function () {
            DataSource::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/data-sources');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'type',
                            'url',
                            'status',
                            'last_sync_at'
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
        
        test('filters data sources by type', function () {
            DataSource::factory()->create(['type' => 'api']);
            DataSource::factory()->create(['type' => 'file']);
            
            $response = $this->getJson('/api/v1/data-sources?type=api');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.type', 'api');
        });
        
        test('filters data sources by status', function () {
            DataSource::factory()->create(['status' => 'active']);
            DataSource::factory()->create(['status' => 'inactive']);
            
            $response = $this->getJson('/api/v1/data-sources?status=active');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'active');
        });
        
        test('searches data sources by name or description', function () {
            DataSource::factory()->create(['name' => 'Weather API']);
            DataSource::factory()->create(['name' => 'Stock Data']);
            
            $response = $this->getJson('/api/v1/data-sources?search=weather');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Weather API');
        });
        
        test('respects per_page parameter', function () {
            DataSource::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/data-sources?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/data-sources', function () {
        test('creates new data source with valid data', function () {
            $dataSourceData = [
                'name' => 'Weather API',
                'slug' => 'weather-api',
                'type' => 'api',
                'url' => 'https://api.weather.com',
                'status' => 'active',
                'description' => 'API para datos meteorológicos'
            ];
            
            $response = $this->postJson('/api/v1/data-sources', $dataSourceData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Weather API')
                ->assertJsonPath('data.type', 'api')
                ->assertJsonPath('data.url', 'https://api.weather.com');
                
            $this->assertDatabaseHas('data_sources', [
                'name' => 'Weather API',
                'slug' => 'weather-api',
                'type' => 'api'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/data-sources', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'type']);
        });
        
        test('returns 422 with invalid type', function () {
            $dataSourceData = [
                'name' => 'Test Source',
                'slug' => 'test-source',
                'type' => 'invalid_type'
            ];
            
            $response = $this->postJson('/api/v1/data-sources', $dataSourceData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 422 with invalid URL format', function () {
            $dataSourceData = [
                'name' => 'Test Source',
                'slug' => 'test-source',
                'type' => 'api',
                'url' => 'invalid-url'
            ];
            
            $response = $this->postJson('/api/v1/data-sources', $dataSourceData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['url']);
        });
    });
    
    describe('GET /api/v1/data-sources/{id}', function () {
        test('returns data source details', function () {
            $dataSource = DataSource::factory()->create();
            
            $response = $this->getJson("/api/v1/data-sources/{$dataSource->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $dataSource->id)
                ->assertJsonPath('data.name', $dataSource->name)
                ->assertJsonPath('data.type', $dataSource->type);
        });
        
        test('returns 404 for non-existent data source', function () {
            $response = $this->getJson('/api/v1/data-sources/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/data-sources/{id}', function () {
        test('updates data source with valid data', function () {
            $dataSource = DataSource::factory()->create();
            $updateData = [
                'name' => 'Updated Weather API',
                'status' => 'inactive'
            ];
            
            $response = $this->putJson("/api/v1/data-sources/{$dataSource->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Updated Weather API')
                ->assertJsonPath('data.status', 'inactive');
                
            $this->assertDatabaseHas('data_sources', [
                'id' => $dataSource->id,
                'name' => 'Updated Weather API',
                'status' => 'inactive'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $dataSource = DataSource::factory()->create();
            
            $response = $this->putJson("/api/v1/data-sources/{$dataSource->id}", [
                'type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 404 for non-existent data source', function () {
            $response = $this->putJson('/api/v1/data-sources/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/data-sources/{id}', function () {
        test('deletes data source successfully', function () {
            $dataSource = DataSource::factory()->create();
            
            $response = $this->deleteJson("/api/v1/data-sources/{$dataSource->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('data_sources', ['id' => $dataSource->id]);
        });
        
        test('returns 404 for non-existent data source', function () {
            $response = $this->deleteJson('/api/v1/data-sources/999');
            
            $response->assertStatus(404);
        });
    });
});
