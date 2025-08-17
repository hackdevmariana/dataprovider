<?php

declare(strict_types=1);

use App\Models\FamilyMember;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Family Members API', function () {
    it('returns paginated family members', function () {
        FamilyMember::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/family-members');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name']]]);
    });

    it('returns a specific family member', function () {
        $familyMember = FamilyMember::factory()->create();
        $response = $this->getJson("/api/v1/family-members/{$familyMember->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name']]);
    });

    it('returns 404 for non-existent family member', function () {
        $response = $this->getJson('/api/v1/family-members/99999');
        $response->assertNotFound();
    });
});
