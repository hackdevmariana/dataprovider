<?php

use App\Models\User;
use App\Models\PersonWork;
use App\Models\Person;
use App\Models\Organization;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('PersonWorkController', function () {
    
    describe('GET /api/v1/person-works', function () {
        test('returns paginated list of person works', function () {
            PersonWork::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/person-works');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'person_id',
                            'organization_id',
                            'position',
                            'start_date',
                            'end_date',
                            'is_current',
                            'description',
                            'person',
                            'organization'
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
            PersonWork::factory()->create(['person_id' => $person->id]);
            PersonWork::factory()->create(['person_id' => Person::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/person-works?person_id={$person->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.person_id', $person->id);
        });
        
        test('filters by organization_id', function () {
            $organization = Organization::factory()->create();
            PersonWork::factory()->create(['organization_id' => $organization->id]);
            PersonWork::factory()->create(['organization_id' => Organization::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/person-works?organization_id={$organization->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.organization_id', $organization->id);
        });
        
        test('filters by is_current', function () {
            PersonWork::factory()->create(['is_current' => true]);
            PersonWork::factory()->create(['is_current' => false]);
            
            $response = $this->getJson('/api/v1/person-works?is_current=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_current', true);
        });
        
        test('filters by position', function () {
            PersonWork::factory()->create(['position' => 'Developer']);
            PersonWork::factory()->create(['position' => 'Manager']);
            
            $response = $this->getJson('/api/v1/person-works?position=Developer');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.position', 'Developer');
        });
        
        test('filters by date range', function () {
            PersonWork::factory()->create(['start_date' => '2020-01-01']);
            PersonWork::factory()->create(['start_date' => '2022-01-01']);
            
            $response = $this->getJson('/api/v1/person-works?start_date_from=2020-01-01&start_date_to=2021-12-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('respects per_page parameter', function () {
            PersonWork::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/person-works?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/person-works', function () {
        test('creates new person work with valid data', function () {
            $person = Person::factory()->create();
            $organization = Organization::factory()->create();
            $personWorkData = [
                'person_id' => $person->id,
                'organization_id' => $organization->id,
                'position' => 'Software Developer',
                'start_date' => '2020-01-01',
                'is_current' => true,
                'description' => 'Desarrollo de aplicaciones web'
            ];
            
            $response = $this->postJson('/api/v1/person-works', $personWorkData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.person_id', $person->id)
                ->assertJsonPath('data.organization_id', $organization->id)
                ->assertJsonPath('data.position', 'Software Developer');
                
            $this->assertDatabaseHas('person_works', [
                'person_id' => $person->id,
                'organization_id' => $organization->id,
                'position' => 'Software Developer'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/person-works', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['person_id', 'organization_id', 'position', 'start_date']);
        });
        
        test('returns 422 with invalid person_id', function () {
            $organization = Organization::factory()->create();
            $personWorkData = [
                'person_id' => 999,
                'organization_id' => $organization->id,
                'position' => 'Developer',
                'start_date' => '2020-01-01'
            ];
            
            $response = $this->postJson('/api/v1/person-works', $personWorkData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['person_id']);
        });
        
        test('returns 422 with invalid organization_id', function () {
            $person = Person::factory()->create();
            $personWorkData = [
                'person_id' => $person->id,
                'organization_id' => 999,
                'position' => 'Developer',
                'start_date' => '2020-01-01'
            ];
            
            $response = $this->postJson('/api/v1/person-works', $personWorkData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['organization_id']);
        });
        
        test('returns 422 with end_date before start_date', function () {
            $person = Person::factory()->create();
            $organization = Organization::factory()->create();
            $personWorkData = [
                'person_id' => $person->id,
                'organization_id' => $organization->id,
                'position' => 'Developer',
                'start_date' => '2020-01-01',
                'end_date' => '2019-12-31'
            ];
            
            $response = $this->postJson('/api/v1/person-works', $personWorkData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['end_date']);
        });
        
        test('returns 422 with duplicate work for same person and organization', function () {
            $person = Person::factory()->create();
            $organization = Organization::factory()->create();
            PersonWork::factory()->create([
                'person_id' => $person->id,
                'organization_id' => $organization->id
            ]);
            
            $personWorkData = [
                'person_id' => $person->id,
                'organization_id' => $organization->id,
                'position' => 'Senior Developer',
                'start_date' => '2022-01-01'
            ];
            
            $response = $this->postJson('/api/v1/person-works', $personWorkData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['organization_id']);
        });
    });
    
    describe('GET /api/v1/person-works/{id}', function () {
        test('returns person work details', function () {
            $personWork = PersonWork::factory()->create();
            
            $response = $this->getJson("/api/v1/person-works/{$personWork->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $personWork->id)
                ->assertJsonPath('data.person_id', $personWork->person_id)
                ->assertJsonPath('data.organization_id', $personWork->organization_id);
        });
        
        test('returns 404 for non-existent person work', function () {
            $response = $this->getJson('/api/v1/person-works/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/person-works/{id}', function () {
        test('updates person work with valid data', function () {
            $personWork = PersonWork::factory()->create();
            $updateData = [
                'position' => 'Senior Software Developer',
                'is_current' => false,
                'end_date' => '2023-12-31',
                'description' => 'Desarrollo de aplicaciones web (actualizado)'
            ];
            
            $response = $this->putJson("/api/v1/person-works/{$personWork->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.position', 'Senior Software Developer')
                ->assertJsonPath('data.is_current', false);
                
            $this->assertDatabaseHas('person_works', [
                'id' => $personWork->id,
                'position' => 'Senior Software Developer',
                'is_current' => false
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $personWork = PersonWork::factory()->create();
            
            $response = $this->putJson("/api/v1/person-works/{$personWork->id}", [
                'end_date' => '2019-12-31'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['end_date']);
        });
        
        test('returns 404 for non-existent person work', function () {
            $response = $this->putJson('/api/v1/person-works/999', [
                'position' => 'Updated Position'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/person-works/{id}', function () {
        test('deletes person work successfully', function () {
            $personWork = PersonWork::factory()->create();
            
            $response = $this->deleteJson("/api/v1/person-works/{$personWork->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('person_works', ['id' => $personWork->id]);
        });
        
        test('returns 404 for non-existent person work', function () {
            $response = $this->deleteJson('/api/v1/person-works/999');
            
            $response->assertStatus(404);
        });
    });
});
