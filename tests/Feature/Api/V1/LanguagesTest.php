<?php

declare(strict_types=1);

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Languages API', function () {
    it('returns paginated languages', function () {
        Language::create(['name' => 'English', 'slug' => 'english', 'iso_code' => 'en']);
        Language::create(['name' => 'Spanish', 'slug' => 'spanish', 'iso_code' => 'es']);
        Language::create(['name' => 'French', 'slug' => 'french', 'iso_code' => 'fr']);
        $response = $this->getJson('/api/v1/languages');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns a specific language by slug', function () {
        $language = Language::create(['name' => 'English', 'slug' => 'english', 'iso_code' => 'en']);
        $response = $this->getJson("/api/v1/languages/{$language->slug}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns a specific language by id', function () {
        $language = Language::create(['name' => 'English', 'slug' => 'english', 'iso_code' => 'en']);
        $response = $this->getJson("/api/v1/languages/{$language->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns 404 for non-existent language', function () {
        $response = $this->getJson('/api/v1/languages/non-existent');
        $response->assertNotFound();
    });
});
