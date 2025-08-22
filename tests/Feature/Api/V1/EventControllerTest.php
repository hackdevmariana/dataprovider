<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Organization;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('EventController', function () {
    
    describe('GET /api/v1/events', function () {
        test('returns paginated list of events', function () {
            Event::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/events');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'start_date',
                            'end_date',
                            'venue_id',
                            'organization_id',
                            'status',
                            'is_featured',
                            'venue',
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
        
        test('filters events by venue_id', function () {
            $venue = Venue::factory()->create();
            Event::factory()->create(['venue_id' => $venue->id]);
            Event::factory()->create(['venue_id' => Venue::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/events?venue_id={$venue->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.venue_id', $venue->id);
        });
        
        test('filters events by organization_id', function () {
            $organization = Organization::factory()->create();
            Event::factory()->create(['organization_id' => $organization->id]);
            Event::factory()->create(['organization_id' => Organization::factory()->create()->id]);
            
            $response = $this->getJson("/api/v1/events?organization_id={$organization->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.organization_id', $organization->id);
        });
        
        test('filters events by status', function () {
            Event::factory()->create(['status' => 'upcoming']);
            Event::factory()->create(['status' => 'ongoing']);
            
            $response = $this->getJson('/api/v1/events?status=upcoming');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'upcoming');
        });
        
        test('filters events by is_featured', function () {
            Event::factory()->create(['is_featured' => true]);
            Event::factory()->create(['is_featured' => false]);
            
            $response = $this->getJson('/api/v1/events?is_featured=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_featured', true);
        });
        
        test('filters events by date range', function () {
            Event::factory()->create(['start_date' => '2024-06-01']);
            Event::factory()->create(['start_date' => '2024-08-01']);
            
            $response = $this->getJson('/api/v1/events?start_date_from=2024-06-01&start_date_to=2024-07-31');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('searches events by title or description', function () {
            Event::factory()->create(['title' => 'Conferencia de Sostenibilidad']);
            Event::factory()->create(['title' => 'Workshop de Tecnología']);
            
            $response = $this->getJson('/api/v1/events?search=sostenibilidad');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Conferencia de Sostenibilidad');
        });
        
        test('respects per_page parameter', function () {
            Event::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/events?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/events', function () {
        test('creates new event with valid data', function () {
            $venue = Venue::factory()->create();
            $organization = Organization::factory()->create();
            $eventData = [
                'title' => 'Conferencia de Sostenibilidad',
                'description' => 'Evento sobre prácticas sostenibles',
                'start_date' => '2024-06-01 09:00:00',
                'end_date' => '2024-06-01 17:00:00',
                'venue_id' => $venue->id,
                'organization_id' => $organization->id,
                'status' => 'upcoming',
                'is_featured' => true,
                'max_attendees' => 100,
                'registration_required' => true
            ];
            
            $response = $this->postJson('/api/v1/events', $eventData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.title', 'Conferencia de Sostenibilidad')
                ->assertJsonPath('data.venue_id', $venue->id)
                ->assertJsonPath('data.status', 'upcoming');
                
            $this->assertDatabaseHas('events', [
                'title' => 'Conferencia de Sostenibilidad',
                'venue_id' => $venue->id,
                'status' => 'upcoming'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/events', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'start_date', 'venue_id']);
        });
        
        test('returns 422 with invalid venue_id', function () {
            $eventData = [
                'title' => 'Test Event',
                'start_date' => '2024-06-01 09:00:00',
                'venue_id' => 999
            ];
            
            $response = $this->postJson('/api/v1/events', $eventData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['venue_id']);
        });
        
        test('returns 422 with invalid organization_id', function () {
            $venue = Venue::factory()->create();
            $eventData = [
                'title' => 'Test Event',
                'start_date' => '2024-06-01 09:00:00',
                'venue_id' => $venue->id,
                'organization_id' => 999
            ];
            
            $response = $this->postJson('/api/v1/events', $eventData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['organization_id']);
        });
        
        test('returns 422 with end_date before start_date', function () {
            $venue = Venue::factory()->create();
            $eventData = [
                'title' => 'Test Event',
                'start_date' => '2024-06-01 17:00:00',
                'end_date' => '2024-06-01 09:00:00',
                'venue_id' => $venue->id
            ];
            
            $response = $this->postJson('/api/v1/events', $eventData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['end_date']);
        });
        
        test('returns 422 with invalid status', function () {
            $venue = Venue::factory()->create();
            $eventData = [
                'title' => 'Test Event',
                'start_date' => '2024-06-01 09:00:00',
                'venue_id' => $venue->id,
                'status' => 'invalid_status'
            ];
            
            $response = $this->postJson('/api/v1/events', $eventData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
    });
    
    describe('GET /api/v1/events/{id}', function () {
        test('returns event details', function () {
            $event = Event::factory()->create();
            
            $response = $this->getJson("/api/v1/events/{$event->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $event->id)
                ->assertJsonPath('data.title', $event->title)
                ->assertJsonPath('data.start_date', $event->start_date);
        });
        
        test('returns 404 for non-existent event', function () {
            $response = $this->getJson('/api/v1/events/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/events/{id}', function () {
        test('updates event with valid data', function () {
            $event = Event::factory()->create();
            $updateData = [
                'title' => 'Conferencia de Sostenibilidad Actualizada',
                'description' => 'Evento sobre prácticas sostenibles (actualizado)',
                'status' => 'ongoing'
            ];
            
            $response = $this->putJson("/api/v1/events/{$event->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.title', 'Conferencia de Sostenibilidad Actualizada')
                ->assertJsonPath('data.status', 'ongoing');
                
            $this->assertDatabaseHas('events', [
                'id' => $event->id,
                'title' => 'Conferencia de Sostenibilidad Actualizada',
                'status' => 'ongoing'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $event = Event::factory()->create();
            
            $response = $this->putJson("/api/v1/events/{$event->id}", [
                'venue_id' => 999
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['venue_id']);
        });
        
        test('returns 404 for non-existent event', function () {
            $response = $this->putJson('/api/v1/events/999', [
                'title' => 'Updated Title'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/events/{id}', function () {
        test('deletes event successfully', function () {
            $event = Event::factory()->create();
            
            $response = $this->deleteJson("/api/v1/events/{$event->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('events', ['id' => $event->id]);
        });
        
        test('returns 404 for non-existent event', function () {
            $response = $this->deleteJson('/api/v1/events/999');
            
            $response->assertStatus(404);
        });
    });
});
