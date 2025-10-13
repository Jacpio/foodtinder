<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property int $category_id
 * @property float $weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryWeight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryWeight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryWeight query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryWeight whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryWeight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryWeight whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryWeight whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryWeight whereWeight($value)
 * @mixin \Eloquent
 */
class CategoryWeight extends Model
{
    protected $fillable = ['user_id', 'category_id', 'weight'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
