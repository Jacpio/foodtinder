<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dish> $dishes
 * @property-read int|null $dishes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FlavourWeight> $weights
 * @property-read int|null $weights_count
 * @method static \Database\Factories\FlavourFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flavour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flavour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flavour query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flavour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flavour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flavour whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flavour whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Flavour extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function weights(): HasMany
    {
        return $this->hasMany(FlavourWeight::class);
    }
    public function dishes(): hasMany{
        return $this->hasMany(Dish::class);
    }
}
