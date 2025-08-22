<?php

use App\Models\User;
use App\Models\Venue;
use App\Models\VenueType;
use App\Models\Organization;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('VenueController', function () {
    
    describe('GET /api/v1/venues', function () {
        test('returns paginated list of venues', function () {
            Venue::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/venues');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'venue_type_id',
                            'organization_id',
                            'address',
                            'city',
                            'country',
                            'is_active',
                            'venue_type',
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
        
        test('filters venues by venue_type_id', function () {
            $venueType = VenueType::factory()->create();
            Venue::factory()->create(['venue_type_id' => $venueType->id]);
            Venue::factory()->create(['venue_type_id' => VenueType::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/venues?venue_type_id={$venueType->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.venue_type_id', $venueType->id);
        });
        
        test('filters venues by organization_id', function () {
            $organization = Organization::factory()->create();
            Venue::factory()->create(['organization_id' => $organization->id]);
            Venue::factory()->create(['organization_id' => Organization::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/venues?organization_id={$organization->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.organization_id', $organization->id);
        });
        
        test('filters venues by city', function () {
            Venue::factory()->create(['city' => 'Madrid']);
            Venue::factory()->create(['city' => 'Barcelona']);
            
            $response = $this->getJson('/api/v1/venues?city=Madrid');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.city', 'Madrid');
        });
        
        test('filters venues by country', function () {
            Venue::factory()->create(['country' => 'Spain']);
            Venue::factory()->create(['country' => 'France']);
            
            $response = $this->getJson('/api/v1/venues?country=Spain');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.country', 'Spain');
        });
        
        test('filters venues by is_active', function () {
            Venue::factory()->create(['is_active' => true]);
            Venue::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/venues?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches venues by name or description', function () {
            Venue::factory()->create(['name' => 'Teatro Principal']);
            Venue::factory()->create(['name' => 'Auditorio Municipal']);
            
            $response = $this->getJson('/api/v1/venues?search=teatro');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Teatro Principal');
        });
        
        test('respects per_page parameter', function () {
            Venue::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/venues?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/venues', function () {
        test('creates new venue with valid data', function () {
            $venueType = VenueType::factory()->create();
            $organization = Organization::factory()->create();
            $venueData = [
                'name' => 'Teatro Principal',
                'description' => 'Teatro histórico del centro de la ciudad',
                'venue_type_id' => $venueType->id,
                'organization_id' => $organization->id,
                'address' => 'Calle Mayor, 123',
                'city' => 'Madrid',
                'country' => 'Spain',
                'postal_code' => '28001',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/venues', $venueData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Teatro Principal')
                ->assertJsonPath('data.city', 'Madrid')
                ->assertJsonPath('data.venue_type_id', $venueType->id);
                
            $this->assertDatabaseHas('venues', [
                'name' => 'Teatro Principal',
                'city' => 'Madrid',
                'venue_type_id' => $venueType->id
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/venues', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'venue_type_id']);
        });
        
        test('returns 422 with invalid venue_type_id', function () {
            $venueData = [
                'name' => 'Test Venue',
                'venue_type_id' => 999
            ];
            
            $response = $this->postJson('/api/v1/venues', $venueData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['venue_type_id']);
        });
        
        test('returns 422 with invalid organization_id', function () {
            $venueType = VenueType::factory()->create();
            $venueData = [
                'name' => 'Test Venue',
                'venue_type_id' => $venueType->id,
                'organization_id' => 999
            ];
            
            $response = $this->postJson('/api/v1/venues', $venueData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['organization_id']);
        });
        
        test('returns 422 with duplicate name in same city', function () {
            $venueType = VenueType::factory()->create();
            Venue::factory()->create([
                'name' => 'Teatro Principal',
                'city' => 'Madrid'
            ]);
            
            $venueData = [
                'name' => 'Teatro Principal',
                'venue_type_id' => $venueType->id,
                'city' => 'Madrid'
            ];
            
            $response = $this->postJson('/api/v1/venues', $venueData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
    });
    
    describe('GET /api/v1/venues/{id}', function () {
        test('returns venue details', function () {
            $venue = Venue::factory()->create();
            
            $response = $this->getJson("/api/v1/venues/{$venue->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $venue->id)
                ->assertJsonPath('data.name', $venue->name)
                ->assertJsonPath('data.city', $venue->city);
        });
        
        test('returns 404 for non-existent venue', function () {
            $response = $this->getJson('/api/v1/venues/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/venues/{id}', function () {
        test('updates venue with valid data', function () {
            $venue = Venue::factory()->create();
            $updateData = [
                'name' => 'Teatro Principal Actualizado',
                'description' => 'Teatro histórico del centro de la ciudad (actualizado)',
                'is_active' => false
            ];
            
            $response = $this->putJson("/api/v1/venues/{$venue->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Teatro Principal Actualizado')
                ->assertJsonPath('data.is_active', false);
                
            $this->assertDatabaseHas('venues', [
                'id' => $venue->id,
                'name' => 'Teatro Principal Actualizado',
                'is_active' => false
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $venue = Venue::factory()->create();
            
            $response = $this->putJson("/api/v1/venues/{$venue->id}", [
                'venue_type_id' => 999
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['venue_type_id']);
        });
        
        test('returns 404 for non-existent venue', function () {
            $response = $this->putJson('/api/v1/venues/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/venues/{id}', function () {
        test('deletes venue successfully', function () {
            $venue = Venue::factory()->create();
            
            $response = $this->deleteJson("/api/v1/venues/{$venue->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('venues', ['id' => $venue->id]);
        });
        
        test('returns 404 for non-existent venue', function () {
            $response = $this->deleteJson('/api/v1/venues/999');
            
            $response->assertStatus(404);
        });
    });
});
