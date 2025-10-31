<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $type_id
 * @property float $value
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dish> $dishes
 * @property-read int|null $dishes_count
 * @property \App\Models\Type $type
 * @property-read \App\Models\Type $typeRelation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ParameterWeight> $weights
 * @property-read int|null $weights_count
 * @method static Builder<static>|Parameter active()
 * @method static \Database\Factories\ParameterFactory factory($count = null, $state = [])
 * @method static Builder<static>|Parameter newModelQuery()
 * @method static Builder<static>|Parameter newQuery()
 * @method static Builder<static>|Parameter query()
 * @method static Builder<static>|Parameter whereCreatedAt($value)
 * @method static Builder<static>|Parameter whereId($value)
 * @method static Builder<static>|Parameter whereIsActive($value)
 * @method static Builder<static>|Parameter whereName($value)
 * @method static Builder<static>|Parameter whereTypeId($value)
 * @method static Builder<static>|Parameter whereUpdatedAt($value)
 * @method static Builder<static>|Parameter whereValue($value)
 * @mixin \Eloquent
 */
class Parameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'value', 'is_active', 'type', 'type_id',
    ];

    protected $casts = [
        'value' => 'float',
        'is_active' => 'boolean',
    ];

    protected $appends = ['type'];

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'dish_parameters')->withTimestamps();
    }

    public function weights(): HasMany
    {
        return $this->hasMany(ParameterWeight::class);
    }

    public function typeRelation(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function setTypeAttribute($value): void
    {
        if (is_numeric($value)) {
            $this->attributes['type_id'] = (int) $value;
            return;
        }

        if (is_string($value) && $value !== '') {
            $this->attributes['type'] = $value;

            $type = Type::firstOrCreate(['name' => $value]);
            $this->attributes['type_id'] = $type->id;
        }
    }

    public function getTypeAttribute(): ?string
    {
        if (array_key_exists('type', $this->attributes) && $this->attributes['type'] !== null) {
            return $this->attributes['type'];
        }

        if ($this->relationLoaded('typeRelation') && $this->getRelation('typeRelation')) {
            return $this->getRelation('typeRelation')->name;
        }

        if ($this->type_id) {
            return Type::whereKey($this->type_id)->value('name');
        }

        return null;
    }
}
