<?php

declare(strict_types=1);

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Events API', function () {
    it('returns a list of events', function () {
        Event::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/events');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'title', 'slug', 'description', 'start_datetime', 'end_datetime']]]);
    });

    it('returns a single event by id', function () {
        $event = Event::factory()->create();
        $response = $this->getJson('/api/v1/events/' . $event->id);
        $response->assertOk()->assertJsonPath('data.id', $event->id);
    });

    it('returns a single event by slug', function () {
        $event = Event::factory()->create(['slug' => 'test-event']);
        $response = $this->getJson('/api/v1/events/test-event');
        $response->assertOk()->assertJsonPath('data.slug', 'test-event');
    });

    it('returns 404 for non-existent event', function () {
        $response = $this->getJson('/api/v1/events/999999');
        $response->assertNotFound();
    });
});
