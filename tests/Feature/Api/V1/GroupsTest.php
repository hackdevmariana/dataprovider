<?php

declare(strict_types=1);

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Groups API', function () {
    it('returns a list of groups', function () {
        Group::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/groups');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'description']]]);
    });

    it('returns a single group by id', function () {
        $group = Group::factory()->create();
        $response = $this->getJson('/api/v1/groups/' . $group->id);
        $response->assertOk()->assertJsonPath('data.id', $group->id);
    });

    it('returns 404 for non-existent group', function () {
        $response = $this->getJson('/api/v1/groups/999999');
        $response->assertNotFound();
    });

    it('creates a group with valid data', function () {
        $data = [
            'name' => 'Test Group',
        ];
        $response = $this->postJson('/api/v1/groups', $data);
        $response->assertCreated()->assertJsonPath('data.name', 'Test Group');
        $this->assertDatabaseHas('groups', ['name' => 'Test Group']);
    });

    it('returns 422 for invalid group data', function () {
        $response = $this->postJson('/api/v1/groups', [
            'name' => '', // required
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    });
});
