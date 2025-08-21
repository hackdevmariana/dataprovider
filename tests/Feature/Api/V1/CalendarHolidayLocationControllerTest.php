<?php

use App\Models\User;
use App\Models\CalendarHolidayLocation;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('CalendarHolidayLocationController', function () {
    
    describe('GET /api/v1/calendar-holiday-locations', function () {
        test('returns paginated list of calendar holiday locations', function () {
            CalendarHolidayLocation::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/calendar-holiday-locations');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'country',
                            'region',
                            'description',
                            'timezone',
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
        
        test('filters locations by country', function () {
            CalendarHolidayLocation::factory()->create(['country' => 'Spain']);
            CalendarHolidayLocation::factory()->create(['country' => 'France']);
            
            $response = $this->getJson('/api/v1/calendar-holiday-locations?country=Spain');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.country', 'Spain');
        });
        
        test('filters locations by region', function () {
            CalendarHolidayLocation::factory()->create(['region' => 'Madrid']);
            CalendarHolidayLocation::factory()->create(['region' => 'Barcelona']);
            
            $response = $this->getJson('/api/v1/calendar-holiday-locations?region=Madrid');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.region', 'Madrid');
        });
        
        test('filters locations by is_active', function () {
            CalendarHolidayLocation::factory()->create(['is_active' => true]);
            CalendarHolidayLocation::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/calendar-holiday-locations?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches locations by name or description', function () {
            CalendarHolidayLocation::factory()->create(['name' => 'Madrid']);
            CalendarHolidayLocation::factory()->create(['name' => 'Barcelona']);
            
            $response = $this->getJson('/api/v1/calendar-holiday-locations?search=madrid');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Madrid');
        });
        
        test('respects per_page parameter', function () {
            CalendarHolidayLocation::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/calendar-holiday-locations?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/calendar-holiday-locations', function () {
        test('creates new calendar holiday location with valid data', function () {
            $locationData = [
                'name' => 'Madrid',
                'country' => 'Spain',
                'region' => 'Madrid',
                'description' => 'Capital de España',
                'timezone' => 'Europe/Madrid',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/calendar-holiday-locations', $locationData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Madrid')
                ->assertJsonPath('data.country', 'Spain')
                ->assertJsonPath('data.timezone', 'Europe/Madrid');
                
            $this->assertDatabaseHas('calendar_holiday_locations', [
                'name' => 'Madrid',
                'country' => 'Spain',
                'timezone' => 'Europe/Madrid'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/calendar-holiday-locations', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'country']);
        });
        
        test('returns 422 with invalid timezone', function () {
            $locationData = [
                'name' => 'Test Location',
                'country' => 'Test Country',
                'timezone' => 'invalid-timezone'
            ];
            
            $response = $this->postJson('/api/v1/calendar-holiday-locations', $locationData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['timezone']);
        });
        
        test('returns 422 with duplicate name in same country', function () {
            CalendarHolidayLocation::factory()->create([
                'name' => 'Madrid',
                'country' => 'Spain'
            ]);
            
            $locationData = [
                'name' => 'Madrid',
                'country' => 'Spain',
                'region' => 'Madrid'
            ];
            
            $response = $this->postJson('/api/v1/calendar-holiday-locations', $locationData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
    });
    
    describe('GET /api/v1/calendar-holiday-locations/{id}', function () {
        test('returns calendar holiday location details', function () {
            $location = CalendarHolidayLocation::factory()->create();
            
            $response = $this->getJson("/api/v1/calendar-holiday-locations/{$location->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $location->id)
                ->assertJsonPath('data.name', $location->name)
                ->assertJsonPath('data.country', $location->country);
        });
        
        test('returns 404 for non-existent location', function () {
            $response = $this->getJson('/api/v1/calendar-holiday-locations/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/calendar-holiday-locations/{id}', function () {
        test('updates calendar holiday location with valid data', function () {
            $location = CalendarHolidayLocation::factory()->create();
            $updateData = [
                'name' => 'Madrid Capital',
                'description' => 'Capital de España y sede del gobierno'
            ];
            
            $response = $this->putJson("/api/v1/calendar-holiday-locations/{$location->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Madrid Capital')
                ->assertJsonPath('data.description', 'Capital de España y sede del gobierno');
                
            $this->assertDatabaseHas('calendar_holiday_locations', [
                'id' => $location->id,
                'name' => 'Madrid Capital',
                'description' => 'Capital de España y sede del gobierno'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $location = CalendarHolidayLocation::factory()->create();
            
            $response = $this->putJson("/api/v1/calendar-holiday-locations/{$location->id}", [
                'timezone' => 'invalid-timezone'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['timezone']);
        });
        
        test('returns 404 for non-existent location', function () {
            $response = $this->putJson('/api/v1/calendar-holiday-locations/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/calendar-holiday-locations/{id}', function () {
        test('deletes calendar holiday location successfully', function () {
            $location = CalendarHolidayLocation::factory()->create();
            
            $response = $this->deleteJson("/api/v1/calendar-holiday-locations/{$location->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('calendar_holiday_locations', ['id' => $location->id]);
        });
        
        test('returns 404 for non-existent location', function () {
            $response = $this->deleteJson('/api/v1/calendar-holiday-locations/999');
            
            $response->assertStatus(404);
        });
    });
});
