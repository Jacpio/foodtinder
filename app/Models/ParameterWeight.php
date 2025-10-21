<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $parameter_id
 * @property int $user_id
 * @property float $weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Parameter $parameter
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight whereParameterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParameterWeight whereWeight($value)
 * @mixin \Eloquent
 */
class ParameterWeight extends Model
{
    use HasFactory;

    protected $table = 'parameter_weights';

    protected $fillable = [
        'parameter_id', 'user_id', 'weight',
    ];

    protected $casts = [
        'weight' => 'float',
    ];

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(Parameter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
