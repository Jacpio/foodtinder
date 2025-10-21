<?php

use App\Models\Dish;
use App\Models\Parameter;
use App\Models\ParameterWeight;
use App\Models\User;
use App\Services\SwipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new SwipeService();
    $this->user = User::factory()->create();

    $this->makeParam = function (string $name, string $type): Parameter {
        return Parameter::firstOrCreate(['name' => $name, 'type' => $type], ['value' => 1, 'is_active' => true]);
    };

    $this->attachDishParams = function (Dish $dish, array $params) {
        $dish->parameters()->syncWithoutDetaching(collect($params)->pluck('id'));
    };
});

it('swipe like creates/bumps ParameterWeight for all dish parameters', function () {
    $cat = ($this->makeParam)('Zupy', 'category');
    $cui = ($this->makeParam)('Polska', 'cuisine');
    $flv = ($this->makeParam)('Słony', 'flavour');

    $dish = Dish::factory()->create(['name' => 'TEST']);
    ($this->attachDishParams)($dish, [$cat, $cui, $flv]);

    $this->service->swipe($this->user, $dish, 'like');

    foreach ([$cat, $cui, $flv] as $p) {
        $w = ParameterWeight::where('user_id', $this->user->id)->where('parameter_id', $p->id)->first();
        expect($w)->not->toBeNull()
            ->and($w->weight)->toEqual(1.0);
    }
});

it('swipe dislike clamps weights at 0', function () {
    $cat = ($this->makeParam)('Dania główne', 'category');
    $dish = Dish::factory()->create(['name' => 'D2']);
    ($this->attachDishParams)($dish, [$cat]);

    $this->service->swipe($this->user, $dish, 'dislike');

    $w = ParameterWeight::where('user_id', $this->user->id)->where('parameter_id', $cat->id)->first();
    expect($w)->not->toBeNull()
        ->and($w->weight)->toEqual(0.0);

    $this->service->swipe($this->user, $dish, 'like');
    $w->refresh();
    expect($w->weight)->toEqual(1.0);
});

it('getUnswipedDishes returns only dishes user did not swipe', function () {
    $p1 = ($this->makeParam)('Zupy', 'category');
    $p2 = ($this->makeParam)('Dania główne', 'category');
    $p3 = ($this->makeParam)('Desery', 'category');
    $p4 = ($this->makeParam)('Przekąski', 'category');
    $p5 = ($this->makeParam)('Włoska', 'cuisine');

    $dishes = Dish::factory()->count(5)->sequence(
        ['name' => 'A'], ['name' => 'B'], ['name' => 'C'], ['name' => 'D'], ['name' => 'E']
    )->create();

    $params = [$p1,$p2,$p3,$p4,$p5];
    foreach ($dishes as $i => $dish) {
        ($this->attachDishParams)($dish, [$params[$i]]);
    }

    $this->user->likedDishes()->attach([$dishes[0]->id, $dishes[1]->id]);

    $unswiped = $this->service->getUnswipedDishes($this->user, 5);

    $swipedIds = [$dishes[0]->id, $dishes[1]->id];
    expect($unswiped->count())->toBe(3);
    foreach ($unswiped as $dish) {
        expect(in_array($dish->id, $swipedIds))->toBeFalse();
    }
});

it('getUnswipedDishes respects limit', function () {
    $p = ($this->makeParam)('Polska', 'cuisine');

    $dishes = Dish::factory()->count(10)->create();
    foreach ($dishes as $d) {
        ($this->attachDishParams)($d, [$p]);
    }

    $unswiped = $this->service->getUnswipedDishes($this->user, 4);
    expect($unswiped->count())->toBe(4);
});
