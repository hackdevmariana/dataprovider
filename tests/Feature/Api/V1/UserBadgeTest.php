<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserBadge;
use Laravel\Sanctum\Sanctum;

class UserBadgeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_list_user_badges()
    {
        // Crear usuarios y badges
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        UserBadge::factory()->create([
            'user_id' => $user1->id,
            'badge_type' => 'gold',
            'category' => 'energy_saver',
            'is_public' => true,
        ]);
        
        UserBadge::factory()->create([
            'user_id' => $user2->id,
            'badge_type' => 'silver',
            'category' => 'community_leader',
            'is_public' => true,
        ]);

        $response = $this->getJson('/api/v1/user-badges');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user',
                        'name',
                        'badge_type',
                        'category',
                        'is_public',
                        'earned_at',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_filter_badges_by_type()
    {
        $user = User::factory()->create();
        
        UserBadge::factory()->create([
            'user_id' => $user->id,
            'badge_type' => 'gold',
            'is_public' => true,
        ]);
        
        UserBadge::factory()->create([
            'user_id' => $user->id,
            'badge_type' => 'silver',
            'is_public' => true,
        ]);

        $response = $this->getJson('/api/v1/user-badges?badge_type=gold');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals('gold', $response->json('data.0.badge_type'));
    }

    /** @test */
    public function authenticated_user_can_view_their_badges()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        UserBadge::factory()->create([
            'user_id' => $user->id,
            'badge_type' => 'gold',
            'category' => 'energy_saver',
        ]);
        
        UserBadge::factory()->create([
            'user_id' => $user->id,
            'badge_type' => 'silver',
            'category' => 'community_leader',
        ]);

        $response = $this->getJson('/api/v1/user-badges/my-badges');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'badge_type',
                        'category',
                        'points_awarded',
                    ]
                ],
                'stats' => [
                    'total_badges',
                    'total_points',
                    'by_type',
                    'by_category',
                ]
            ]);
        
        $this->assertEquals(2, $response->json('stats.total_badges'));
    }

    /** @test */
    public function it_can_show_specific_badge()
    {
        $user = User::factory()->create();
        
        $badge = UserBadge::factory()->create([
            'user_id' => $user->id,
            'badge_type' => 'gold',
            'is_public' => true,
        ]);

        $response = $this->getJson("/api/v1/user-badges/{$badge->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user',
                    'name',
                    'badge_type',
                    'category',
                    'earned_at',
                ]
            ]);
    }

    /** @test */
    public function it_cannot_show_private_badge_to_unauthorized_user()
    {
        $user = User::factory()->create();
        
        $badge = UserBadge::factory()->create([
            'user_id' => $user->id,
            'is_public' => false,
        ]);

        $response = $this->getJson("/api/v1/user-badges/{$badge->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_create_badge()
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();
        Sanctum::actingAs($user);

        $badgeData = [
            'user_id' => $targetUser->id,
            'name' => 'Ahorrador de Oro',
            'description' => 'Ha ahorrado más de 1000 kWh este año',
            'badge_type' => 'gold',
            'category' => 'energy_saver',
            'points_awarded' => 500,
            'is_public' => true,
            'is_featured' => true,
        ];

        $response = $this->postJson('/api/v1/user-badges', $badgeData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'badge_type',
                    'category',
                    'points_awarded',
                ],
                'message'
            ]);

        $this->assertDatabaseHas('user_badges', [
            'user_id' => $targetUser->id,
            'name' => 'Ahorrador de Oro',
            'badge_type' => 'gold',
        ]);
    }

    /** @test */
    public function it_validates_badge_creation_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user-badges', [
            'name' => '', // Required field empty
            'badge_type' => 'invalid_type', // Invalid enum value
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'badge_type', 'user_id', 'description']);
    }

    /** @test */
    public function it_can_get_badge_statistics()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        UserBadge::factory()->create([
            'user_id' => $user1->id,
            'badge_type' => 'gold',
            'category' => 'energy_saver',
        ]);
        
        UserBadge::factory()->create([
            'user_id' => $user2->id,
            'badge_type' => 'silver',
            'category' => 'community_leader',
        ]);

        $response = $this->getJson('/api/v1/user-badges/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_badges',
                    'total_users_with_badges',
                    'by_type',
                    'by_category',
                ]
            ]);
    }

    /** @test */
    public function user_can_delete_their_own_badge()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $badge = UserBadge::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/v1/user-badges/{$badge->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Insignia eliminada exitosamente']);

        $this->assertDatabaseMissing('user_badges', ['id' => $badge->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_badge()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);
        
        $badge = UserBadge::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->deleteJson("/api/v1/user-badges/{$badge->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('user_badges', ['id' => $badge->id]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_my_badges()
    {
        $response = $this->getJson('/api/v1/user-badges/my-badges');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Usuario no autenticado']);
    }
}