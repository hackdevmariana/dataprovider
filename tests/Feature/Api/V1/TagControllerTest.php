<?php

use App\Models\User;
use App\Models\Tag;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('TagController', function () {
    
    describe('GET /api/v1/tags', function () {
        test('returns paginated list of tags', function () {
            Tag::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/tags');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'color',
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
        
        test('filters tags by is_active', function () {
            Tag::factory()->create(['is_active' => true]);
            Tag::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/tags?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches tags by name or description', function () {
            Tag::factory()->create(['name' => 'Sostenibilidad']);
            Tag::factory()->create(['name' => 'Tecnología']);
            
            $response = $this->getJson('/api/v1/tags?search=sostenibilidad');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Sostenibilidad');
        });
        
        test('respects per_page parameter', function () {
            Tag::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/tags?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/tags', function () {
        test('creates new tag with valid data', function () {
            $tagData = [
                'name' => 'Sostenibilidad',
                'slug' => 'sostenibilidad',
                'description' => 'Tag relacionado con sostenibilidad',
                'color' => '#4CAF50',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/tags', $tagData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Sostenibilidad')
                ->assertJsonPath('data.slug', 'sostenibilidad')
                ->assertJsonPath('data.color', '#4CAF50');
                
            $this->assertDatabaseHas('tags', [
                'name' => 'Sostenibilidad',
                'slug' => 'sostenibilidad',
                'color' => '#4CAF50'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/tags', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug']);
        });
        
        test('returns 422 with invalid color format', function () {
            $tagData = [
                'name' => 'Test Tag',
                'slug' => 'test-tag',
                'color' => 'invalid-color'
            ];
            
            $response = $this->postJson('/api/v1/tags', $tagData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['color']);
        });
        
        test('returns 422 with duplicate slug', function () {
            Tag::factory()->create(['slug' => 'test-tag']);
            
            $tagData = [
                'name' => 'Another Tag',
                'slug' => 'test-tag'
            ];
            
            $response = $this->postJson('/api/v1/tags', $tagData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
        });
    });
    
    describe('GET /api/v1/tags/{id}', function () {
        test('returns tag details', function () {
            $tag = Tag::factory()->create();
            
            $response = $this->getJson("/api/v1/tags/{$tag->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $tag->id)
                ->assertJsonPath('data.name', $tag->name)
                ->assertJsonPath('data.slug', $tag->slug);
        });
        
        test('returns 404 for non-existent tag', function () {
            $response = $this->getJson('/api/v1/tags/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/tags/{id}', function () {
        test('updates tag with valid data', function () {
            $tag = Tag::factory()->create();
            $updateData = [
                'name' => 'Sostenibilidad Verde',
                'color' => '#2E7D32'
            ];
            
            $response = $this->putJson("/api/v1/tags/{$tag->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Sostenibilidad Verde')
                ->assertJsonPath('data.color', '#2E7D32');
                
            $this->assertDatabaseHas('tags', [
                'id' => $tag->id,
                'name' => 'Sostenibilidad Verde',
                'color' => '#2E7D32'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $tag = Tag::factory()->create();
            
            $response = $this->putJson("/api/v1/tags/{$tag->id}", [
                'color' => 'invalid-color'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['color']);
        });
        
        test('returns 404 for non-existent tag', function () {
            $response = $this->putJson('/api/v1/tags/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/tags/{id}', function () {
        test('deletes tag successfully', function () {
            $tag = Tag::factory()->create();
            
            $response = $this->deleteJson("/api/v1/tags/{$tag->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        });
        
        test('returns 404 for non-existent tag', function () {
            $response = $this->deleteJson('/api/v1/tags/999');
            
            $response->assertStatus(404);
        });
    });
});
