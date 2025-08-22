<?php

use App\Models\User;
use App\Models\Profession;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('ProfessionController', function () {
    
    describe('GET /api/v1/professions', function () {
        test('returns paginated list of professions', function () {
            Profession::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/professions');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'category',
                            'is_public_facing',
                            'is_active'
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
        
        test('filters professions by category', function () {
            Profession::factory()->create(['category' => 'technology']);
            Profession::factory()->create(['category' => 'healthcare']);
            
            $response = $this->getJson('/api/v1/professions?category=technology');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.category', 'technology');
        });
        
        test('filters professions by is_public_facing', function () {
            Profession::factory()->create(['is_public_facing' => true]);
            Profession::factory()->create(['is_public_facing' => false]);
            
            $response = $this->getJson('/api/v1/professions?is_public_facing=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_public_facing', true);
        });
        
        test('filters professions by is_active', function () {
            Profession::factory()->create(['is_active' => true]);
            Profession::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/professions?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches professions by name or description', function () {
            Profession::factory()->create(['name' => 'Ingeniero de Software']);
            Profession::factory()->create(['name' => 'Médico']);
            
            $response = $this->getJson('/api/v1/professions?search=ingeniero');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Ingeniero de Software');
        });
        
        test('respects per_page parameter', function () {
            Profession::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/professions?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/professions', function () {
        test('creates new profession with valid data', function () {
            $professionData = [
                'name' => 'Ingeniero de Software',
                'slug' => 'ingeniero-de-software',
                'description' => 'Desarrollador de aplicaciones y sistemas',
                'category' => 'technology',
                'is_public_facing' => true,
                'is_active' => true,
                'requirements' => ['Grado en Ingeniería Informática'],
                'skills' => ['Programación', 'Diseño de Software']
            ];
            
            $response = $this->postJson('/api/v1/professions', $professionData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Ingeniero de Software')
                ->assertJsonPath('data.category', 'technology')
                ->assertJsonPath('data.is_public_facing', true);
                
            $this->assertDatabaseHas('professions', [
                'name' => 'Ingeniero de Software',
                'slug' => 'ingeniero-de-software',
                'category' => 'technology'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/professions', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'is_public_facing']);
        });
        
        test('returns 422 with invalid category', function () {
            $professionData = [
                'name' => 'Test Profession',
                'slug' => 'test-profession',
                'is_public_facing' => true,
                'category' => 'invalid_category'
            ];
            
            $response = $this->postJson('/api/v1/professions', $professionData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 422 with duplicate slug', function () {
            Profession::factory()->create(['slug' => 'test-profession']);
            
            $professionData = [
                'name' => 'Another Profession',
                'slug' => 'test-profession',
                'is_public_facing' => true
            ];
            
            $response = $this->postJson('/api/v1/professions', $professionData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
        });
        
        test('returns 422 with invalid requirements format', function () {
            $professionData = [
                'name' => 'Test Profession',
                'slug' => 'test-profession',
                'is_public_facing' => true,
                'requirements' => 'invalid-json'
            ];
            
            $response = $this->postJson('/api/v1/professions', $professionData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['requirements']);
        });
    });
    
    describe('GET /api/v1/professions/{id}', function () {
        test('returns profession details', function () {
            $profession = Profession::factory()->create();
            
            $response = $this->getJson("/api/v1/professions/{$profession->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $profession->id)
                ->assertJsonPath('data.name', $profession->name)
                ->assertJsonPath('data.category', $profession->category);
        });
        
        test('returns 404 for non-existent profession', function () {
            $response = $this->getJson('/api/v1/professions/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/professions/{id}', function () {
        test('updates profession with valid data', function () {
            $profession = Profession::factory()->create();
            $updateData = [
                'name' => 'Ingeniero de Software Senior',
                'description' => 'Desarrollador senior de aplicaciones y sistemas',
                'skills' => ['Programación', 'Diseño de Software', 'Arquitectura']
            ];
            
            $response = $this->putJson("/api/v1/professions/{$profession->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Ingeniero de Software Senior')
                ->assertJsonPath('data.description', 'Desarrollador senior de aplicaciones y sistemas');
                
            $this->assertDatabaseHas('professions', [
                'id' => $profession->id,
                'name' => 'Ingeniero de Software Senior',
                'description' => 'Desarrollador senior de aplicaciones y sistemas'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $profession = Profession::factory()->create();
            
            $response = $this->putJson("/api/v1/professions/{$profession->id}", [
                'category' => 'invalid_category'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 404 for non-existent profession', function () {
            $response = $this->putJson('/api/v1/professions/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/professions/{id}', function () {
        test('deletes profession successfully', function () {
            $profession = Profession::factory()->create();
            
            $response = $this->deleteJson("/api/v1/professions/{$profession->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('professions', ['id' => $profession->id]);
        });
        
        test('returns 404 for non-existent profession', function () {
            $response = $this->deleteJson('/api/v1/professions/999');
            
            $response->assertStatus(404);
        });
    });
});
