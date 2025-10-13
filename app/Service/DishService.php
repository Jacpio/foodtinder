<?php

namespace App\Service;

use App\Models\CategoryWeight;
use App\Models\CuisineWeight;
use App\Models\Dish;
use App\Models\FlavourWeight;
use App\Models\User;

class DishService
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

    public function recommendedDishes(User $user)
    {
        return Dish::with(['category', 'cuisine', 'flavour'])
            ->get()
            ->map(function ($dish) use ($user) {
                $categoryWeight = CategoryWeight::where('user_id', $user->id)
                    ->where('category_id', $dish->category_id)
                    ->value('weight') ?? 0;

                $cuisineWeight = CuisineWeight::where('user_id', $user->id)
                    ->where('cuisine_id', $dish->cuisine_id)
                    ->value('weight') ?? 0;

                $flavourWeight = 0;
                if ($dish->flavour) {
                    $flavourWeight = FlavourWeight::where('user_id', $user->id)
                        ->where('flavour_id', $dish->flavour->id)
                        ->value('weight') ?? 0;
                }

                $dish->match_score = $categoryWeight + $cuisineWeight + $flavourWeight;

                return $dish;
            })
            ->filter(fn($dish) => $dish->match_score > 0)
            ->sortByDesc('match_score')
            ->values();
    }
}
