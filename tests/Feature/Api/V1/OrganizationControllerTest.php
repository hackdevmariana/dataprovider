<?php

use App\Models\User;
use App\Models\Organization;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('OrganizationController', function () {
    
    describe('GET /api/v1/organizations', function () {
        test('returns paginated list of organizations', function () {
            Organization::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/organizations');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'type',
                            'email',
                            'phone',
                            'website',
                            'status'
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
        
        test('filters organizations by type', function () {
            Organization::factory()->create(['type' => 'company']);
            Organization::factory()->create(['type' => 'nonprofit']);
            
            $response = $this->getJson('/api/v1/organizations?type=company');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.type', 'company');
        });
        
        test('filters organizations by status', function () {
            Organization::factory()->create(['status' => 'active']);
            Organization::factory()->create(['status' => 'inactive']);
            
            $response = $this->getJson('/api/v1/organizations?status=active');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'active');
        });
        
        test('searches organizations by name or description', function () {
            Organization::factory()->create(['name' => 'Tech Corp']);
            Organization::factory()->create(['name' => 'Green NGO']);
            
            $response = $this->getJson('/api/v1/organizations?search=tech');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Tech Corp');
        });
        
        test('respects per_page parameter', function () {
            Organization::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/organizations?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/organizations', function () {
        test('creates new organization with valid data', function () {
            $organizationData = [
                'name' => 'Tech Solutions Inc',
                'slug' => 'tech-solutions-inc',
                'type' => 'company',
                'email' => 'contact@techsolutions.com',
                'phone' => '+1234567890',
                'website' => 'https://techsolutions.com',
                'status' => 'active'
            ];
            
            $response = $this->postJson('/api/v1/organizations', $organizationData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Tech Solutions Inc')
                ->assertJsonPath('data.type', 'company')
                ->assertJsonPath('data.email', 'contact@techsolutions.com');
                
            $this->assertDatabaseHas('organizations', [
                'name' => 'Tech Solutions Inc',
                'slug' => 'tech-solutions-inc',
                'type' => 'company'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/organizations', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'type']);
        });
        
        test('returns 422 with invalid type', function () {
            $organizationData = [
                'name' => 'Test Org',
                'slug' => 'test-org',
                'type' => 'invalid_type'
            ];
            
            $response = $this->postJson('/api/v1/organizations', $organizationData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 422 with invalid email format', function () {
            $organizationData = [
                'name' => 'Test Org',
                'slug' => 'test-org',
                'type' => 'company',
                'email' => 'invalid-email'
            ];
            
            $response = $this->postJson('/api/v1/organizations', $organizationData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
        
        test('returns 422 with invalid website format', function () {
            $organizationData = [
                'name' => 'Test Org',
                'slug' => 'test-org',
                'type' => 'company',
                'website' => 'invalid-website'
            ];
            
            $response = $this->postJson('/api/v1/organizations', $organizationData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['website']);
        });
    });
    
    describe('GET /api/v1/organizations/{id}', function () {
        test('returns organization details', function () {
            $organization = Organization::factory()->create();
            
            $response = $this->getJson("/api/v1/organizations/{$organization->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $organization->id)
                ->assertJsonPath('data.name', $organization->name)
                ->assertJsonPath('data.type', $organization->type);
        });
        
        test('returns 404 for non-existent organization', function () {
            $response = $this->getJson('/api/v1/organizations/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/organizations/{id}', function () {
        test('updates organization with valid data', function () {
            $organization = Organization::factory()->create();
            $updateData = [
                'name' => 'Updated Tech Solutions',
                'status' => 'inactive'
            ];
            
            $response = $this->putJson("/api/v1/organizations/{$organization->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Updated Tech Solutions')
                ->assertJsonPath('data.status', 'inactive');
                
            $this->assertDatabaseHas('organizations', [
                'id' => $organization->id,
                'name' => 'Updated Tech Solutions',
                'status' => 'inactive'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $organization = Organization::factory()->create();
            
            $response = $this->putJson("/api/v1/organizations/{$organization->id}", [
                'type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 404 for non-existent organization', function () {
            $response = $this->putJson('/api/v1/organizations/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/organizations/{id}', function () {
        test('deletes organization successfully', function () {
            $organization = Organization::factory()->create();
            
            $response = $this->deleteJson("/api/v1/organizations/{$organization->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('organizations', ['id' => $organization->id]);
        });
        
        test('returns 404 for non-existent organization', function () {
            $response = $this->deleteJson('/api/v1/organizations/999');
            
            $response->assertStatus(404);
        });
    });
});
