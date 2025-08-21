<?php

use App\Models\User;
use App\Models\VenueType;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('VenueTypeController', function () {
    
    describe('GET /api/v1/venue-types', function () {
        test('returns paginated list of venue types', function () {
            VenueType::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/venue-types');
            
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
        
        test('filters venue types by category', function () {
            VenueType::factory()->create(['category' => 'entertainment']);
            VenueType::factory()->create(['category' => 'business']);
            
            $response = $this->getJson('/api/v1/venue-types?category=entertainment');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.category', 'entertainment');
        });
        
        test('filters venue types by is_active', function () {
            VenueType::factory()->create(['is_active' => true]);
            VenueType::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/venue-types?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches venue types by name or description', function () {
            VenueType::factory()->create(['name' => 'Teatro']);
            VenueType::factory()->create(['name' => 'Oficina']);
            
            $response = $this->getJson('/api/v1/venue-types?search=teatro');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Teatro');
        });
        
        test('respects per_page parameter', function () {
            VenueType::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/venue-types?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/venue-types', function () {
        test('creates new venue type with valid data', function () {
            $venueTypeData = [
                'name' => 'Teatro',
                'slug' => 'teatro',
                'category' => 'entertainment',
                'description' => 'Lugar para presentaciones teatrales',
                'is_active' => true,
                'icon' => 'fa-theater-masks',
                'color' => '#9C27B0'
            ];
            
            $response = $this->postJson('/api/v1/venue-types', $venueTypeData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Teatro')
                ->assertJsonPath('data.category', 'entertainment')
                ->assertJsonPath('data.icon', 'fa-theater-masks');
                
            $this->assertDatabaseHas('venue_types', [
                'name' => 'Teatro',
                'slug' => 'teatro',
                'category' => 'entertainment'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/venue-types', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'category']);
        });
        
        test('returns 422 with invalid category', function () {
            $venueTypeData = [
                'name' => 'Test Type',
                'slug' => 'test-type',
                'category' => 'invalid_category'
            ];
            
            $response = $this->postJson('/api/v1/venue-types', $venueTypeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 422 with duplicate slug', function () {
            VenueType::factory()->create(['slug' => 'test-type']);
            
            $venueTypeData = [
                'name' => 'Another Type',
                'slug' => 'test-type',
                'category' => 'entertainment'
            ];
            
            $response = $this->postJson('/api/v1/venue-types', $venueTypeData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
        });
    });
    
    describe('GET /api/v1/venue-types/{id}', function () {
        test('returns venue type details', function () {
            $venueType = VenueType::factory()->create();
            
            $response = $this->getJson("/api/v1/venue-types/{$venueType->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $venueType->id)
                ->assertJsonPath('data.name', $venueType->name)
                ->assertJsonPath('data.category', $venueType->category);
        });
        
        test('returns 404 for non-existent venue type', function () {
            $response = $this->getJson('/api/v1/venue-types/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/venue-types/{id}', function () {
        test('updates venue type with valid data', function () {
            $venueType = VenueType::factory()->create();
            $updateData = [
                'name' => 'Teatro Clásico',
                'description' => 'Lugar para presentaciones teatrales clásicas'
            ];
            
            $response = $this->putJson("/api/v1/venue-types/{$venueType->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Teatro Clásico')
                ->assertJsonPath('data.description', 'Lugar para presentaciones teatrales clásicas');
                
            $this->assertDatabaseHas('venue_types', [
                'id' => $venueType->id,
                'name' => 'Teatro Clásico',
                'description' => 'Lugar para presentaciones teatrales clásicas'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $venueType = VenueType::factory()->create();
            
            $response = $this->putJson("/api/v1/venue-types/{$venueType->id}", [
                'category' => 'invalid_category'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
        
        test('returns 404 for non-existent venue type', function () {
            $response = $this->putJson('/api/v1/venue-types/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/venue-types/{id}', function () {
        test('deletes venue type successfully', function () {
            $venueType = VenueType::factory()->create();
            
            $response = $this->deleteJson("/api/v1/venue-types/{$venueType->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('venue_types', ['id' => $venueType->id]);
        });
        
        test('returns 404 for non-existent venue type', function () {
            $response = $this->deleteJson('/api/v1/venue-types/999');
            
            $response->assertStatus(404);
        });
    });
});
