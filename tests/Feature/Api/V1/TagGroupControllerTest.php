<?php

use App\Models\User;
use App\Models\TagGroup;
use App\Models\Tag;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('TagGroupController', function () {
    
    describe('GET /api/v1/tag-groups', function () {
        test('returns paginated list of tag groups', function () {
            TagGroup::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/tag-groups');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'is_active',
                            'tags_count'
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
        
        test('filters tag groups by is_active', function () {
            TagGroup::factory()->create(['is_active' => true]);
            TagGroup::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/tag-groups?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches tag groups by name or description', function () {
            TagGroup::factory()->create(['name' => 'Sostenibilidad']);
            TagGroup::factory()->create(['name' => 'Tecnología']);
            
            $response = $this->getJson('/api/v1/tag-groups?search=sostenibilidad');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Sostenibilidad');
        });
        
        test('respects per_page parameter', function () {
            TagGroup::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/tag-groups?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/tag-groups', function () {
        test('creates new tag group with valid data', function () {
            $tagGroupData = [
                'name' => 'Sostenibilidad',
                'slug' => 'sostenibilidad',
                'description' => 'Grupo de etiquetas relacionadas con sostenibilidad',
                'is_active' => true,
                'color' => '#4CAF50',
                'icon' => 'fa-leaf'
            ];
            
            $response = $this->postJson('/api/v1/tag-groups', $tagGroupData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Sostenibilidad')
                ->assertJsonPath('data.slug', 'sostenibilidad')
                ->assertJsonPath('data.color', '#4CAF50');
                
            $this->assertDatabaseHas('tag_groups', [
                'name' => 'Sostenibilidad',
                'slug' => 'sostenibilidad',
                'is_active' => true
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/tag-groups', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug']);
        });
        
        test('returns 422 with invalid color format', function () {
            $tagGroupData = [
                'name' => 'Test Group',
                'slug' => 'test-group',
                'color' => 'invalid-color'
            ];
            
            $response = $this->postJson('/api/v1/tag-groups', $tagGroupData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['color']);
        });
        
        test('returns 422 with duplicate slug', function () {
            TagGroup::factory()->create(['slug' => 'test-group']);
            
            $tagGroupData = [
                'name' => 'Another Group',
                'slug' => 'test-group'
            ];
            
            $response = $this->postJson('/api/v1/tag-groups', $tagGroupData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
        });
    });
    
    describe('GET /api/v1/tag-groups/{id}', function () {
        test('returns tag group details', function () {
            $tagGroup = TagGroup::factory()->create();
            
            $response = $this->getJson("/api/v1/tag-groups/{$tagGroup->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $tagGroup->id)
                ->assertJsonPath('data.name', $tagGroup->name)
                ->assertJsonPath('data.slug', $tagGroup->slug);
        });
        
        test('returns 404 for non-existent tag group', function () {
            $response = $this->getJson('/api/v1/tag-groups/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/tag-groups/{id}', function () {
        test('updates tag group with valid data', function () {
            $tagGroup = TagGroup::factory()->create();
            $updateData = [
                'name' => 'Sostenibilidad Verde',
                'color' => '#2E7D32'
            ];
            
            $response = $this->putJson("/api/v1/tag-groups/{$tagGroup->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Sostenibilidad Verde')
                ->assertJsonPath('data.color', '#2E7D32');
                
            $this->assertDatabaseHas('tag_groups', [
                'id' => $tagGroup->id,
                'name' => 'Sostenibilidad Verde',
                'color' => '#2E7D32'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $tagGroup = TagGroup::factory()->create();
            
            $response = $this->putJson("/api/v1/tag-groups/{$tagGroup->id}", [
                'color' => 'invalid-color'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['color']);
        });
        
        test('returns 404 for non-existent tag group', function () {
            $response = $this->putJson('/api/v1/tag-groups/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/tag-groups/{id}', function () {
        test('deletes tag group successfully', function () {
            $tagGroup = TagGroup::factory()->create();
            
            $response = $this->deleteJson("/api/v1/tag-groups/{$tagGroup->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('tag_groups', ['id' => $tagGroup->id]);
        });
        
        test('returns 422 when tag group has tags', function () {
            $tagGroup = TagGroup::factory()->create();
            Tag::factory()->create(['tag_group_id' => $tagGroup->id]);
            
            $response = $this->deleteJson("/api/v1/tag-groups/{$tagGroup->id}");
            
            $response->assertStatus(422)
                ->assertJsonPath('message', 'No se puede eliminar el grupo porque tiene etiquetas asociadas');
        });
        
        test('returns 404 for non-existent tag group', function () {
            $response = $this->deleteJson('/api/v1/tag-groups/999');
            
            $response->assertStatus(404);
        });
    });
});
