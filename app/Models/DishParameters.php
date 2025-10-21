<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $dish_id
 * @property int $parameter_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DishParameters newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DishParameters newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DishParameters query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DishParameters whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DishParameters whereDishId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DishParameters whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DishParameters whereParameterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DishParameters whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DishParameters extends Model
{
    //
}
