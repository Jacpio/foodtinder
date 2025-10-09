<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dish> $dishes
 * @property-read int|null $dishes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CategoryWeight> $weights
 * @property-read int|null $weights_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cuisine extends Model
{
    protected $fillable = ['name'];


    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    public function weights(): HasMany{
        return $this->hasMany(CategoryWeight::class);
    }
}
