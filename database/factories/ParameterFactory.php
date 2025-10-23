<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parameter>
 */
class ParameterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement(['cuisine', 'category', 'flavour', 'other']),
            'value' => $this->faker->randomFloat(2, 1, 10),
            'is_active' => $this->faker->randomElement([true,false]),
        ];
    }
}
