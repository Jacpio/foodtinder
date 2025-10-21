<?php

use App\Models\Dish;
use App\Models\Parameter;
use App\Models\ParameterWeight;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new RecommendationService();
    $this->user = User::factory()->create();

    $this->makeParam = function (string $name, string $type): Parameter {
        return Parameter::firstOrCreate(['name' => $name, 'type' => $type], ['value' => 1, 'is_active' => true]);
    };

    $this->attachDishParams = function (Dish $dish, array $params) {
        $dish->parameters()->syncWithoutDetaching(collect($params)->pluck('id'));
    };
});

it('returns only dishes with match_score > 0 and sorts by score', function () {
    $cat1 = ($this->makeParam)('Dania główne', 'category');
    $cui1 = ($this->makeParam)('Włoska', 'cuisine');
    $flv1 = ($this->makeParam)('Słony', 'flavour');

    $cat2 = ($this->makeParam)('Zupy', 'category');
    $cui2 = ($this->makeParam)('Polska', 'cuisine');
    $flv2 = ($this->makeParam)('Słodki', 'flavour');

    $dish1 = Dish::factory()->create(['name' => 'Dish 1']);
    ($this->attachDishParams)($dish1, [$cat1, $cui1, $flv1]);

    $dish2 = Dish::factory()->create(['name' => 'Dish 2']);
    ($this->attachDishParams)($dish2, [$cat2, $cui2, $flv2]);

    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $cat1->id, 'weight' => 2]);
    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $cui1->id, 'weight' => 3]);
    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $flv1->id, 'weight' => 1]);

    $recommended = $this->service->recommendedDishes($this->user);

    expect($recommended->pluck('id')->toArray())->toEqual([$dish1->id])
        ->and($recommended->first()->match_score)->toEqual(6.0);
});

it('calculates match_score correctly for single weight types', function () {
    $cat = ($this->makeParam)('Zupy', 'category');
    $cui = ($this->makeParam)('Włoska', 'cuisine');
    $flv = ($this->makeParam)('Słodki', 'flavour');

    $dishCategoryOnly = Dish::factory()->create(['name' => 'CategoryOnly']);
    ($this->attachDishParams)($dishCategoryOnly, [$cat]);

    $dishCuisineOnly = Dish::factory()->create(['name' => 'CuisineOnly']);
    ($this->attachDishParams)($dishCuisineOnly, [$cui]);

    $dishFlavourOnly = Dish::factory()->create(['name' => 'FlavourOnly']);
    ($this->attachDishParams)($dishFlavourOnly, [$flv]);

    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $cat->id, 'weight' => 5]);
    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $cui->id, 'weight' => 3]);
    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $flv->id, 'weight' => 1]);

    $recommended = $this->service->recommendedDishes($this->user);
    $scores = $recommended->pluck('match_score', 'name')->toArray();

    expect($scores['CategoryOnly'])->toEqual(5.0)
        ->and($scores['CuisineOnly'])->toEqual(3.0)
        ->and($scores['FlavourOnly'])->toEqual(1.0);
});

it('calculates match_score correctly for multiple weights', function () {
    $cat = ($this->makeParam)('Dania główne', 'category'); // 2
    $cui = ($this->makeParam)('Włoska', 'cuisine');        // 3
    $flv = ($this->makeParam)('Słony', 'flavour');         // 1

    $dish = Dish::factory()->create(['name' => 'Combo']);
    ($this->attachDishParams)($dish, [$cat, $cui, $flv]);

    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $cat->id, 'weight' => 2]);
    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $cui->id, 'weight' => 3]);
    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $flv->id, 'weight' => 1]);

    $recommended = $this->service->recommendedDishes($this->user);
    expect($recommended->first()->match_score)->toEqual(6.0);
});

it('returns empty if user has no weights', function () {
    $cat = ($this->makeParam)('Zupy', 'category');
    $cui = ($this->makeParam)('Polska', 'cuisine');
    $flv = ($this->makeParam)('Słony', 'flavour');

    $dish = Dish::factory()->create();
    ($this->attachDishParams)($dish, [$cat, $cui, $flv]);

    $recommended = $this->service->recommendedDishes($this->user);
    expect($recommended)->toBeEmpty();
});

it('sorts dishes by match_score descending', function () {
    $cat = ($this->makeParam)('Dania główne', 'category'); // 2
    $cui = ($this->makeParam)('Włoska', 'cuisine');        // 3
    $flv1 = ($this->makeParam)('Słony', 'flavour');        // 1
    $flv2 = ($this->makeParam)('Słodki', 'flavour');       // 0

    $dish1 = Dish::factory()->create(['name' => 'DishHigh']);
    ($this->attachDishParams)($dish1, [$cat, $cui, $flv1]);

    $dish2 = Dish::factory()->create(['name' => 'DishLow']);
    ($this->attachDishParams)($dish2, [$cat, $cui, $flv2]);

    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $cat->id, 'weight' => 2]);
    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $cui->id, 'weight' => 3]);
    ParameterWeight::create(['user_id' => $this->user->id, 'parameter_id' => $flv1->id, 'weight' => 1]);

    $recommended = $this->service->recommendedDishes($this->user);

    expect($recommended->pluck('name')->first())->toEqual('DishHigh')
        ->and($recommended->pluck('name')->last())->toEqual('DishLow');
});
