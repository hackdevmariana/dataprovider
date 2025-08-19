<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\ExpertVerification;
use Laravel\Sanctum\Sanctum;

class ExpertVerificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function authenticated_user_can_submit_verification_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $verificationData = [
            'expertise_area' => 'solar',
            'verification_level' => 'basic',
            'years_experience' => 5,
            'expertise_description' => 'Tengo 5 años de experiencia trabajando con instalaciones solares fotovoltaicas y he participado en más de 50 proyectos de diferentes escalas.',
            'credentials' => [
                'certification' => 'Certificado en Energía Solar Fotovoltaica',
                'institution' => 'Instituto de Energías Renovables',
            ],
        ];

        $response = $this->postJson('/api/v1/expert-verifications', $verificationData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'expertise_area',
                    'verification_level',
                    'status',
                    'submitted_at',
                ],
                'message'
            ]);

        $this->assertDatabaseHas('expert_verifications', [
            'user_id' => $user->id,
            'expertise_area' => 'solar',
            'verification_level' => 'basic',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_validates_verification_request_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/expert-verifications', [
            'expertise_area' => 'invalid_area',
            'verification_level' => '',
            'years_experience' => -1,
            'expertise_description' => 'Too short', // Less than 50 characters
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'expertise_area',
                'verification_level',
                'years_experience',
                'expertise_description'
            ]);
    }

    /** @test */
    public function user_cannot_submit_duplicate_pending_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Crear una verificación pendiente existente
        ExpertVerification::factory()->create([
            'user_id' => $user->id,
            'expertise_area' => 'solar',
            'status' => 'pending',
        ]);

        $verificationData = [
            'expertise_area' => 'solar',
            'verification_level' => 'basic',
            'years_experience' => 5,
            'expertise_description' => 'Tengo experiencia en energía solar y quiero obtener verificación como experto en esta área.',
        ];

        $response = $this->postJson('/api/v1/expert-verifications', $verificationData);

        $response->assertStatus(409)
            ->assertJson(['message' => 'Ya tienes una solicitud pendiente para esta área de expertise']);
    }

    /** @test */
    public function it_can_list_expert_verifications()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        ExpertVerification::factory()->create([
            'user_id' => $user1->id,
            'expertise_area' => 'solar',
            'status' => 'pending',
        ]);

        ExpertVerification::factory()->create([
            'user_id' => $user2->id,
            'expertise_area' => 'wind',
            'status' => 'approved',
        ]);

        $response = $this->getJson('/api/v1/expert-verifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user',
                        'expertise_area',
                        'verification_level',
                        'status',
                        'submitted_at',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_filter_verifications_by_status()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        ExpertVerification::factory()->create([
            'user_id' => $user1->id,
            'status' => 'pending',
        ]);

        ExpertVerification::factory()->create([
            'user_id' => $user2->id,
            'status' => 'approved',
        ]);

        $response = $this->getJson('/api/v1/expert-verifications?status=pending');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals('pending', $response->json('data.0.status'));
    }

    /** @test */
    public function user_can_view_their_own_verification()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $verification = ExpertVerification::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson("/api/v1/expert-verifications/{$verification->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user',
                    'expertise_area',
                    'verification_level',
                    'status',
                    'expertise_description',
                ]
            ]);
    }

    /** @test */
    public function user_cannot_view_other_users_verification()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $verification = ExpertVerification::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->getJson("/api/v1/expert-verifications/{$verification->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_verification_statistics()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        ExpertVerification::factory()->create([
            'user_id' => $user1->id,
            'expertise_area' => 'solar',
            'status' => 'pending',
        ]);

        ExpertVerification::factory()->create([
            'user_id' => $user2->id,
            'expertise_area' => 'wind',
            'status' => 'approved',
        ]);

        $response = $this->getJson('/api/v1/expert-verifications/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_requests',
                    'by_status',
                    'by_expertise_area',
                    'by_verification_level',
                    'approval_rate',
                ]
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_submit_verification()
    {
        $verificationData = [
            'expertise_area' => 'solar',
            'verification_level' => 'basic',
            'years_experience' => 5,
            'expertise_description' => 'Tengo experiencia en energía solar.',
        ];

        $response = $this->postJson('/api/v1/expert-verifications', $verificationData);

        $response->assertStatus(401);
    }
}