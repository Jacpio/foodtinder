<?php

use App\Models\Category;
use App\Models\Cuisine;
use App\Models\Dish;
use App\Models\Flavour;
use App\Models\Swipe;
use App\Models\User;
use App\Models\CategoryWeight;
use App\Models\FlavourWeight;
use App\Models\CuisineWeight;
use App\Services\SwipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new SwipeService();
    $this->user = User::factory()->create();

    $this->category = Category::factory()->create();
    $this->flavour = Flavour::factory()->create();
    $this->cuisine = Cuisine::factory()->create();
});

it('creates a new CategoryWeight with 0 if not exists', function () {
    $this->service->addWeightToCategory($this->category->id, $this->user->id, 'like');

    $weight = CategoryWeight::first();
    expect($weight)->not->toBeNull()
        ->and($weight->weight)->toEqual(0);
});

it('increments CategoryWeight when decision is like', function () {
    $categoryId = 1;
    $weight = CategoryWeight::create([
        'user_id' => $this->user->id,
        'category_id' => $categoryId,
        'weight' => 2
    ]);

    $this->service->addWeightToCategory($categoryId, $this->user->id, 'like');

    $weight->refresh();
    expect($weight->weight)->toEqual(3);
});

it('decrements CategoryWeight when decision is dislike', function () {
    $categoryId = 1;
    $weight = CategoryWeight::create([
        'user_id' => $this->user->id,
        'category_id' => $categoryId,
        'weight' => 2
    ]);

    $this->service->addWeightToCategory($categoryId, $this->user->id, 'dislike');

    $weight->refresh();
    expect($weight->weight)->toEqual(1);
});

it('increments FlavourWeight when decision is like', function () {
    $flavourId = 1;
    $weight = FlavourWeight::create([
        'user_id' => $this->user->id,
        'flavour_id' => $flavourId,
        'weight' => 1
    ]);

    $this->service->addWeightToFlavour($flavourId, $this->user->id, 'like');

    $weight->refresh();
    expect($weight->weight)->toEqual(2);
});

it('decrements FlavourWeight when decision is dislike', function () {
    $flavourId = 1;
    $weight = FlavourWeight::create([
        'user_id' => $this->user->id,
        'flavour_id' => $flavourId,
        'weight' => 1
    ]);

    $this->service->addWeightToFlavour($flavourId, $this->user->id, 'dislike');

    $weight->refresh();
    expect($weight->weight)->toEqual(0);
});

it('increments CuisineWeight when decision is like', function () {
    $cuisineId = 1;
    $weight = CuisineWeight::create([
        'user_id' => $this->user->id,
        'cuisine_id' => $cuisineId,
        'weight' => 5
    ]);

    $this->service->addWeightToCuisine($cuisineId, $this->user->id, 'like');

    $weight->refresh();
    expect($weight->weight)->toEqual(6);
});

it('decrements CuisineWeight when decision is dislike', function () {
    $cuisineId = 1;
    $weight = CuisineWeight::create([
        'user_id' => $this->user->id,
        'cuisine_id' => $cuisineId,
        'weight' => 5
    ]);

    $this->service->addWeightToCuisine($cuisineId, $this->user->id, 'dislike');

    $weight->refresh();
    expect($weight->weight)->toEqual(4);
});

it('returns only unswiped dishes', function () {
    $dishes = Dish::factory()->count(5)->create();
    foreach ($dishes->take(2) as $dish) {
        Swipe::create([
            'user_id' => $this->user->id,
            'dish_id' => $dish->id,
        ]);
    }

    $unswiped = $this->service->getUnswipedDishes($this->user, 5);

    $swipedIds = $dishes->take(2)->pluck('id')->toArray();
    foreach ($unswiped as $dish) {
        expect(in_array($dish->id, $swipedIds))->toBeFalse();
    }

    expect($unswiped->count())->toBe(3);
});

it('respects the limit when fetching unswiped dishes', function () {
    Dish::factory()->count(10)->create();

    $unswiped = $this->service->getUnswipedDishes($this->user, 4);

    expect($unswiped->count())->toBe(4);
});
