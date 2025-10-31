<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    /** @use HasFactory<\Database\Factories\TypeFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function parameters(): HasMany
    {
        return $this->hasMany(Parameter::class, 'type_id');
    }
}
