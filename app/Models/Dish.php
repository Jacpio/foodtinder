<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property string|null $image_url
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $image_url_full
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $likedByUsers
 * @property-read int|null $liked_by_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Parameter> $parameters
 * @property-read int|null $parameters_count
 * @method static \Database\Factories\DishFactory factory($count = null, $state = [])
 * @method static Builder<static>|Dish newModelQuery()
 * @method static Builder<static>|Dish newQuery()
 * @method static Builder<static>|Dish query()
 * @method static Builder<static>|Dish whereCreatedAt($value)
 * @method static Builder<static>|Dish whereDescription($value)
 * @method static Builder<static>|Dish whereId($value)
 * @method static Builder<static>|Dish whereImageUrl($value)
 * @method static Builder<static>|Dish whereName($value)
 * @method static Builder<static>|Dish whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Dish extends Model
{
    use HasFactory;
    protected $appends = ['image_url_full'];
    protected $fillable = [
        'name',
        'image_url',
        'description',
    ];

    public function parameters(): BelongsToMany
    {
        return $this->BelongsToMany(Parameter::class, 'dish_parameters')->withTimestamps();
    }

    public function getImageUrlFullAttribute(): string
    {
        if (!$this->image_url) {
            return asset('default.jpg');
        }

        return asset(  Storage::url( $this->image_url));
    }
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'swipes')
            ->withTimestamps();
    }
}
