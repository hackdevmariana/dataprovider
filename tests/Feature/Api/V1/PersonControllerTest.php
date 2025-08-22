<?php

use App\Models\User;
use App\Models\Person;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('PersonController', function () {
    
    describe('GET /api/v1/persons', function () {
        test('returns paginated list of persons', function () {
            Person::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/persons');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'first_name',
                            'last_name',
                            'email',
                            'phone',
                            'date_of_birth',
                            'gender',
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
        
        test('filters persons by gender', function () {
            Person::factory()->create(['gender' => 'male']);
            Person::factory()->create(['gender' => 'female']);
            
            $response = $this->getJson('/api/v1/persons?gender=male');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.gender', 'male');
        });
        
        test('filters persons by is_active', function () {
            Person::factory()->create(['is_active' => true]);
            Person::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/persons?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('filters persons by age range', function () {
            Person::factory()->create(['date_of_birth' => '1990-01-01']);
            Person::factory()->create(['date_of_birth' => '2000-01-01']);
            
            $response = $this->getJson('/api/v1/persons?age_min=25&age_max=35');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('searches persons by name or email', function () {
            Person::factory()->create(['first_name' => 'Juan', 'email' => 'juan@example.com']);
            Person::factory()->create(['first_name' => 'María', 'email' => 'maria@example.com']);
            
            $response = $this->getJson('/api/v1/persons?search=juan');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.first_name', 'Juan');
        });
        
        test('respects per_page parameter', function () {
            Person::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/persons?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/persons', function () {
        test('creates new person with valid data', function () {
            $personData = [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan.perez@example.com',
                'phone' => '+34612345678',
                'date_of_birth' => '1990-01-01',
                'gender' => 'male',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/persons', $personData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.first_name', 'Juan')
                ->assertJsonPath('data.last_name', 'Pérez')
                ->assertJsonPath('data.email', 'juan.perez@example.com');
                
            $this->assertDatabaseHas('persons', [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan.perez@example.com'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/persons', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['first_name', 'last_name', 'email']);
        });
        
        test('returns 422 with invalid email format', function () {
            $personData = [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'invalid-email'
            ];
            
            $response = $this->postJson('/api/v1/persons', $personData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
        
        test('returns 422 with invalid gender', function () {
            $personData = [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan@example.com',
                'gender' => 'invalid_gender'
            ];
            
            $response = $this->postJson('/api/v1/persons', $personData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['gender']);
        });
        
        test('returns 422 with invalid date format', function () {
            $personData = [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan@example.com',
                'date_of_birth' => 'invalid-date'
            ];
            
            $response = $this->postJson('/api/v1/persons', $personData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['date_of_birth']);
        });
        
        test('returns 422 with duplicate email', function () {
            Person::factory()->create(['email' => 'juan@example.com']);
            
            $personData = [
                'first_name' => 'Another Juan',
                'last_name' => 'García',
                'email' => 'juan@example.com'
            ];
            
            $response = $this->postJson('/api/v1/persons', $personData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
    });
    
    describe('GET /api/v1/persons/{id}', function () {
        test('returns person details', function () {
            $person = Person::factory()->create();
            
            $response = $this->getJson("/api/v1/persons/{$person->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $person->id)
                ->assertJsonPath('data.first_name', $person->first_name)
                ->assertJsonPath('data.last_name', $person->last_name);
        });
        
        test('returns 404 for non-existent person', function () {
            $response = $this->getJson('/api/v1/persons/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/persons/{id}', function () {
        test('updates person with valid data', function () {
            $person = Person::factory()->create();
            $updateData = [
                'first_name' => 'Juan Carlos',
                'phone' => '+34687654321'
            ];
            
            $response = $this->putJson("/api/v1/persons/{$person->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.first_name', 'Juan Carlos')
                ->assertJsonPath('data.phone', '+34687654321');
                
            $this->assertDatabaseHas('persons', [
                'id' => $person->id,
                'first_name' => 'Juan Carlos',
                'phone' => '+34687654321'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $person = Person::factory()->create();
            
            $response = $this->putJson("/api/v1/persons/{$person->id}", [
                'email' => 'invalid-email'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
        
        test('returns 404 for non-existent person', function () {
            $response = $this->putJson('/api/v1/persons/999', [
                'first_name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/persons/{id}', function () {
        test('deletes person successfully', function () {
            $person = Person::factory()->create();
            
            $response = $this->deleteJson("/api/v1/persons/{$person->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('persons', ['id' => $person->id]);
        });
        
        test('returns 404 for non-existent person', function () {
            $response = $this->deleteJson('/api/v1/persons/999');
            
            $response->assertStatus(404);
        });
    });
});
