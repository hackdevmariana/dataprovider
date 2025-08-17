<?php

declare(strict_types=1);

use App\Models\Festival;
use App\Models\Event;
use App\Models\Artist;
use App\Models\Municipality;
use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Festivals Advanced API', function () {
    it('returns events of a festival', function () {
        $festival = Festival::factory()->create();
        Event::factory()->count(2)->create(['festival_id' => $festival->id]);
        $response = $this->getJson("/api/v1/festivals/{$festival->id}/events");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'title', 'festival_id']]]);
    });

    it('returns artists of a festival', function () {
        $festival = Festival::factory()->create();
        $event = Event::factory()->create(['festival_id' => $festival->id]);
        $artist = Artist::factory()->create();
        $event->artists()->attach($artist->id);
        $response = $this->getJson("/api/v1/festivals/{$festival->id}/artists");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name']]]);
    });

    it('returns festivals by municipality', function () {
        $municipality = Municipality::factory()->create();
        $festival = Festival::factory()->create(['location_id' => $municipality->id]);
        $response = $this->getJson("/api/v1/festivals/municipality/{$municipality->id}");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'location_id']]]);
    });

    it('returns festivals by region', function () {
        $region = Region::factory()->create();
        $municipality = Municipality::factory()->create(['region_id' => $region->id]);
        $festival = Festival::factory()->create(['location_id' => $municipality->id]);
        $response = $this->getJson("/api/v1/festivals/region/{$region->id}");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'location_id']]]);
    });

    it('returns festivals by province', function () {
        $province = Province::factory()->create();
        $municipality = Municipality::factory()->create(['province_id' => $province->id]);
        $festival = Festival::factory()->create(['location_id' => $municipality->id]);
        $response = $this->getJson("/api/v1/festivals/province/{$province->id}");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'location_id']]]);
    });

    it('returns festivals by autonomous community', function () {
        $community = AutonomousCommunity::factory()->create();
        $municipality = Municipality::factory()->create(['autonomous_community_id' => $community->id]);
        $festival = Festival::factory()->create(['location_id' => $municipality->id]);
        $response = $this->getJson("/api/v1/festivals/autonomous-community/{$community->id}");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'location_id']]]);
    });

    it('returns festivals today', function () {
        $municipality = \App\Models\Municipality::factory()->create();
        $festival = \App\Models\Festival::factory()->create(['location_id' => $municipality->id]);
        $date = now()->format('Y-m-d') . ' 10:00:00';
        $event = \App\Models\Event::factory()->create(['festival_id' => $festival->id, 'start_datetime' => $date]);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'festival_id' => $festival->id,
            'start_datetime' => $date,
        ]);
        $response = $this->getJson('/api/v1/festivals/today');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name']]]);
    });

    it('returns festivals this week', function () {
        $municipality = \App\Models\Municipality::factory()->create();
        $festival = \App\Models\Festival::factory()->create(['location_id' => $municipality->id]);
        // Use a date that's definitely within this week (start of week + 1 day)
        $date = now()->startOfWeek()->addDay()->format('Y-m-d') . ' 10:00:00';
        $event = \App\Models\Event::factory()->create(['festival_id' => $festival->id, 'start_datetime' => $date]);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'festival_id' => $festival->id,
            'start_datetime' => $date,
        ]);
        $response = $this->getJson('/api/v1/festivals/this-week');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name']]]);
    });

    it('returns festivals this month', function () {
        $municipality = \App\Models\Municipality::factory()->create();
        $festival = \App\Models\Festival::factory()->create(['location_id' => $municipality->id]);
        $date = now()->addDays(10)->format('Y-m-d') . ' 10:00:00';
        $event = \App\Models\Event::factory()->create(['festival_id' => $festival->id, 'start_datetime' => $date]);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'festival_id' => $festival->id,
            'start_datetime' => $date,
        ]);
        $response = $this->getJson('/api/v1/festivals/this-month');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name']]]);
    });

    it('returns festivals this year', function () {
        $municipality = \App\Models\Municipality::factory()->create();
        $festival = \App\Models\Festival::factory()->create(['location_id' => $municipality->id]);
        $date = now()->addMonths(2)->format('Y-m-d') . ' 10:00:00';
        $event = \App\Models\Event::factory()->create(['festival_id' => $festival->id, 'start_datetime' => $date]);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'festival_id' => $festival->id,
            'start_datetime' => $date,
        ]);
        $response = $this->getJson('/api/v1/festivals/this-year');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name']]]);
    });

    it('returns festivals and unassigned events', function () {
        $festival = Festival::factory()->create();
        $event = Event::factory()->create(['festival_id' => null]);
        $response = $this->getJson('/api/v1/festivals-and-unassigned-events');
        $response->assertOk()->assertJsonStructure(['festivals', 'unassigned_events']);
    });

    it('returns festivals with events and unassigned events', function () {
        $festival = Festival::factory()->create();
        Event::factory()->create(['festival_id' => $festival->id]);
        Event::factory()->create(['festival_id' => null]);
        $response = $this->getJson('/api/v1/festivals-with-events-and-unassigned');
        $response->assertOk()->assertJsonStructure(['festivals', 'unassigned_events']);
    });
});
