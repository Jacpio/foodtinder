<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\ParameterWeight;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SwipeService
{
    /**
     * @param  'like'|'dislike'  $decision
     */
    public function swipe(User $user, Dish $dish, string $decision): void
    {
        $decision = strtolower($decision);
        if (!in_array($decision, ['like', 'dislike'], true)) {
            throw new InvalidArgumentException('decision must be "like" or "dislike"');
        }

        DB::transaction(function () use ($user, $dish, $decision) {
            if (method_exists($user, 'likedDishes')) {
                $rel = $user->likedDishes();
                if ($rel instanceof BelongsToMany) {
                    $rel->syncWithoutDetaching([$dish->id]);
                }
            }

            $parameterIds = $dish->parameters()->pluck('parameters.id');

            foreach ($parameterIds as $pid) {
                $pw = ParameterWeight::firstOrNew([
                    'user_id'      => $user->id,
                    'parameter_id' => $pid,
                ]);

                $current = (float) ($pw->weight ?? 0.0);
                $delta   = $decision === 'like' ? 1.0 : -1.0;

                $pw->weight = $this->clamp($current + $delta, 0.0, 20.0);
                $pw->save();
            }
        });
    }

    public function getUnswipedDishes(User $user, int $limit): Collection
    {
        $swipedDishIds = collect();
        if (method_exists($user, 'likedDishes')) {
            $rel = $user->likedDishes();
            if ($rel instanceof BelongsToMany) {
                $swipedDishIds = $rel->pluck('dishes.id');
            }
        }

        $base = Dish::query()
            ->with('parameters')
            ->when($swipedDishIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $swipedDishIds));

        if ($user->vegan ?? false) {
            $base->where('is_vegan', true);
        }

        $available = (clone $base)->count();
        $limit = max(0, min($limit, $available));

        return $base->inRandomOrder()
            ->take($limit)
            ->get();
    }

    public function getUnswipedDishesByParameter(?User $user, int $limit, int $parameterId): Collection
    {
        $swipedDishIds = collect();
        if ($user && method_exists($user, 'likedDishes')) {
            $rel = $user->likedDishes();
            if ($rel instanceof BelongsToMany) {
                $swipedDishIds = $rel->pluck('dishes.id');
            }
        }

        $base = Dish::query()
            ->with('parameters')
            ->when($swipedDishIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $swipedDishIds))
            ->whereHas('parameters', fn ($q) => $q->where('parameters.id', $parameterId));

        if (($user->vegan ?? false) === true) {
            $base->where('is_vegan', true);
        }

        $available = (clone $base)->count();
        $limit = max(0, min($limit, $available));

        return $base->inRandomOrder()->take($limit)->get();
    }

    private function clamp(float $v, float $min, float $max): float
    {
        return max($min, min($max, $v));
    }
}
