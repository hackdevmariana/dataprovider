<?php

use App\Models\User;
use App\Models\UserGeneratedContent;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('UserGeneratedContentController', function () {
    
    describe('GET /api/v1/user-generated-contents', function () {
        test('returns paginated list of user generated contents', function () {
            UserGeneratedContent::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/user-generated-contents');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'title',
                            'content',
                            'type',
                            'status',
                            'is_featured',
                            'likes_count',
                            'dislikes_count'
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
        
        test('filters by type', function () {
            UserGeneratedContent::factory()->create(['type' => 'article']);
            UserGeneratedContent::factory()->create(['type' => 'review']);
            
            $response = $this->getJson('/api/v1/user-generated-contents?type=article');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.type', 'article');
        });
        
        test('filters by status', function () {
            UserGeneratedContent::factory()->create(['status' => 'published']);
            UserGeneratedContent::factory()->create(['status' => 'draft']);
            
            $response = $this->getJson('/api/v1/user-generated-contents?status=published');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'published');
        });
        
        test('filters by is_featured', function () {
            UserGeneratedContent::factory()->create(['is_featured' => true]);
            UserGeneratedContent::factory()->create(['is_featured' => false]);
            
            $response = $this->getJson('/api/v1/user-generated-contents?is_featured=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_featured', true);
        });
        
        test('searches by title or content', function () {
            UserGeneratedContent::factory()->create(['title' => 'Sostenibilidad Verde']);
            UserGeneratedContent::factory()->create(['title' => 'Tecnología Limpia']);
            
            $response = $this->getJson('/api/v1/user-generated-contents?search=sostenibilidad');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Sostenibilidad Verde');
        });
        
        test('respects per_page parameter', function () {
            UserGeneratedContent::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/user-generated-contents?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/user-generated-contents', function () {
        test('creates new user generated content with valid data', function () {
            $contentData = [
                'title' => 'Mi Experiencia con Sostenibilidad',
                'content' => 'Este es el contenido de mi artículo sobre sostenibilidad...',
                'type' => 'article',
                'status' => 'published',
                'tags' => ['sostenibilidad', 'medio ambiente']
            ];
            
            $response = $this->postJson('/api/v1/user-generated-contents', $contentData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.title', 'Mi Experiencia con Sostenibilidad')
                ->assertJsonPath('data.type', 'article')
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('user_generated_contents', [
                'title' => 'Mi Experiencia con Sostenibilidad',
                'type' => 'article',
                'user_id' => $this->user->id
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/user-generated-contents', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'content', 'type']);
        });
        
        test('returns 422 with invalid type', function () {
            $contentData = [
                'title' => 'Test Title',
                'content' => 'Test content',
                'type' => 'invalid_type'
            ];
            
            $response = $this->postJson('/api/v1/user-generated-contents', $contentData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 422 with invalid status', function () {
            $contentData = [
                'title' => 'Test Title',
                'content' => 'Test content',
                'type' => 'article',
                'status' => 'invalid_status'
            ];
            
            $response = $this->postJson('/api/v1/user-generated-contents', $contentData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 422 with title too short', function () {
            $contentData = [
                'title' => 'Hi',
                'content' => 'Test content',
                'type' => 'article'
            ];
            
            $response = $this->postJson('/api/v1/user-generated-contents', $contentData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['title']);
        });
    });
    
    describe('GET /api/v1/user-generated-contents/{id}', function () {
        test('returns user generated content details', function () {
            $content = UserGeneratedContent::factory()->create();
            
            $response = $this->getJson("/api/v1/user-generated-contents/{$content->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $content->id)
                ->assertJsonPath('data.title', $content->title)
                ->assertJsonPath('data.type', $content->type);
        });
        
        test('returns 404 for non-existent content', function () {
            $response = $this->getJson('/api/v1/user-generated-contents/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/user-generated-contents/{id}', function () {
        test('updates user generated content with valid data', function () {
            $content = UserGeneratedContent::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'title' => 'Mi Experiencia Actualizada con Sostenibilidad',
                'status' => 'draft'
            ];
            
            $response = $this->putJson("/api/v1/user-generated-contents/{$content->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.title', 'Mi Experiencia Actualizada con Sostenibilidad')
                ->assertJsonPath('data.status', 'draft');
                
            $this->assertDatabaseHas('user_generated_contents', [
                'id' => $content->id,
                'title' => 'Mi Experiencia Actualizada con Sostenibilidad',
                'status' => 'draft'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $content = UserGeneratedContent::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/user-generated-contents/{$content->id}", [
                'type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 404 for non-existent content', function () {
            $response = $this->putJson('/api/v1/user-generated-contents/999', [
                'title' => 'Updated Title'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/user-generated-contents/{id}', function () {
        test('deletes user generated content successfully', function () {
            $content = UserGeneratedContent::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/user-generated-contents/{$content->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('user_generated_contents', ['id' => $content->id]);
        });
        
        test('returns 404 for non-existent content', function () {
            $response = $this->deleteJson('/api/v1/user-generated-contents/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('POST /api/v1/user-generated-contents/{id}/like', function () {
        test('likes content successfully', function () {
            $content = UserGeneratedContent::factory()->create();
            
            $response = $this->postJson("/api/v1/user-generated-contents/{$content->id}/like");
            
            $response->assertStatus(200)
                ->assertJsonPath('message', 'Contenido marcado como me gusta');
        });
        
        test('returns 404 for non-existent content', function () {
            $response = $this->postJson('/api/v1/user-generated-contents/999/like');
            
            $response->assertStatus(404);
        });
    });
    
    describe('POST /api/v1/user-generated-contents/{id}/dislike', function () {
        test('dislikes content successfully', function () {
            $content = UserGeneratedContent::factory()->create();
            
            $response = $this->postJson("/api/v1/user-generated-contents/{$content->id}/dislike");
            
            $response->assertStatus(200)
                ->assertJsonPath('message', 'Contenido marcado como no me gusta');
        });
        
        test('returns 404 for non-existent content', function () {
            $response = $this->postJson('/api/v1/user-generated-contents/999/dislike');
            
            $response->assertStatus(404);
        });
    });
});
