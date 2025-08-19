<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserSubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test listar suscripciones del usuario
     */
    public function test_can_list_user_subscriptions()
    {
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create([
            'name' => 'Plan Test',
            'price' => 29.99,
        ]);

        // Crear suscripción de prueba
        UserSubscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/user-subscriptions');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'subscription_plan',
                             'status',
                             'amount_paid',
                             'billing_cycle',
                             'starts_at',
                             'ends_at',
                         ]
                     ],
                     'meta' => [
                         'total',
                         'active_subscriptions',
                         'total_spent',
                         'current_plan',
                     ]
                 ]);
    }

    /**
     * Test crear nueva suscripción
     */
    public function test_can_create_subscription()
    {
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create([
            'name' => 'Premium Plan',
            'price' => 49.99,
            'billing_cycle' => 'monthly',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user-subscriptions', [
            'subscription_plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'payment_method' => 'card',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'subscription_plan',
                         'status',
                         'amount_paid',
                     ],
                     'message'
                 ]);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount_paid' => 49.99,
        ]);
    }

    /**
     * Test no permitir múltiples suscripciones activas
     */
    public function test_cannot_create_multiple_active_subscriptions()
    {
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();

        // Crear suscripción activa existente
        UserSubscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user-subscriptions', [
            'subscription_plan_id' => $plan->id,
        ]);

        $response->assertStatus(409)
                 ->assertJson([
                     'message' => 'Ya tienes una suscripción activa'
                 ]);
    }

    /**
     * Test cancelar suscripción
     */
    public function test_can_cancel_subscription()
    {
        $user = User::factory()->create();
        $subscription = UserSubscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/user-subscriptions/{$subscription->id}", [
            'reason' => 'No longer needed',
            'cancel_immediately' => false,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'status',
                     ],
                     'message'
                 ]);

        $subscription->refresh();
        $this->assertEquals('cancelled', $subscription->status);
        $this->assertEquals('No longer needed', $subscription->cancellation_reason);
    }

    /**
     * Test reactivar suscripción
     */
    public function test_can_reactivate_cancelled_subscription()
    {
        $user = User::factory()->create();
        $subscription = UserSubscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'cancelled',
            'cancelled_at' => now()->subDay(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/user-subscriptions/{$subscription->id}/reactivate");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'status',
                     ],
                     'message'
                 ]);

        $subscription->refresh();
        $this->assertEquals('active', $subscription->status);
        $this->assertNull($subscription->cancelled_at);
    }

    /**
     * Test filtrar suscripciones por estado
     */
    public function test_can_filter_subscriptions_by_status()
    {
        $user = User::factory()->create();
        
        // Crear suscripciones con diferentes estados
        UserSubscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);
        
        UserSubscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'cancelled',
        ]);

        Sanctum::actingAs($user);

        // Filtrar solo activas
        $response = $this->getJson('/api/v1/user-subscriptions?status=active');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('active', $data[0]['status']);
    }

    /**
     * Test acceso sin autenticación
     */
    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/v1/user-subscriptions');

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Usuario no autenticado'
                 ]);
    }

    /**
     * Test no puede acceder a suscripciones de otros usuarios
     */
    public function test_cannot_access_other_user_subscriptions()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $subscription = UserSubscription::factory()->create([
            'user_id' => $user2->id,
        ]);

        Sanctum::actingAs($user1);

        $response = $this->getJson("/api/v1/user-subscriptions/{$subscription->id}");

        $response->assertStatus(403)
                 ->assertJson([
                     'message' => 'Sin permisos para ver esta suscripción'
                 ]);
    }

    /**
     * Test validación de datos al crear suscripción
     */
    public function test_validates_subscription_creation_data()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user-subscriptions', [
            'subscription_plan_id' => 999, // Plan inexistente
            'billing_cycle' => 'invalid_cycle',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'subscription_plan_id',
                     'billing_cycle'
                 ]);
    }
}