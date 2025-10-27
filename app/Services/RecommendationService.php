<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * @param User $user
     * @param int $limit
     * @param array|null $types
     * @param bool $onlyActive
     * @param bool $includeZero
     * @return \Illuminate\Support\Collection
     */
    public function getUserTopParameters(
        User $user,
        int $limit = 10,
        ?array $types = ['category','cuisine','flavour','other'],
        bool $onlyActive = true,
        bool $includeZero = false,
        bool $onlyWithUnswipedDishes = true
    ): \Illuminate\Support\Collection {
        $allowed = ['category','cuisine','flavour','other'];
        $types   = $types ? array_values(array_intersect($types, $allowed)) : $allowed;

        $pw = DB::table('parameter_weights')
            ->select('parameter_id', 'weight')
            ->where('user_id', $user->id);

        $dc = DB::table('dish_parameters')
            ->select('parameter_id', DB::raw('COUNT(DISTINCT dish_id) AS dishes_count'))
            ->groupBy('parameter_id');

        $sw = DB::table('swipes')
            ->select('dish_id')
            ->where('user_id', $user->id)
            ->distinct();

        $unswiped = DB::table('dish_parameters as dp')
            ->leftJoinSub($sw, 'sw', 'sw.dish_id', '=', 'dp.dish_id')
            ->whereNull('sw.dish_id')
            ->select('dp.parameter_id', DB::raw('COUNT(DISTINCT dp.dish_id) AS unswiped_dishes_count'))
            ->groupBy('dp.parameter_id');

        $query = DB::table('parameters as p')
            ->leftJoinSub($pw, 'pw', 'pw.parameter_id', '=', 'p.id')
            ->leftJoinSub($dc, 'dc', 'dc.parameter_id', '=', 'p.id')
            ->leftJoinSub($unswiped, 'udc', 'udc.parameter_id', '=', 'p.id')
            ->whereIn('p.type', $types);

        if ($onlyActive) {
            $query->where('p.is_active', 1);
        }

        if (!$includeZero) {
            $query->whereRaw('COALESCE(pw.weight, 0) > 0');
        }

        if ($onlyWithUnswipedDishes) {
            $query->whereRaw('COALESCE(udc.unswiped_dishes_count, 0) > 0');
        }

        return $query->select(
            'p.id',
            'p.name',
            'p.type',
            DB::raw('COALESCE(pw.weight, 0) AS weight'),
            DB::raw('COALESCE(dc.dishes_count, 0) AS dishes_count'),
            DB::raw('COALESCE(udc.unswiped_dishes_count, 0) AS unswiped_dishes_count'),
            DB::raw('(CASE WHEN COALESCE(udc.unswiped_dishes_count, 0) > 0 THEN 1 ELSE 0 END) AS has_unswiped_dishes')
        )
            ->orderByDesc('weight')
            ->orderByDesc('dishes_count')
            ->orderBy('p.name')
            ->limit($limit)
            ->get();
    }

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
            ->when($user->vegan, fn ($q) => $q->where('dishes.is_vegan', true))
            ->orderByDesc('scores.match_score')
            ->with(['parameters' => function ($q) use ($relevantTypes) {
                $q->select('parameters.id', 'name', 'type')
                    ->whereIn('type', $relevantTypes);
            }])
            ->select('dishes.*', 'scores.match_score')
            ->paginate($perPage);
    }
}
