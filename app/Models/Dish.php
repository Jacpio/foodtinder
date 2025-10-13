<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property int $cuisine_id
 * @property string|null $image_url
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\Cuisine $cuisine
 * @method static Builder<static>|Dish newModelQuery()
 * @method static Builder<static>|Dish newQuery()
 * @method static Builder<static>|Dish query()
 * @method static Builder<static>|Dish whereCategoryId($value)
 * @method static Builder<static>|Dish whereCreatedAt($value)
 * @method static Builder<static>|Dish whereCuisineId($value)
 * @method static Builder<static>|Dish whereDescription($value)
 * @method static Builder<static>|Dish whereId($value)
 * @method static Builder<static>|Dish whereImageUrl($value)
 * @method static Builder<static>|Dish whereName($value)
 * @method static Builder<static>|Dish whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Dish extends Model
{
    protected $appends = ['image_url_full'];
    protected $fillable = [
        'name',
        'category_id',
        'cuisine_id',
        'image_url',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlFullAttribute(): string
    {
        if (!$this->image_url) {
            return asset('default.jpg');
        }

        return asset(  Storage::url( $this->image_url));
    }

    public function cuisine(): BelongsTo
    {
        return $this->belongsTo(Cuisine::class);
    }
    public function swipes(): HasMany{
        return $this->hasMany(Swipe::class);
    }
    public function flavour(): BelongsTo{
        return $this->belongsTo(Flavour::class);
    }
}
