<?php

namespace App\Services;

use App\Models\CategoryWeight;
use App\Models\CuisineWeight;
use App\Models\Dish;
use App\Models\FlavourWeight;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class SwipeService
{
    public function addWeightToCategory(int $categoryId, int $userId, string $decision): void
    {
        $categoryWeight = CategoryWeight::firstOrCreate(
            [
                'user_id' => $userId,
                'category_id' => $categoryId
            ],
            [
                'weight' => 0
            ]
        );
        if (!$categoryWeight->wasRecentlyCreated) {
            if ($decision === 'like') {
                $categoryWeight->increment('weight');
            }elseif ($decision === 'dislike') {
                $categoryWeight->decrement('weight');
            }
        }
    }

    public function addWeightToFlavour(int $flavourId, int $userId, string $decision): void
    {
        $flavourWeight = FlavourWeight::firstOrCreate(
            [
                'user_id' => $userId,
                'flavour_id' => $flavourId
            ],
            [
                'weight' => 0
            ]
        );
        if (!$flavourWeight->wasRecentlyCreated){
            if ($decision === 'like') {
                $flavourWeight->increment('weight');
            }else if ($decision === 'dislike') {
                $flavourWeight->decrement('weight');
            }
        }
    }

    public function addWeightToCuisine(int $cuisineId, int $userId, string $decision): void
    {
        $cuisineWeight = CuisineWeight::firstOrCreate(
            [
                'user_id' => $userId,
                'cuisine_id' => $cuisineId
            ],
            [
                'weight' => 0
            ]
        );
        if (!$cuisineWeight->wasRecentlyCreated){
            if ($decision === 'like'){
                $cuisineWeight->increment('weight');
            }else if ($decision === 'dislike'){
                $cuisineWeight->decrement('weight');
            }
        }
    }

    public function getUnswipedDishes(?User $user, int $limit): Collection
    {
        $swipedDishIds = $user->swipes()->pluck('dish_id');
        $availableDishesCount = Dish::whereNotIn('id', $swipedDishIds)->count();
        $limit = min($limit, $availableDishesCount);
        return Dish::with(['category', 'cuisine', 'flavour'])
            ->whereNotIn('id', $swipedDishIds)
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }
}
