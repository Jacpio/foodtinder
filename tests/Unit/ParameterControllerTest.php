<?php

use App\Models\Parameter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class, RefreshDatabase::class);

function actingAsAdmin(): User
{
    $user = User::factory()->create();

    $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
    $user->assignRole($admin);

    Sanctum::actingAs($user, ['*']);

    return $user;
}

beforeEach(function () {
    actingAsAdmin();
});

test('index returns paginated list', function () {
    $types = ['category', 'cuisine', 'flavour', 'other'];
    for ($i = 1; $i <= 12; $i++) {
        Parameter::create([
            'name'      => "Param {$i}",
            'type'      => $types[$i % 4],
            'value'     => 1,
            'is_active' => true,
        ]);
    }

    $response = $this->get('/api/parameter?per_page=5');

    $response->assertOk()
        ->assertJsonStructure([
            'current_page',
            'data',
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);

    expect(count($response->json('data')))->toBe(5)
        ->and($response->json('per_page'))->toBe(5);
});

test('index validates per_page upper bound', function () {
    $this->get('/api/parameter?per_page=50')->assertStatus(302);
});

test('show returns single parameter', function () {
    $p = Parameter::create([
        'name' => 'Włoska',
        'type' => 'cuisine',
        'value' => 1,
        'is_active' => true,
    ]);

    $this->get("/api/parameter/{$p->id}")
        ->assertOk()
        ->assertJson([
            'id' => $p->id,
            'name' => 'Włoska',
            'type' => 'cuisine',
        ]);
});

test('show 404 when not found', function () {
    $this->get('/api/parameter/999999')->assertStatus(404);
});

test('store creates parameter (201)', function () {
    $payload = [
        'name'      => 'Zupy',
        'type'      => 'category',
        'value'     => 1.0,
        'is_active' => true,
    ];

    $res = $this->postJson('/api/parameter', $payload);
    $res->assertStatus(201)
        ->assertJsonFragment([
            'name' => 'Zupy',
            'type' => 'category',
        ]);

    $this->assertDatabaseHas('parameters', ['name' => 'Zupy', 'type' => 'category']);
});

test('store fails on duplicate name (422)', function () {
    Parameter::create(['name' => 'Zupy', 'type' => 'category', 'value' => 1, 'is_active' => true]);

    $this->postJson('/api/parameter', [
        'name' => 'Zupy',
        'type' => 'category',
        'value' => 1,
        'is_active' => true,
    ])->assertStatus(422);
});

test('store fails on invalid type (422)', function () {
    $this->postJson('/api/parameter', [
        'name' => 'Coś',
        'type' => 'invalid-type',
    ])->assertStatus(422);
});

test('update modifies parameter (200)', function () {
    $p = Parameter::create([
        'name' => 'Polska',
        'type' => 'cuisine',
        'value' => 1,
        'is_active' => true,
    ]);

    $res = $this->putJson("/api/parameter/{$p->id}", [
        'name' => 'Polska kuchnia',
        'value' => 0.5,
        'is_active' => false,
    ]);

    $res->assertOk()
        ->assertJsonFragment([
            'name' => 'Polska kuchnia',
            'value' => 0.5,
            'is_active' => false,
        ]);

    $this->assertDatabaseHas('parameters', [
        'id' => $p->id,
        'name' => 'Polska kuchnia',
        'value' => 0.5,
        'is_active' => 0,
    ]);
});

test('update enforces unique name (422)', function () {
    $a = Parameter::create(['name' => 'A', 'type' => 'category', 'value' => 1, 'is_active' => true]);
    $b = Parameter::create(['name' => 'B', 'type' => 'category', 'value' => 1, 'is_active' => true]);

    $this->putJson("/api/parameter/{$b->id}", [
        'name' => 'A',
    ])->assertStatus(422);
});

test('update 404 when not found', function () {
    $this->putJson('/api/parameter/999999', ['name' => 'X'])->assertStatus(404);
});

test('destroy deletes parameter (200)', function () {
    $p = Parameter::create([
        'name' => 'Do usunięcia',
        'type' => 'other',
        'value' => 1,
        'is_active' => true,
    ]);

    $this->delete("/api/parameter/{$p->id}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Success']);

    $this->assertDatabaseMissing('parameters', ['id' => $p->id]);
});

test('destroy 404 when not found', function () {
    $this->delete('/api/parameter/999999')->assertStatus(404);
});

test('store returns 403 for non-admin user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $this->postJson('/api/parameter', [
        'name' => 'Nie powinno przejść',
        'type' => 'category',
    ])->assertStatus(403);
});
