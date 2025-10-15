<?php

namespace App\Services;

use App\Models\CategoryWeight;
use App\Models\CuisineWeight;
use App\Models\Dish;
use App\Models\FlavourWeight;
use App\Models\User;

class RecommendationService
{
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
