<?php

declare(strict_types=1);

use App\Models\Timezone;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Timezones API', function () {
    it('returns paginated timezones', function () {
        Timezone::create(['name' => 'Europe/Madrid']);
        Timezone::create(['name' => 'America/New_York']);
        Timezone::create(['name' => 'Asia/Tokyo']);
        $response = $this->getJson('/api/v1/timezones');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name']]]);
    });

    it('returns a specific timezone by name', function () {
        $timezone = Timezone::create(['name' => 'Europe/Madrid']);
        $response = $this->getJson("/api/v1/timezones/{$timezone->name}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name']]);
    });

    it('returns a specific timezone by id', function () {
        $timezone = Timezone::create(['name' => 'Europe/Madrid']);
        $response = $this->getJson("/api/v1/timezones/{$timezone->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name']]);
    });

    it('returns 404 for non-existent timezone', function () {
        $response = $this->getJson('/api/v1/timezones/non-existent');
        $response->assertNotFound();
    });
});
