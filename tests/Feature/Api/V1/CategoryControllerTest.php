<?php

use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('CategoryController', function () {
    
    describe('GET /api/v1/categories', function () {
        test('returns paginated list of categories', function () {
            Category::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/categories');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'parent_id',
                            'is_active',
                            'sort_order'
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
        
        test('filters categories by parent_id', function () {
            $parentCategory = Category::factory()->create();
            Category::factory()->create(['parent_id' => $parentCategory->id]);
            Category::factory()->create(['parent_id' => null]);
            
            $response = $this->getJson("/api/v1/categories?parent_id={$parentCategory->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.parent_id', $parentCategory->id);
        });
        
        test('filters categories by active status', function () {
            Category::factory()->create(['is_active' => true]);
            Category::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/categories?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches categories by name or description', function () {
            Category::factory()->create(['name' => 'Tecnología']);
            Category::factory()->create(['name' => 'Deportes']);
            
            $response = $this->getJson('/api/v1/categories?search=tecnologia');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Tecnología');
        });
        
        test('respects per_page parameter', function () {
            Category::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/categories?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/categories', function () {
        test('creates new category with valid data', function () {
            $categoryData = [
                'name' => 'Tecnología',
                'slug' => 'tecnologia',
                'description' => 'Categoría para contenido tecnológico',
                'is_active' => true,
                'sort_order' => 1
            ];
            
            $response = $this->postJson('/api/v1/categories', $categoryData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Tecnología')
                ->assertJsonPath('data.slug', 'tecnologia')
                ->assertJsonPath('data.is_active', true);
                
            $this->assertDatabaseHas('categories', [
                'name' => 'Tecnología',
                'slug' => 'tecnologia',
                'is_active' => true
            ]);
        });
        
        test('creates subcategory with valid parent_id', function () {
            $parentCategory = Category::factory()->create();
            
            $categoryData = [
                'name' => 'Desarrollo Web',
                'slug' => 'desarrollo-web',
                'parent_id' => $parentCategory->id,
                'description' => 'Subcategoría de desarrollo web',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/categories', $categoryData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.parent_id', $parentCategory->id);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/categories', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug']);
        });
        
        test('returns 422 with invalid parent_id', function () {
            $categoryData = [
                'name' => 'Test Category',
                'slug' => 'test-category',
                'parent_id' => 999
            ];
            
            $response = $this->postJson('/api/v1/categories', $categoryData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['parent_id']);
        });
        
        test('returns 422 with duplicate slug', function () {
            Category::factory()->create(['slug' => 'test-category']);
            
            $categoryData = [
                'name' => 'Another Category',
                'slug' => 'test-category'
            ];
            
            $response = $this->postJson('/api/v1/categories', $categoryData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
        });
    });
    
    describe('GET /api/v1/categories/{id}', function () {
        test('returns category details', function () {
            $category = Category::factory()->create();
            
            $response = $this->getJson("/api/v1/categories/{$category->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $category->id)
                ->assertJsonPath('data.name', $category->name)
                ->assertJsonPath('data.slug', $category->slug);
        });
        
        test('returns 404 for non-existent category', function () {
            $response = $this->getJson('/api/v1/categories/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/categories/{id}', function () {
        test('updates category with valid data', function () {
            $category = Category::factory()->create();
            $updateData = [
                'name' => 'Tecnología Actualizada',
                'description' => 'Descripción actualizada'
            ];
            
            $response = $this->putJson("/api/v1/categories/{$category->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Tecnología Actualizada')
                ->assertJsonPath('data.description', 'Descripción actualizada');
                
            $this->assertDatabaseHas('categories', [
                'id' => $category->id,
                'name' => 'Tecnología Actualizada',
                'description' => 'Descripción actualizada'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $category = Category::factory()->create();
            
            $response = $this->putJson("/api/v1/categories/{$category->id}", [
                'parent_id' => 999
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['parent_id']);
        });
        
        test('returns 404 for non-existent category', function () {
            $response = $this->putJson('/api/v1/categories/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/categories/{id}', function () {
        test('deletes category successfully', function () {
            $category = Category::factory()->create();
            
            $response = $this->deleteJson("/api/v1/categories/{$category->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        });
        
        test('returns 422 when category has subcategories', function () {
            $parentCategory = Category::factory()->create();
            Category::factory()->create(['parent_id' => $parentCategory->id]);
            
            $response = $this->deleteJson("/api/v1/categories/{$parentCategory->id}");
            
            $response->assertStatus(422)
                ->assertJsonPath('message', 'No se puede eliminar la categoría porque tiene subcategorías');
        });
        
        test('returns 404 for non-existent category', function () {
            $response = $this->deleteJson('/api/v1/categories/999');
            
            $response->assertStatus(404);
        });
    });
});
