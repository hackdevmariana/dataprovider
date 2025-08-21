<?php

use App\Models\User;
use App\Models\OrganizationFeature;
use App\Models\Organization;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('OrganizationFeatureController', function () {
    
    describe('GET /api/v1/organization-features', function () {
        test('returns paginated list of organization features', function () {
            OrganizationFeature::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/organization-features');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'organization_id',
                            'feature_name',
                            'feature_value',
                            'is_enabled',
                            'metadata'
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
        
        test('filters features by organization_id', function () {
            $organization = Organization::factory()->create();
            OrganizationFeature::factory()->create(['organization_id' => $organization->id]);
            OrganizationFeature::factory()->create(['organization_id' => Organization::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/organization-features?organization_id={$organization->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.organization_id', $organization->id);
        });
        
        test('filters features by feature_name', function () {
            OrganizationFeature::factory()->create(['feature_name' => 'sustainability_tracking']);
            OrganizationFeature::factory()->create(['feature_name' => 'carbon_calculator']);
            
            $response = $this->getJson('/api/v1/organization-features?feature_name=sustainability_tracking');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.feature_name', 'sustainability_tracking');
        });
        
        test('filters features by is_enabled', function () {
            OrganizationFeature::factory()->create(['is_enabled' => true]);
            OrganizationFeature::factory()->create(['is_enabled' => false]);
            
            $response = $this->getJson('/api/v1/organization-features?is_enabled=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_enabled', true);
        });
        
        test('searches features by feature_name', function () {
            OrganizationFeature::factory()->create(['feature_name' => 'sustainability_tracking']);
            OrganizationFeature::factory()->create(['feature_name' => 'carbon_calculator']);
            
            $response = $this->getJson('/api/v1/organization-features?search=sustainability');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.feature_name', 'sustainability_tracking');
        });
        
        test('respects per_page parameter', function () {
            OrganizationFeature::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/organization-features?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/organization-features', function () {
        test('creates new organization feature with valid data', function () {
            $organization = Organization::factory()->create();
            $featureData = [
                'organization_id' => $organization->id,
                'feature_name' => 'sustainability_tracking',
                'feature_value' => 'enabled',
                'is_enabled' => true,
                'metadata' => ['tracking_level' => 'advanced']
            ];
            
            $response = $this->postJson('/api/v1/organization-features', $featureData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.organization_id', $organization->id)
                ->assertJsonPath('data.feature_name', 'sustainability_tracking')
                ->assertJsonPath('data.is_enabled', true);
                
            $this->assertDatabaseHas('organization_features', [
                'organization_id' => $organization->id,
                'feature_name' => 'sustainability_tracking',
                'is_enabled' => true
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/organization-features', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['organization_id', 'feature_name']);
        });
        
        test('returns 422 with invalid organization_id', function () {
            $featureData = [
                'organization_id' => 999,
                'feature_name' => 'test_feature'
            ];
            
            $response = $this->postJson('/api/v1/organization-features', $featureData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['organization_id']);
        });
        
        test('returns 422 with duplicate feature for same organization', function () {
            $organization = Organization::factory()->create();
            OrganizationFeature::factory()->create([
                'organization_id' => $organization->id,
                'feature_name' => 'sustainability_tracking'
            ]);
            
            $featureData = [
                'organization_id' => $organization->id,
                'feature_name' => 'sustainability_tracking',
                'feature_value' => 'disabled'
            ];
            
            $response = $this->postJson('/api/v1/organization-features', $featureData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['feature_name']);
        });
        
        test('returns 422 with invalid metadata format', function () {
            $organization = Organization::factory()->create();
            $featureData = [
                'organization_id' => $organization->id,
                'feature_name' => 'test_feature',
                'metadata' => 'invalid-json'
            ];
            
            $response = $this->postJson('/api/v1/organization-features', $featureData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['metadata']);
        });
    });
    
    describe('GET /api/v1/organization-features/{id}', function () {
        test('returns organization feature details', function () {
            $feature = OrganizationFeature::factory()->create();
            
            $response = $this->getJson("/api/v1/organization-features/{$feature->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $feature->id)
                ->assertJsonPath('data.organization_id', $feature->organization_id)
                ->assertJsonPath('data.feature_name', $feature->feature_name);
        });
        
        test('returns 404 for non-existent feature', function () {
            $response = $this->getJson('/api/v1/organization-features/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/organization-features/{id}', function () {
        test('updates organization feature with valid data', function () {
            $feature = OrganizationFeature::factory()->create();
            $updateData = [
                'feature_value' => 'disabled',
                'is_enabled' => false,
                'metadata' => ['tracking_level' => 'basic']
            ];
            
            $response = $this->putJson("/api/v1/organization-features/{$feature->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.feature_value', 'disabled')
                ->assertJsonPath('data.is_enabled', false);
                
            $this->assertDatabaseHas('organization_features', [
                'id' => $feature->id,
                'feature_value' => 'disabled',
                'is_enabled' => false
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $feature = OrganizationFeature::factory()->create();
            
            $response = $this->putJson("/api/v1/organization-features/{$feature->id}", [
                'metadata' => 'invalid-json'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['metadata']);
        });
        
        test('returns 404 for non-existent feature', function () {
            $response = $this->putJson('/api/v1/organization-features/999', [
                'feature_value' => 'updated'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/organization-features/{id}', function () {
        test('deletes organization feature successfully', function () {
            $feature = OrganizationFeature::factory()->create();
            
            $response = $this->deleteJson("/api/v1/organization-features/{$feature->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('organization_features', ['id' => $feature->id]);
        });
        
        test('returns 404 for non-existent feature', function () {
            $response = $this->deleteJson('/api/v1/organization-features/999');
            
            $response->assertStatus(404);
        });
    });
});
