<?php

declare(strict_types=1);

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Persons API', function () {
    it('returns paginated persons', function () {
        Person::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/persons');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns a specific person by slug', function () {
        $person = Person::factory()->create();
        $response = $this->getJson("/api/v1/persons/{$person->slug}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns a specific person by id', function () {
        $person = Person::factory()->create();
        $response = $this->getJson("/api/v1/persons/{$person->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns 404 for non-existent person', function () {
        $response = $this->getJson('/api/v1/persons/non-existent');
        $response->assertNotFound();
    });
});
