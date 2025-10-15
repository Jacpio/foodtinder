<?php

use App\Models\Dish;
use App\Models\User;
use App\Models\Category;
use App\Models\Cuisine;
use App\Models\Flavour;
use App\Models\CategoryWeight;
use App\Models\CuisineWeight;
use App\Models\FlavourWeight;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new RecommendationService();
    $this->user = User::factory()->create();

    $this->category1 = Category::factory()->create();
    $this->category2 = Category::factory()->create();
    $this->cuisine1 = Cuisine::factory()->create();
    $this->cuisine2 = Cuisine::factory()->create();
    $this->flavour1 = Flavour::factory()->create();
    $this->flavour2 = Flavour::factory()->create();
});

it('returns only dishes with match_score > 0 and sorts by score', function () {
    $dish1 = Dish::factory()->create([
        'category_id' => $this->category1->id,
        'cuisine_id' => $this->cuisine1->id,
        'flavour_id' => $this->flavour1->id,
    ]);
    $dish2 = Dish::factory()->create([
        'category_id' => $this->category2->id,
        'cuisine_id' => $this->cuisine2->id,
        'flavour_id' => $this->flavour2->id,
    ]);

    CategoryWeight::create([
        'user_id' => $this->user->id,
        'category_id' => $this->category1->id,
        'weight' => 2,
    ]);
    CuisineWeight::create([
        'user_id' => $this->user->id,
        'cuisine_id' => $this->cuisine1->id,
        'weight' => 3,
    ]);
    FlavourWeight::create([
        'user_id' => $this->user->id,
        'flavour_id' => $this->flavour1->id,
        'weight' => 1,
    ]);

    $recommended = $this->service->recommendedDishes($this->user);

    expect($recommended->pluck('id')->toArray())->toEqual([
        $dish1->id,
    ])
        ->and($recommended->first()->match_score)->toEqual(6);
});

it('calculates match_score correctly for single weight types', function () {
    $dishCategoryOnly = Dish::factory()->create([
        'category_id' => $this->category1->id,
        'cuisine_id' => $this->cuisine2->id,
        'flavour_id' => $this->flavour2->id,
    ]);

    $dishCuisineOnly = Dish::factory()->create([
        'category_id' => $this->category2->id,
        'cuisine_id' => $this->cuisine1->id,
        'flavour_id' => $this->flavour2->id,
    ]);

    $dishFlavourOnly = Dish::factory()->create([
        'category_id' => $this->category2->id,
        'cuisine_id' => $this->cuisine2->id,
        'flavour_id' => $this->flavour1->id,
    ]);

    CategoryWeight::create([
        'user_id' => $this->user->id,
        'category_id' => $this->category1->id,
        'weight' => 5,
    ]);
    CuisineWeight::create([
        'user_id' => $this->user->id,
        'cuisine_id' => $this->cuisine1->id,
        'weight' => 3,
    ]);
    FlavourWeight::create([
        'user_id' => $this->user->id,
        'flavour_id' => $this->flavour1->id,
        'weight' => 1,
    ]);

    $recommended = $this->service->recommendedDishes($this->user);

    $scores = $recommended->pluck('match_score', 'id')->toArray();

    expect($scores[$dishCategoryOnly->id])->toEqual(5)
        ->and($scores[$dishCuisineOnly->id])->toEqual(3)
        ->and($scores[$dishFlavourOnly->id])->toEqual(1);
});


it('calculates match_score correctly for multiple weights', function () {
    $dish = Dish::factory()->create([
        'category_id' => $this->category1->id,
        'cuisine_id' => $this->cuisine1->id,
        'flavour_id' => $this->flavour1->id,
    ]);

    CategoryWeight::create(['user_id' => $this->user->id, 'category_id' => $this->category1->id, 'weight' => 2]);
    CuisineWeight::create(['user_id' => $this->user->id, 'cuisine_id' => $this->cuisine1->id, 'weight' => 3]);
    FlavourWeight::create(['user_id' => $this->user->id, 'flavour_id' => $this->flavour1->id, 'weight' => 1]);

    $recommended = $this->service->recommendedDishes($this->user);
    expect($recommended->first()->match_score)->toEqual(6);
});

it('returns empty if user has no weights', function () {
    $dish = Dish::factory()->create([
        'category_id' => $this->category1->id,
        'cuisine_id' => $this->cuisine1->id,
        'flavour_id' => $this->flavour1->id,
    ]);

    $recommended = $this->service->recommendedDishes($this->user);
    expect($recommended)->toBeEmpty();
});


it('sorts dishes by match_score descending', function () {
    $dish1 = Dish::factory()->create([
        'category_id' => $this->category1->id,
        'cuisine_id' => $this->cuisine1->id,
        'flavour_id' => $this->flavour1->id,
    ]);
    $dish2 = Dish::factory()->create([
        'category_id' => $this->category1->id,
        'cuisine_id' => $this->cuisine1->id,
        'flavour_id' => $this->flavour2->id,
    ]);

    CategoryWeight::create(['user_id' => $this->user->id, 'category_id' => $this->category1->id, 'weight' => 2]);
    CuisineWeight::create(['user_id' => $this->user->id, 'cuisine_id' => $this->cuisine1->id, 'weight' => 3]);
    FlavourWeight::create(['user_id' => $this->user->id, 'flavour_id' => $this->flavour1->id, 'weight' => 1]);

    $recommended = $this->service->recommendedDishes($this->user);

    expect($recommended->pluck('id')->first())->toEqual($dish1->id)
        ->and($recommended->pluck('id')->last())->toEqual($dish2->id);
});
