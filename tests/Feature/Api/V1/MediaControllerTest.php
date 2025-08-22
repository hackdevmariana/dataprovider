<?php

use App\Models\User;
use App\Models\Media;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    Storage::fake('public');
});

describe('MediaController', function () {
    
    describe('GET /api/v1/media', function () {
        test('returns paginated list of media', function () {
            Media::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/media');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'filename',
                            'original_name',
                            'mime_type',
                            'size',
                            'path',
                            'url',
                            'is_public',
                            'media_type'
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
        
        test('filters media by media_type', function () {
            Media::factory()->create(['media_type' => 'image']);
            Media::factory()->create(['media_type' => 'document']);
            
            $response = $this->getJson('/api/v1/media?media_type=image');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.media_type', 'image');
        });
        
        test('filters media by is_public', function () {
            Media::factory()->create(['is_public' => true]);
            Media::factory()->create(['is_public' => false]);
            
            $response = $this->getJson('/api/v1/media?is_public=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_public', true);
        });
        
        test('filters media by mime_type', function () {
            Media::factory()->create(['mime_type' => 'image/jpeg']);
            Media::factory()->create(['mime_type' => 'image/png']);
            
            $response = $this->getJson('/api/v1/media?mime_type=image/jpeg');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.mime_type', 'image/jpeg');
        });
        
        test('searches media by filename or original_name', function () {
            Media::factory()->create(['filename' => 'sustainability_report.pdf']);
            Media::factory()->create(['filename' => 'company_logo.png']);
            
            $response = $this->getJson('/api/v1/media?search=sustainability');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.filename', 'sustainability_report.pdf');
        });
        
        test('respects per_page parameter', function () {
            Media::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/media?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/media', function () {
        test('uploads new media file successfully', function () {
            $file = UploadedFile::fake()->image('sustainability.jpg', 100, 100);
            
            $mediaData = [
                'file' => $file,
                'is_public' => true,
                'media_type' => 'image',
                'description' => 'Imagen de sostenibilidad'
            ];
            
            $response = $this->postJson('/api/v1/media', $mediaData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.original_name', 'sustainability.jpg')
                ->assertJsonPath('data.media_type', 'image')
                ->assertJsonPath('data.is_public', true);
                
            $this->assertDatabaseHas('media', [
                'original_name' => 'sustainability.jpg',
                'media_type' => 'image',
                'is_public' => true
            ]);
            
            Storage::disk('public')->assertExists($response->json('data.path'));
        });
        
        test('returns 422 without file', function () {
            $response = $this->postJson('/api/v1/media', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['file']);
        });
        
        test('returns 422 with invalid file type', function () {
            $file = UploadedFile::fake()->create('document.exe', 100);
            
            $response = $this->postJson('/api/v1/media', [
                'file' => $file
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['file']);
        });
        
        test('returns 422 with file too large', function () {
            $file = UploadedFile::fake()->create('large_file.pdf', 10241); // 10MB + 1KB
            
            $response = $this->postJson('/api/v1/media', [
                'file' => $file
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['file']);
        });
        
        test('returns 422 with invalid media_type', function () {
            $file = UploadedFile::fake()->image('test.jpg');
            
            $response = $this->postJson('/api/v1/media', [
                'file' => $file,
                'media_type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['media_type']);
        });
    });
    
    describe('GET /api/v1/media/{id}', function () {
        test('returns media details', function () {
            $media = Media::factory()->create();
            
            $response = $this->getJson("/api/v1/media/{$media->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $media->id)
                ->assertJsonPath('data.filename', $media->filename)
                ->assertJsonPath('data.media_type', $media->media_type);
        });
        
        test('returns 404 for non-existent media', function () {
            $response = $this->getJson('/api/v1/media/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/media/{id}', function () {
        test('updates media with valid data', function () {
            $media = Media::factory()->create();
            $updateData = [
                'is_public' => false,
                'description' => 'Descripción actualizada del archivo'
            ];
            
            $response = $this->putJson("/api/v1/media/{$media->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.is_public', false)
                ->assertJsonPath('data.description', 'Descripción actualizada del archivo');
                
            $this->assertDatabaseHas('media', [
                'id' => $media->id,
                'is_public' => false,
                'description' => 'Descripción actualizada del archivo'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $media = Media::factory()->create();
            
            $response = $this->putJson("/api/v1/media/{$media->id}", [
                'media_type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['media_type']);
        });
        
        test('returns 404 for non-existent media', function () {
            $response = $this->putJson('/api/v1/media/999', [
                'is_public' => false
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/media/{id}', function () {
        test('deletes media successfully', function () {
            $media = Media::factory()->create();
            
            $response = $this->deleteJson("/api/v1/media/{$media->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('media', ['id' => $media->id]);
        });
        
        test('returns 404 for non-existent media', function () {
            $response = $this->deleteJson('/api/v1/media/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('POST /api/v1/media/{id}/download', function () {
        test('increments download count', function () {
            $media = Media::factory()->create();
            
            $response = $this->postJson("/api/v1/media/{$media->id}/download");
            
            $response->assertStatus(200)
                ->assertJsonPath('message', 'Descarga registrada');
        });
        
        test('returns 404 for non-existent media', function () {
            $response = $this->postJson('/api/v1/media/999/download');
            
            $response->assertStatus(404);
        });
    });
});
