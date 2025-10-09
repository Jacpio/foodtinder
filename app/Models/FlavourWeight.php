<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property int $flavour_id
 * @property float $weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Flavour $flavour
 * @property-read \App\Models\User $user
 * @method static Builder<static>|FlavourWeight newModelQuery()
 * @method static Builder<static>|FlavourWeight newQuery()
 * @method static Builder<static>|FlavourWeight query()
 * @method static Builder<static>|FlavourWeight whereCreatedAt($value)
 * @method static Builder<static>|FlavourWeight whereFlavourId($value)
 * @method static Builder<static>|FlavourWeight whereUpdatedAt($value)
 * @method static Builder<static>|FlavourWeight whereUserId($value)
 * @method static Builder<static>|FlavourWeight whereWeight($value)
 * @mixin \Eloquent
 */
class FlavourWeight extends Model
{
    protected $fillable = ['user_id', 'flavour_id', 'weight'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flavour(): BelongsTo
    {
        return $this->belongsTo(Flavour::class);
    }
}
