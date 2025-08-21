<?php

use App\Models\User;
use App\Models\PersonProfession;
use App\Models\Person;
use App\Models\Profession;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('PersonProfessionController', function () {
    
    describe('GET /api/v1/person-professions', function () {
        test('returns paginated list of person professions', function () {
            PersonProfession::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/person-professions');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'person_id',
                            'profession_id',
                            'start_date',
                            'end_date',
                            'is_current',
                            'description',
                            'person',
                            'profession'
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
        
        test('filters by person_id', function () {
            $person = Person::factory()->create();
            PersonProfession::factory()->create(['person_id' => $person->id]);
            PersonProfession::factory()->create(['person_id' => Person::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/person-professions?person_id={$person->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.person_id', $person->id);
        });
        
        test('filters by profession_id', function () {
            $profession = Profession::factory()->create();
            PersonProfession::factory()->create(['profession_id' => $profession->id]);
            PersonProfession::factory()->create(['profession_id' => Profession::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/person-professions?profession_id={$profession->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.profession_id', $profession->id);
        });
        
        test('filters by is_current', function () {
            PersonProfession::factory()->create(['is_current' => true]);
            PersonProfession::factory()->create(['is_current' => false]);
            
            $response = $this->getJson('/api/v1/person-professions?is_current=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_current', true);
        });
        
        test('filters by date range', function () {
            PersonProfession::factory()->create(['start_date' => '2020-01-01']);
            PersonProfession::factory()->create(['start_date' => '2022-01-01']);
            
            $response = $this->getJson('/api/v1/person-professions?start_date_from=2020-01-01&start_date_to=2021-12-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('respects per_page parameter', function () {
            PersonProfession::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/person-professions?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/person-professions', function () {
        test('creates new person profession with valid data', function () {
            $person = Person::factory()->create();
            $profession = Profession::factory()->create();
            $personProfessionData = [
                'person_id' => $person->id,
                'profession_id' => $profession->id,
                'start_date' => '2020-01-01',
                'is_current' => true,
                'description' => 'Desarrollador de software senior'
            ];
            
            $response = $this->postJson('/api/v1/person-professions', $personProfessionData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.person_id', $person->id)
                ->assertJsonPath('data.profession_id', $profession->id)
                ->assertJsonPath('data.is_current', true);
                
            $this->assertDatabaseHas('person_professions', [
                'person_id' => $person->id,
                'profession_id' => $profession->id,
                'start_date' => '2020-01-01'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/person-professions', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['person_id', 'profession_id', 'start_date']);
        });
        
        test('returns 422 with invalid person_id', function () {
            $profession = Profession::factory()->create();
            $personProfessionData = [
                'person_id' => 999,
                'profession_id' => $profession->id,
                'start_date' => '2020-01-01'
            ];
            
            $response = $this->postJson('/api/v1/person-professions', $personProfessionData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['person_id']);
        });
        
        test('returns 422 with invalid profession_id', function () {
            $person = Person::factory()->create();
            $personProfessionData = [
                'person_id' => $person->id,
                'profession_id' => 999,
                'start_date' => '2020-01-01'
            ];
            
            $response = $this->postJson('/api/v1/person-professions', $personProfessionData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['profession_id']);
        });
        
        test('returns 422 with end_date before start_date', function () {
            $person = Person::factory()->create();
            $profession = Profession::factory()->create();
            $personProfessionData = [
                'person_id' => $person->id,
                'profession_id' => $profession->id,
                'start_date' => '2020-01-01',
                'end_date' => '2019-12-31'
            ];
            
            $response = $this->postJson('/api/v1/person-professions', $personProfessionData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['end_date']);
        });
        
        test('returns 422 with duplicate profession for same person', function () {
            $person = Person::factory()->create();
            $profession = Profession::factory()->create();
            PersonProfession::factory()->create([
                'person_id' => $person->id,
                'profession_id' => $profession->id
            ]);
            
            $personProfessionData = [
                'person_id' => $person->id,
                'profession_id' => $profession->id,
                'start_date' => '2022-01-01'
            ];
            
            $response = $this->postJson('/api/v1/person-professions', $personProfessionData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['profession_id']);
        });
    });
    
    describe('GET /api/v1/person-professions/{id}', function () {
        test('returns person profession details', function () {
            $personProfession = PersonProfession::factory()->create();
            
            $response = $this->getJson("/api/v1/person-professions/{$personProfession->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $personProfession->id)
                ->assertJsonPath('data.person_id', $personProfession->person_id)
                ->assertJsonPath('data.profession_id', $personProfession->profession_id);
        });
        
        test('returns 404 for non-existent person profession', function () {
            $response = $this->getJson('/api/v1/person-professions/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/person-professions/{id}', function () {
        test('updates person profession with valid data', function () {
            $personProfession = PersonProfession::factory()->create();
            $updateData = [
                'is_current' => false,
                'end_date' => '2023-12-31',
                'description' => 'Desarrollador de software senior (actualizado)'
            ];
            
            $response = $this->putJson("/api/v1/person-professions/{$personProfession->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.is_current', false)
                ->assertJsonPath('data.end_date', '2023-12-31');
                
            $this->assertDatabaseHas('person_professions', [
                'id' => $personProfession->id,
                'is_current' => false,
                'end_date' => '2023-12-31'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $personProfession = PersonProfession::factory()->create();
            
            $response = $this->putJson("/api/v1/person-professions/{$personProfession->id}", [
                'end_date' => '2019-12-31'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['end_date']);
        });
        
        test('returns 404 for non-existent person profession', function () {
            $response = $this->putJson('/api/v1/person-professions/999', [
                'is_current' => false
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/person-professions/{id}', function () {
        test('deletes person profession successfully', function () {
            $personProfession = PersonProfession::factory()->create();
            
            $response = $this->deleteJson("/api/v1/person-professions/{$personProfession->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('person_professions', ['id' => $personProfession->id]);
        });
        
        test('returns 404 for non-existent person profession', function () {
            $response = $this->deleteJson('/api/v1/person-professions/999');
            
            $response->assertStatus(404);
        });
    });
});
