<?php

use App\Models\Dish;
use App\Models\Parameter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    config(['auth.defaults.guard' => 'api']);

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    Sanctum::actingAs($this->admin, ['*']);
});

function makeParameters(int $count = 3): Collection
{
    $types = ['category','cuisine','flavour','other'];

    return collect(range(1, $count))->map(function ($i) use ($types) {
        return Parameter::factory()->create([
            'type' => $types[($i - 1) % count($types)],
        ]);
    });
}

it('GET /api/dish returns paginated list with parameters', function () {
    $params = makeParameters(4);

    Dish::factory()->count(3)->create()->each(function (Dish $d) use ($params) {
        $d->parameters()->sync($params->random(2)->pluck('id')->all());
    });

    $res = $this->getJson('/api/dish?per_page=2');

    $res->assertOk()
        ->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'description',
                    'is_vegan',
                    'image_url',
                    'parameters' => [
                        ['id','name','type','value','is_active'],
                    ],
                ],
            ],
            'current_page',
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

    expect((int) $res->json('per_page'))->toBe(2);
});

it('GET /api/dish/{id} returns a dish with parameters', function () {
    $p = makeParameters(3);
    $dish = Dish::factory()->create();
    $dish->parameters()->sync($p->pluck('id')->all());

    $res = $this->getJson("/api/dish/{$dish->id}");

    $res->assertOk()
        ->assertJsonPath('id', $dish->id)
        ->assertJsonCount(3, 'parameters');
});

it('GET /api/dish/{id} returns 404 for non-existing dish', function () {
    $res = $this->getJson('/api/dish/999999');

    $res->assertStatus(404)
        ->assertJson(['message' => 'Not found']);
});

it('POST /api/dish (JSON) creates a dish and attaches parameters', function () {
    $p = makeParameters(3);

    $payload = [
        'name' => 'Pizza Margherita',
        'description' => 'Classic',
        'is_vegan' => true,
        'parameter_ids' => $p->pluck('id')->all(),
    ];

    $res = $this->postJson('/api/dish', $payload);

    $res->assertCreated()
        ->assertJsonPath('name', 'Pizza Margherita')
        ->assertJsonPath('is_vegan', true)
        ->assertJsonCount(3, 'parameters');

    $this->assertDatabaseHas('dishes', ['name' => 'Pizza Margherita']);

    $dishId = $res->json('id');

    foreach ($p as $param) {
        $this->assertDatabaseHas('dish_parameters', [
            'dish_id' => $dishId,
            'parameter_id' => $param->id,
        ]);
    }
});

it('POST /api/dish (multipart) uploads and stores an image', function () {
    Storage::fake('public');

    $p = makeParameters(2);

    $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

    $payload = [
        'name' => 'Cucumber Soup',
        'description' => 'Classic',
        'parameter_ids' => $p->pluck('id')->all(),
        'image' => $file,
    ];

    $res = $this->post('/api/dish', $payload, ['Accept' => 'application/json']);

    $res->assertCreated();

    $path = $res->json('image_url');
    expect($path)->not->toBeNull();

    Storage::disk('public')->assertExists($path);
});

it('PUT /api/dish/{id} updates fields and syncs parameters', function () {
    $dish = Dish::factory()->create([
        'name' => 'Old name',
        'description' => 'Old',
    ]);

    $oldParams = makeParameters(2);
    $dish->parameters()->sync($oldParams->pluck('id')->all());

    $newParams = makeParameters(2);

    $res = $this->putJson("/api/dish/{$dish->id}", [
        'name' => 'New name',
        'description' => 'New desc',
        'parameter_ids' => $newParams->pluck('id')->all(),
    ]);

    $res->assertOk()
        ->assertJsonPath('name', 'New name')
        ->assertJsonPath('description', 'New desc')
        ->assertJsonCount(2, 'parameters');

    foreach ($newParams as $param) {
        $this->assertDatabaseHas('dish_parameters', [
            'dish_id' => $dish->id,
            'parameter_id' => $param->id,
        ]);
    }

    foreach ($oldParams as $param) {
        $this->assertDatabaseMissing('dish_parameters', [
            'dish_id' => $dish->id,
            'parameter_id' => $param->id,
        ]);
    }
});

it('PUT /api/dish/{id} replaces image and supports remove_image', function () {
    Storage::fake('public');

    $initial = UploadedFile::fake()->image('old.jpg');

    $create = $this->post('/api/dish', [
        'name' => 'With image',
        'image' => $initial,
    ], ['Accept' => 'application/json'])->assertCreated();

    $dishId = $create->json('id');
    $oldPath = $create->json('image_url');
    Storage::disk('public')->assertExists($oldPath);

    $newFile = UploadedFile::fake()->image('new.jpg');

    $update = $this->put("/api/dish/{$dishId}", [
        'image' => $newFile,
    ], ['Accept' => 'application/json'])->assertOk();

    $newPath = $update->json('image_url');
    expect($newPath)->not->toEqual($oldPath);
    Storage::disk('public')->assertExists($newPath);
    Storage::disk('public')->assertMissing($oldPath);


    $update2 = $this->put("/api/dish/{$dishId}", [
        'remove_image' => true,
    ], ['Accept' => 'application/json'])->assertOk();

    expect($update2->json('image_url'))->toBeNull();
});

it('DELETE /api/dish/{id} deletes dish and its file', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('del.jpg');

    $create = $this->post('/api/dish', [
        'name' => 'To delete',
        'image' => $file,
    ], ['Accept' => 'application/json'])->assertCreated();

    $dishId = $create->json('id');
    $path = $create->json('image_url');
    Storage::disk('public')->assertExists($path);

    $this->deleteJson("/api/dish/{$dishId}")
        ->assertOk()
        ->assertJson(['message' => 'Success']);

    $this->assertDatabaseMissing('dishes', ['id' => $dishId]);
    Storage::disk('public')->assertMissing($path);
});

it('DELETE /api/dish/{id} returns 422 for invalid id', function () {
    $this->deleteJson('/api/dish/999999')
        ->assertStatus(422)
        ->assertJson(['message' => 'The given data was invalid.']);
});
