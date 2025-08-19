<?php

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');
});

test('can list all topics', function () {
    Topic::factory()->count(5)->create(['is_active' => true, 'visibility' => 'public']);
    
    $response = $this->getJson('/api/v1/topics');
    
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [
                     '*' => [
                         'id',
                         'name',
                         'slug',
                         'description',
                         'category',
                         'members_count',
                         'posts_count',
                         'is_featured',
                         'created_at'
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

test('can create a new topic', function () {
    $topicData = [
        'name' => 'Instalaciones Solares Avanzadas',
        'description' => 'Discusión sobre técnicas avanzadas de instalación solar',
        'category' => 'technology',
        'allow_polls' => true,
        'allow_images' => true,
        'allow_links' => true,
    ];
    
    $response = $this->postJson('/api/v1/topics', $topicData);
    
    $response->assertStatus(201)
             ->assertJsonFragment([
                 'name' => 'Instalaciones Solares Avanzadas',
                 'category' => 'technology',
                 'members_count' => 1, // El creador se une automáticamente
             ]);
    
    $this->assertDatabaseHas('topics', [
        'name' => 'Instalaciones Solares Avanzadas',
        'slug' => 'instalaciones-solares-avanzadas',
        'creator_id' => $this->user->id,
    ]);
    
    $this->assertDatabaseHas('topic_memberships', [
        'user_id' => $this->user->id,
        'role' => 'admin',
    ]);
});

test('can join a topic', function () {
    $topic = Topic::factory()->create([
        'visibility' => 'public',
        'is_active' => true,
    ]);
    
    $response = $this->postJson("/api/v1/topics/{$topic->slug}/join");
    
    $response->assertStatus(200)
             ->assertJson(['message' => 'Te has unido al tema exitosamente']);
    
    $this->assertDatabaseHas('topic_memberships', [
        'topic_id' => $topic->id,
        'user_id' => $this->user->id,
        'role' => 'member',
    ]);
});

test('can leave a topic', function () {
    $topic = Topic::factory()->create([
        'visibility' => 'public',
        'is_active' => true,
    ]);
    
    // Primero unirse al tema
    $this->postJson("/api/v1/topics/{$topic->slug}/join");
    
    // Luego abandonar el tema
    $response = $this->postJson("/api/v1/topics/{$topic->slug}/leave");
    
    $response->assertStatus(200)
             ->assertJson(['message' => 'Has abandonado el tema exitosamente']);
    
    $this->assertDatabaseMissing('topic_memberships', [
        'topic_id' => $topic->id,
        'user_id' => $this->user->id,
    ]);
});

test('cannot leave own created topic', function () {
    $topic = Topic::factory()->create([
        'creator_id' => $this->user->id,
        'visibility' => 'public',
        'is_active' => true,
    ]);
    
    // Crear membresía como admin (simulando creación automática)
    $topic->members()->attach($this->user->id, ['role' => 'admin']);
    
    $response = $this->postJson("/api/v1/topics/{$topic->slug}/leave");
    
    $response->assertStatus(400)
             ->assertJson(['message' => 'No puedes abandonar un tema que creaste']);
});

test('can filter topics by category', function () {
    Topic::factory()->create(['category' => 'technology', 'visibility' => 'public', 'is_active' => true]);
    Topic::factory()->create(['category' => 'legislation', 'visibility' => 'public', 'is_active' => true]);
    Topic::factory()->create(['category' => 'technology', 'visibility' => 'public', 'is_active' => true]);
    
    $response = $this->getJson('/api/v1/topics?category=technology');
    
    $response->assertStatus(200);
    
    $topics = $response->json('data');
    expect(count($topics))->toBe(2);
    
    foreach ($topics as $topic) {
        expect($topic['category'])->toBe('technology');
    }
});

test('can get trending topics', function () {
    // Crear temas con diferentes activity scores
    $topicHigh = Topic::factory()->create([
        'activity_score' => 100,
        'visibility' => 'public',
        'is_active' => true,
        'name' => 'Tema Muy Activo'
    ]);
    
    $topicMedium = Topic::factory()->create([
        'activity_score' => 50,
        'visibility' => 'public', 
        'is_active' => true,
        'name' => 'Tema Moderadamente Activo'
    ]);
    
    $topicLow = Topic::factory()->create([
        'activity_score' => 10,
        'visibility' => 'public',
        'is_active' => true,
        'name' => 'Tema Poco Activo'
    ]);
    
    $response = $this->getJson('/api/v1/topics/trending');
    
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [
                     '*' => [
                         'id',
                         'name',
                         'activity_score',
                         'members_count',
                         'posts_count'
                     ]
                 ]
             ]);
    
    $topics = $response->json('data');
    expect(count($topics))->toBe(3);
    
    // Verificar que están ordenados por activity_score descendente
    expect($topics[0]['activity_score'])->toBeGreaterThanOrEqual($topics[1]['activity_score']);
    expect($topics[1]['activity_score'])->toBeGreaterThanOrEqual($topics[2]['activity_score']);
});

test('can get topic statistics', function () {
    $topic = Topic::factory()->create([
        'visibility' => 'public',
        'is_active' => true,
        'members_count' => 25,
        'posts_count' => 150,
        'comments_count' => 300,
        'activity_score' => 75.5,
    ]);
    
    $response = $this->getJson("/api/v1/topics/{$topic->slug}/stats");
    
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [
                     'members_count',
                     'posts_count', 
                     'comments_count',
                     'activity_score',
                     'posts_this_week',
                     'active_members_this_month',
                     'top_contributors'
                 ]
             ]);
});

test('validates topic creation data', function () {
    $response = $this->postJson('/api/v1/topics', [
        'name' => '', // Requerido
        'description' => '', // Requerido
        'category' => 'invalid_category', // Debe ser una categoría válida
    ]);
    
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name', 'description', 'category']);
});

test('cannot access private topic without membership', function () {
    $topic = Topic::factory()->create([
        'visibility' => 'private',
        'is_active' => true,
    ]);
    
    $response = $this->getJson("/api/v1/topics/{$topic->slug}");
    
    $response->assertStatus(403)
             ->assertJson(['message' => 'Acceso denegado']);
});
