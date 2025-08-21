<?php

use App\Models\User;
use App\Models\CompanyType;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('CompanyTypeController', function () {
    
    describe('GET /api/v1/company-types', function () {
        test('returns paginated list of company types', function () {
            CompanyType::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/company-types');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'category',
                            'description',
                            'is_active',
                            'icon'
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
        
        test('filters company types by category', function () {
            CompanyType::factory()->create(['category' => 'technology']);
            CompanyType::factory()->create(['category' => 'finance']);
            
            $response = $this->getJson('/api/v1/company-types?category=technology');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.category', 'technology');
        });
        
        test('filters company types by is_active', function () {
            CompanyType::factory()->create(['is_active' => true]);
            CompanyType::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/company-types?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches company types by name or description', function () {
            CompanyType::factory()->create(['name' => 'Startup']);
            CompanyType::factory()->create(['name' => 'Corporación']);
            
            $response = $this->getJson('/api/v1/company-types?search=startup');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Startup');
        });
        
        test('respects per_page parameter', function () {
            CompanyType::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/company-types?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/company-types', function () {
        test('creates new company type with valid data', function () {
            $companyTypeData = [
                'name' => 'Startup',
                'slug' => 'startup',
                'category' => 'technology',
                'description' => 'Empresa tecnológica en fase inicial',
                'is_active' => true,
                'icon' => 'fa-rocket',
                'color' => '#FF5722'
            ];
            
            $response = $this->postJson('/api/v1/company-types', $companyTypeData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Startup')
                ->assertJsonPath('data.category', 'technology')
                ->assertJsonPath('data.icon', 'fa-rocket');
                
            $this->assertDatabaseHas('company_types', [
                'name' => 'Startup',
                'slug' => 'startup',
                'category' => 'technology'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/company-types', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'category']);
        });
        
        test('returns 422 with invalid category', function () {
            $companyTypeData = [
                'name' => 'Test Type',
                'slug' => 'test-type',
                'category' => 'invalid_category'
            ];
            
            $response = $this->postJson('/api/v1/company-types', $companyTypeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 422 with duplicate slug', function () {
            CompanyType::factory()->create(['slug' => 'test-type']);
            
            $companyTypeData = [
                'name' => 'Another Type',
                'slug' => 'test-type',
                'category' => 'technology'
            ];
            
            $response = $this->postJson('/api/v1/company-types', $companyTypeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
        });
        
        test('returns 422 with invalid color format', function () {
            $companyTypeData = [
                'name' => 'Test Type',
                'slug' => 'test-type',
                'category' => 'technology',
                'color' => 'invalid-color'
            ];
            
            $response = $this->postJson('/api/v1/company-types', $companyTypeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['color']);
        });
    });
    
    describe('GET /api/v1/company-types/{id}', function () {
        test('returns company type details', function () {
            $companyType = CompanyType::factory()->create();
            
            $response = $this->getJson("/api/v1/company-types/{$companyType->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $companyType->id)
                ->assertJsonPath('data.name', $companyType->name)
                ->assertJsonPath('data.category', $companyType->category);
        });
        
        test('returns 404 for non-existent company type', function () {
            $response = $this->getJson('/api/v1/company-types/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/company-types/{id}', function () {
        test('updates company type with valid data', function () {
            $companyType = CompanyType::factory()->create();
            $updateData = [
                'name' => 'Startup Tecnológica',
                'description' => 'Empresa tecnológica innovadora en fase inicial'
            ];
            
            $response = $this->putJson("/api/v1/company-types/{$companyType->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Startup Tecnológica')
                ->assertJsonPath('data.description', 'Empresa tecnológica innovadora en fase inicial');
                
            $this->assertDatabaseHas('company_types', [
                'id' => $companyType->id,
                'name' => 'Startup Tecnológica',
                'description' => 'Empresa tecnológica innovadora en fase inicial'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $companyType = CompanyType::factory()->create();
            
            $response = $this->putJson("/api/v1/company-types/{$companyType->id}", [
                'category' => 'invalid_category'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 404 for non-existent company type', function () {
            $response = $this->putJson('/api/v1/company-types/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/company-types/{id}', function () {
        test('deletes company type successfully', function () {
            $companyType = CompanyType::factory()->create();
            
            $response = $this->deleteJson("/api/v1/company-types/{$companyType->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('company_types', ['id' => $companyType->id]);
        });
        
        test('returns 404 for non-existent company type', function () {
            $response = $this->deleteJson('/api/v1/company-types/999');
            
            $response->assertStatus(404);
        });
    });
});
