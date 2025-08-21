<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class CategoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario autenticado
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_list_categories_with_pagination()
    {
        Category::factory()->count(25)->create();
        
        $response = $this->getJson('/api/v1/categories?page=1&per_page=10');

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
                            'sort_order',
                            'children_count'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);

        $this->assertCount(10, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_categories_by_parent_id()
    {
        $parentCategory = Category::factory()->create();
        Category::factory()->count(3)->create(['parent_id' => $parentCategory->id]);
        Category::factory()->count(2)->create(['parent_id' => null]);
        
        $response = $this->getJson("/api/v1/categories?parent_id={$parentCategory->id}");

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_categories_by_active_status()
    {
        Category::factory()->count(3)->create(['is_active' => true]);
        Category::factory()->count(2)->create(['is_active' => false]);
        
        $response = $this->getJson('/api/v1/categories?is_active=true');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_search_categories_by_name_or_description()
    {
        Category::factory()->create(['name' => 'Tecnología', 'description' => 'Categoría de tecnología']);
        Category::factory()->create(['name' => 'Deportes', 'description' => 'Categoría de deportes']);
        
        $response = $this->getJson('/api/v1/categories?search=tecnologia');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Tecnología', $response->json('data.0.name'));
    }

    /** @test */
    public function it_can_create_a_new_category()
    {
        $categoryData = [
            'name' => 'Nueva Categoría',
            'slug' => 'nueva-categoria',
            'description' => 'Descripción de la nueva categoría',
            'is_active' => true,
            'sort_order' => 1
        ];

        $response = $this->postJson('/api/v1/categories', $categoryData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'is_active',
                        'sort_order',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Nueva Categoría',
            'slug' => 'nueva-categoria'
        ]);
    }

    /** @test */
    public function it_can_create_category_with_parent()
    {
        $parentCategory = Category::factory()->create();
        
        $categoryData = [
            'name' => 'Subcategoría',
            'slug' => 'subcategoria',
            'parent_id' => $parentCategory->id,
            'is_active' => true
        ];

        $response = $this->postJson('/api/v1/categories', $categoryData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', [
            'name' => 'Subcategoría',
            'parent_id' => $parentCategory->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_category()
    {
        $response = $this->postJson('/api/v1/categories', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug']);
    }

    /** @test */
    public function it_validates_slug_uniqueness_when_creating_category()
    {
        Category::factory()->create(['slug' => 'test-category']);
        
        $response = $this->postJson('/api/v1/categories', [
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_can_show_category_details()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_category()
    {
        $response = $this->getJson('/api/v1/categories/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_category()
    {
        $category = Category::factory()->create();

        $updateData = [
            'name' => 'Categoría Actualizada',
            'description' => 'Descripción actualizada'
        ];

        $response = $this->putJson("/api/v1/categories/{$category->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'Categoría Actualizada',
                        'description' => 'Descripción actualizada'
                    ]
                ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Categoría Actualizada'
        ]);
    }

    /** @test */
    public function it_validates_slug_uniqueness_when_updating_category()
    {
        $category1 = Category::factory()->create(['slug' => 'category-1']);
        $category2 = Category::factory()->create(['slug' => 'category-2']);

        $response = $this->putJson("/api/v1/categories/{$category1->id}", [
            'slug' => 'category-2'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_can_delete_category_without_children()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function it_cannot_delete_category_with_children()
    {
        $parentCategory = Category::factory()->create();
        Category::factory()->create(['parent_id' => $parentCategory->id]);

        $response = $this->deleteJson("/api/v1/categories/{$parentCategory->id}");

        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'No se puede eliminar la categoría porque tiene subcategorías'
                ]);

        $this->assertDatabaseHas('categories', ['id' => $parentCategory->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_category()
    {
        $response = $this->deleteJson('/api/v1/categories/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        Category::factory()->count(150)->create();
        
        $response = $this->getJson('/api/v1/categories?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_categories_ordered_by_sort_order()
    {
        Category::factory()->create(['sort_order' => 3]);
        Category::factory()->create(['sort_order' => 1]);
        Category::factory()->create(['sort_order' => 2]);
        
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(1, $data[0]['sort_order']);
        $this->assertEquals(2, $data[1]['sort_order']);
        $this->assertEquals(3, $data[2]['sort_order']);
    }

    /** @test */
    public function it_counts_children_correctly()
    {
        $parentCategory = Category::factory()->create();
        Category::factory()->count(3)->create(['parent_id' => $parentCategory->id]);
        
        $response = $this->getJson("/api/v1/categories/{$parentCategory->id}");

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('data.children_count'));
    }
}
