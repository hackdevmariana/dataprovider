<?php

declare(strict_types=1);

use App\Models\EventType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('EventTypes API', function () {
    it('returns a list of event types', function () {
        EventType::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/event-types');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug', 'description']]]);
    });

    it('returns a single event type by id', function () {
        $eventType = EventType::factory()->create();
        $response = $this->getJson('/api/v1/event-types/' . $eventType->id);
        $response->assertOk()->assertJsonPath('data.id', $eventType->id);
    });

    it('returns a single event type by slug', function () {
        $eventType = EventType::factory()->create(['slug' => 'test-type']);
        $response = $this->getJson('/api/v1/event-types/test-type');
        $response->assertOk()->assertJsonPath('data.slug', 'test-type');
    });

    it('returns 404 for non-existent event type', function () {
        $response = $this->getJson('/api/v1/event-types/999999');
        $response->assertNotFound();
    });
});
