<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $dish_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Dish $dish
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Swipe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Swipe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Swipe query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Swipe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Swipe whereDishId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Swipe whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Swipe whereUserId($value)
 * @mixin \Eloquent
 */
class Swipe extends Model
{
    protected $fillable = ['dish_id', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }
}
