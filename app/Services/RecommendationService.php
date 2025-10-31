<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     *
     * @param User       $user
     * @param int        $limit
     * @param array|null $types
     * @param bool       $onlyActive
     * @param bool       $includeZero
     * @param bool       $onlyWithUnswipedDishes
     * @return Collection
     */
    public function getUserTopParameters(
        User $user,
        int $limit = 10,
        ?array $types = null,
        bool $onlyActive = true,
        bool $includeZero = false,
        bool $onlyWithUnswipedDishes = true
    ): Collection {
        $typeIds = $this->resolveTypeIds($types);
        if (empty($typeIds)) {
            return collect();
        }

        $pw = DB::table('parameter_weights as pw')
            ->select('pw.parameter_id', 'pw.weight')
            ->where('pw.user_id', $user->id);

        $dc = DB::table('dish_parameters as dp')
            ->select('dp.parameter_id', DB::raw('COUNT(DISTINCT dp.dish_id) AS dishes_count'))
            ->groupBy('dp.parameter_id');

        $sw = DB::table('swipes as s')
            ->select('s.dish_id')
            ->where('s.user_id', $user->id)
            ->distinct();

        $unswiped = DB::table('dish_parameters as dp2')
            ->leftJoinSub($sw, 'sw', 'sw.dish_id', '=', 'dp2.dish_id')
            ->whereNull('sw.dish_id')
            ->select('dp2.parameter_id', DB::raw('COUNT(DISTINCT dp2.dish_id) AS unswiped_dishes_count'))
            ->groupBy('dp2.parameter_id');

        $query = DB::table('parameters as p')
            ->join('types as t', 't.id', '=', 'p.type_id')
            ->leftJoinSub($pw, 'pw', 'pw.parameter_id', '=', 'p.id')
            ->leftJoinSub($dc, 'dc', 'dc.parameter_id', '=', 'p.id')
            ->leftJoinSub($unswiped, 'udc', 'udc.parameter_id', '=', 'p.id')
            ->whereIn('p.type_id', $typeIds);

        if ($onlyActive) {
            $query->where('p.is_active', 1);
        }

        if (!$includeZero) {
            $query->whereRaw('COALESCE(pw.weight, 0) > 0');
        }

        if ($onlyWithUnswipedDishes) {
            $query->whereRaw('COALESCE(udc.unswiped_dishes_count, 0) > 0');
        }

        return $query
            ->select(
                'p.id',
                'p.name',
                'p.type_id',
                't.name as type',
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

    public function recommendedDishes(User $user, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $relevantTypes = ['category', 'cuisine', 'flavour', 'other'];

        $scores = \DB::table('dish_parameters as dp')
            ->join('parameters as p', 'p.id', '=', 'dp.parameter_id')
            ->join('types as t', 't.id', '=', 'p.type_id')
            ->leftJoin('parameter_weights as pw', function ($join) use ($user) {
                $join->on('pw.parameter_id', '=', 'p.id')
                    ->where('pw.user_id', '=', $user->id);
            })
            ->whereIn('t.name', $relevantTypes)
            ->groupBy('dp.dish_id')
            ->select('dp.dish_id', \DB::raw('COALESCE(SUM(pw.weight), 0) AS match_score'));

        return Dish::query()
            ->joinSub($scores, 'scores', fn($join) => $join->on('scores.dish_id', '=', 'dishes.id'))
            ->where('scores.match_score', '>', 0)
            ->when($user->is_vegan ?? false, fn($q) => $q->where('dishes.is_vegan', true))
            ->orderByDesc('scores.match_score')
            ->with(['parameters.type'])
            ->select('dishes.*', 'scores.match_score')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param array<int,string|int>|null $types
     * @return array<int,int>
     */
    private function resolveTypeIds(?array $types): array
    {
        if (empty($types)) {
            return DB::table('types')->pluck('id')->all();
        }

        $ids   = [];
        $names = [];

        foreach ($types as $t) {
            if (is_numeric($t)) {
                $ids[] = (int) $t;
            } elseif (is_string($t) && $t !== '') {
                $names[] = $t;
            }
        }

        $idsFromNames = [];
        if (!empty($names)) {
            $idsFromNames = DB::table('types')
                ->whereIn('name', $names)
                ->pluck('id')
                ->all();
        }

        return array_values(array_unique(array_map('intval', array_merge($ids, $idsFromNames))));
    }
}
