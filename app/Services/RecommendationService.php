<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * @return LengthAwarePaginator;
     */
    public function recommendedDishes(User $user, int $perPage = 10): LengthAwarePaginator
    {
        $relevantTypes = ['category', 'cuisine', 'flavour', 'other'];

        $scores = DB::table('dish_parameters')
            ->join('parameters', 'parameters.id', '=', 'dish_parameters.parameter_id')
            ->leftJoin('parameter_weights', function ($join) use ($user) {
                $join->on('parameter_weights.parameter_id', '=', 'parameters.id')
                    ->where('parameter_weights.user_id', '=', $user->id);
            })
            ->whereIn('parameters.type', $relevantTypes)
            ->groupBy('dish_parameters.dish_id')
            ->select(
                'dish_parameters.dish_id',
                DB::raw('COALESCE(SUM(parameter_weights.weight), 0) AS match_score')
            );

        return Dish::query()
            ->joinSub($scores, 'scores', function ($join) {
                $join->on('scores.dish_id', '=', 'dishes.id');
            })
            ->where('scores.match_score', '>', 0)
            ->orderByDesc('scores.match_score')
            ->with(['parameters' => function ($q) use ($relevantTypes) {
                $q->select('parameters.id', 'name', 'type')
                    ->whereIn('type', $relevantTypes);
            }])
            ->select('dishes.*', 'scores.match_score')
            ->paginate($perPage);
    }
}
