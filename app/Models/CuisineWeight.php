<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $cuisine_id
 * @property float $weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cuisine $cuisine
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight whereCuisineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuisineWeight whereWeight($value)
 * @mixin \Eloquent
 */
class CuisineWeight extends Model
{
    protected $fillable = ['user_id', 'cuisine_id', 'weight'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cuisine(): BelongsTo
    {
        return $this->belongsTo(Cuisine::class);
    }
}
