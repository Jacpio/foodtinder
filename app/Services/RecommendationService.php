<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\ParameterWeight;
use App\Models\User;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     *
     * @return \Illuminate\Support\Collection  // kolekcja Dish z atrybutem -> match_score
     */
    public function recommendedDishes(User $user, ?int $limit = null): Collection
    {
        $weights = ParameterWeight::where('user_id', $user->id)
            ->pluck('weight', 'parameter_id');

        $relevantTypes = ['category', 'cuisine', 'flavour', 'other'];

        $dishes = Dish::with(['parameters' => function ($q) use ($relevantTypes) {
            $q->select('parameters.id', 'name', 'type')
            ->whereIn('type', $relevantTypes);
        }])
            ->get();

        $scored = $dishes->map(function (Dish $dish) use ($weights): Dish {
            $score = 0.0;

            foreach ($dish->parameters as $param) {
                $pid = $param->id;
                $score += (float) ($weights[$pid] ?? 0.0);
            }

            $dish->setAttribute('match_score', $score);

            return $dish;
        })
            ->filter(fn (Dish $d) => ($d->match_score ?? 0) > 0)
            ->sortByDesc('match_score')
            ->values();

        return $limit ? $scored->take($limit)->values() : $scored;
    }
}
