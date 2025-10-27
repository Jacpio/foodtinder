<?php

use App\Models\Dish;
use App\Models\Parameter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function makeParams(int $count = 3): Collection
{
    $types = ['category','cuisine','flavour','other'];

    return collect(range(1, $count))->map(function ($i) use ($types) {
        return Parameter::factory()->create([
            'type' => $types[($i - 1) % count($types)],
        ]);
    });
}

it('GET /api/share/recommendations returns dishes (with parameters) for provided ids', function () {
    $params = makeParams(4);

    $d1 = Dish::factory()->create(['name' => 'Share Dish 1']);
    $d2 = Dish::factory()->create(['name' => 'Share Dish 2']);

    $d1->parameters()->sync($params->take(2)->pluck('id')->all());
    $d2->parameters()->sync($params->slice(2, 2)->pluck('id')->all());

    $ids = implode(',', [$d1->id, $d2->id]);

    $res = $this->getJson("/api/share/recommendations?ids={$ids}");

    $res->assertOk()
        ->assertJsonCount(2)
        ->assertJsonStructure([
            [
                'id', 'name', 'description', 'image_url',
                'parameters' => [
                    ['id', 'name', 'type', 'value', 'is_active']
                ]
            ]
        ]);


    $returnedIds = collect($res->json())->pluck('id')->sort()->values()->all();
    expect($returnedIds)->toEqualCanonicalizing([$d1->id, $d2->id]);
});

it('GET /api/share/recommendations ignores non-existent ids', function () {
    $params = makeParams(2);

    $d = Dish::factory()->create(['name' => 'Existing']);
    $d->parameters()->sync($params->pluck('id')->all());

    $nonExistingId = 999999;

    $res = $this->getJson("/api/share/recommendations?ids={$d->id},{$nonExistingId}");

    $res->assertOk()->assertJsonCount(1);
    expect($res->json('0.id'))->toBe($d->id);
});

it('GET /api/share/recommendations returns empty array when none match', function () {
    $res = $this->getJson('/api/share/recommendations?ids=123456,654321');

    $res->assertOk();
    expect($res->json())->toBeArray()->toBeEmpty();
});
