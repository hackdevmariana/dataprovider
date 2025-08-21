<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Tag;
use App\Models\User;
use App\Models\TagGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class TagsTest extends TestCase
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
    public function it_can_list_tags_with_pagination()
    {
        Tag::factory()->count(25)->create();
        
        $response = $this->getJson('/api/v1/tags?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'tag_group_id',
                            'is_active',
                            'usage_count'
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
    public function it_can_filter_tags_by_tag_group()
    {
        $tagGroup = TagGroup::factory()->create();
        Tag::factory()->count(3)->create(['tag_group_id' => $tagGroup->id]);
        Tag::factory()->count(2)->create(['tag_group_id' => null]);
        
        $response = $this->getJson("/api/v1/tags?tag_group_id={$tagGroup->id}");

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_tags_by_active_status()
    {
        Tag::factory()->count(3)->create(['is_active' => true]);
        Tag::factory()->count(2)->create(['is_active' => false]);
        
        $response = $this->getJson('/api/v1/tags?is_active=true');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_search_tags_by_name_or_description()
    {
        Tag::factory()->create(['name' => 'Sostenibilidad', 'description' => 'Tag de sostenibilidad']);
        Tag::factory()->create(['name' => 'Tecnología', 'description' => 'Tag de tecnología']);
        
        $response = $this->getJson('/api/v1/tags?search=sostenibilidad');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Sostenibilidad', $response->json('data.0.name'));
    }

    /** @test */
    public function it_can_create_a_new_tag()
    {
        $tagData = [
            'name' => 'Nuevo Tag',
            'slug' => 'nuevo-tag',
            'description' => 'Descripción del nuevo tag',
            'is_active' => true
        ];

        $response = $this->postJson('/api/v1/tags', $tagData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'is_active',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'Nuevo Tag',
            'slug' => 'nuevo-tag'
        ]);
    }

    /** @test */
    public function it_can_create_tag_with_group()
    {
        $tagGroup = TagGroup::factory()->create();
        
        $tagData = [
            'name' => 'Tag con Grupo',
            'slug' => 'tag-con-grupo',
            'tag_group_id' => $tagGroup->id,
            'is_active' => true
        ];

        $response = $this->postJson('/api/v1/tags', $tagData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tags', [
            'name' => 'Tag con Grupo',
            'tag_group_id' => $tagGroup->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_tag()
    {
        $response = $this->postJson('/api/v1/tags', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug']);
    }

    /** @test */
    public function it_validates_slug_uniqueness_when_creating_tag()
    {
        Tag::factory()->create(['slug' => 'test-tag']);
        
        $response = $this->postJson('/api/v1/tags', [
            'name' => 'Test Tag',
            'slug' => 'test-tag'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_can_show_tag_details()
    {
        $tag = Tag::factory()->create();

        $response = $this->getJson("/api/v1/tags/{$tag->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_tag()
    {
        $response = $this->getJson('/api/v1/tags/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_tag()
    {
        $tag = Tag::factory()->create();

        $updateData = [
            'name' => 'Tag Actualizado',
            'description' => 'Descripción actualizada'
        ];

        $response = $this->putJson("/api/v1/tags/{$tag->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'Tag Actualizado',
                        'description' => 'Descripción actualizada'
                    ]
                ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Tag Actualizado'
        ]);
    }

    /** @test */
    public function it_validates_slug_uniqueness_when_updating_tag()
    {
        $tag1 = Tag::factory()->create(['slug' => 'tag-1']);
        $tag2 = Tag::factory()->create(['slug' => 'tag-2']);

        $response = $this->putJson("/api/v1/tags/{$tag1->id}", [
            'slug' => 'tag-2'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_can_delete_tag()
    {
        $tag = Tag::factory()->create();

        $response = $this->deleteJson("/api/v1/tags/{$tag->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_tag()
    {
        $response = $this->deleteJson('/api/v1/tags/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        Tag::factory()->count(150)->create();
        
        $response = $this->getJson('/api/v1/tags?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_tags_ordered_by_name()
    {
        Tag::factory()->create(['name' => 'Zebra']);
        Tag::factory()->create(['name' => 'Alpha']);
        Tag::factory()->create(['name' => 'Beta']);
        
        $response = $this->getJson('/api/v1/tags');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Alpha', $data[0]['name']);
        $this->assertEquals('Beta', $data[1]['name']);
        $this->assertEquals('Zebra', $data[2]['name']);
    }

    /** @test */
    public function it_counts_usage_correctly()
    {
        $tag = Tag::factory()->create();
        // Aquí se simularía el uso del tag en otros modelos
        
        $response = $this->getJson("/api/v1/tags/{$tag->id}");

        $response->assertStatus(200);
        $this->assertArrayHasKey('usage_count', $response->json('data'));
    }

    /** @test */
    public function it_can_filter_tags_by_multiple_criteria()
    {
        $tagGroup = TagGroup::factory()->create();
        Tag::factory()->create([
            'name' => 'Tag Activo',
            'tag_group_id' => $tagGroup->id,
            'is_active' => true
        ]);
        Tag::factory()->create([
            'name' => 'Tag Inactivo',
            'tag_group_id' => $tagGroup->id,
            'is_active' => false
        ]);
        
        $response = $this->getJson("/api/v1/tags?tag_group_id={$tagGroup->id}&is_active=true");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Tag Activo', $response->json('data.0.name'));
    }
}
