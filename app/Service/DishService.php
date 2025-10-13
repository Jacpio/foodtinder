<?php

namespace App\Service;

use App\Models\CategoryWeight;
use App\Models\CuisineWeight;
use App\Models\FlavourWeight;

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
}
