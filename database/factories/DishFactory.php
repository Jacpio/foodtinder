<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Cuisine;
use App\Models\Flavour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'image_url' => $this->faker->uuid() . '.jpg',
            'category_id' => Category::factory(),
            'cuisine_id' => Cuisine::factory(),
            'flavour_id' => Flavour::factory(),
        ];
    }
}
